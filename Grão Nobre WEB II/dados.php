<?php

session_start();

if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = array();
}

if (isset($_GET["cafe"]) && isset($_GET["qtd"])) {
    $cafe = $_GET["cafe"];
    $qtd = intval($_GET["qtd"]);

    $cafeExplode = explode("*", $cafe);
    $nomeCafe = $cafeExplode[0];
    $valorCafe = floatval($cafeExplode[1]);
    $subTotal = ($valorCafe * $qtd);

    $item = [
        "nome" => $nomeCafe,
        "valor" => $valorCafe,
        "quantidade" => $qtd, 
        "subtotal" => $subTotal
    ];
    array_push($_SESSION["carrinho"], $item);
}

if (isset($_GET["comida"]) && isset($_GET["qtde"])) {
    $comida = $_GET["comida"];
    $qtde = intval($_GET["qtde"]);

    $comidaExplode = explode("*", $comida);
    $nomeComida = $comidaExplode[0];
    $valorComida = floatval($comidaExplode[1]);
    $subTotal2 = ($valorComida * $qtde);

    $item2 = [
        "nome" => $nomeComida,
        "valor" => $valorComida,
        "quantidade" => $qtde, 
        "subtotal" => $subTotal2
    ];
    array_push($_SESSION["carrinho"], $item2);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["acao"]) && $_POST["acao"] === "remover" && isset($_POST["remove"])) {
        $remover = intval($_POST["remove"]); 
        if (isset($_SESSION["carrinho"][$remover])) {
            array_splice($_SESSION["carrinho"], $remover, 1);
        }
        header("Location: dados.php");
        exit();
    }

    if (isset($_POST["acao"]) && $_POST["acao"] === "atualizar_quantidade" && isset($_POST["remove"]) && isset($_POST["atualizado"])) {
        $remover = intval($_POST["remove"]); 
        $novaQuantidade = intval($_POST["atualizado"]);

        if (isset($_SESSION["carrinho"][$remover]) && $novaQuantidade >= 1) {
            $novoItem = &$_SESSION["carrinho"][$remover]; 
            $valorUnitario = $novoItem["valor"];
            $novoSubtotal = $valorUnitario * $novaQuantidade;

            $novoItem["quantidade"] = $novaQuantidade;
            $novoItem["subtotal"] = $novoSubtotal;
        }

        header("Location: dados.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de compras</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h3> Carrinho de compras </h3>
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Produto</th>
                <th>Valor Un.</th>
                <th>Quant.</th>
                <th>Subtotal</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $num = 1;
                foreach($_SESSION["carrinho"] as $remove => $item) :
            ?>
                <tr>
                    <td> <?= $num ?> </td>
                    <td> <?= $item["nome"] ?> </td>
                    <td> <?= number_format($item["valor"], 2, ',', '.') ?> </td>
                    <td> <?= $item["quantidade"] ?> </td>
                    <td> <?= number_format($item["subtotal"], 2, ',', '.') ?> </td>
                    <td>
                        <form action="dados.php" method="post" class="form-inline">
                            <input type="hidden" name="acao" value="atualizar_quantidade">
                            <input type="hidden" name="remove" value="<?= $remove ?>">
                            <input
                                type="number"
                                name="atualizado"
                                value="<?= $item['quantidade'] ?>"
                                min="1"
                                class="input-quantidade"
                                required
                            >
                            <button type="submit" class="btn-acao btn-atualizar">Atualizar</button>
                        </form>
                        
                        <form action="dados.php" method="post" class="form-inline">
                            <input type="hidden" name="acao" value="remover">
                            <input type="hidden" name="remove" value="<?= $remove ?>">
                            <button type="submit" class="btn-acao btn-remover">Remover</button>
                        </form>
                    </td>
                </tr>
            <?php 
                    $num++;
                endforeach; 
            ?>
        </tbody>
    </table>

    <br>
    
    <button onclick="window.location.href='index.php'">Continuar comprando</button>
    <button onclick="window.location.href='cancelar.php'">Cancelar compra</button>

</body>
</html>