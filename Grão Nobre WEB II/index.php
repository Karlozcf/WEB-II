<?php

session_start();

if( !isset( $_SESSION["carrinho"] ) ) {
    $_SESSION["carrinho"] = array();
}

$produtosLista = 'produtos.json';

function lerProdutos($lista) {
    if (!file_exists($lista) || filesize($lista) == 0) {
        return ['cafes' => [], 'comidas' => []];
    }
    $json = file_get_contents($lista);
    return json_decode($json, true);
}

$produtos = lerProdutos($produtosLista);
$listaCafes = $produtos['cafes'];
$listaComidas = $produtos['comidas'];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Café Grão Nobre</title>
</head>
<body>
    <h1>Bem Vindo!</h1>
    <h2>Cardápio:</h2>

    <form action="dados.php" method="get">
        <label for="cafe"> Café: </label>
        <br>
        <select name="cafe" id="cafe" required>
            <option value=""> Selecione um café </option>
            <?php foreach( $listaCafes as $ca ) :  ?>
                <option value="<?= $ca['nome'] ."*". $ca['valor'] ?>">
                    <?= $ca['nome'] ." | R$ ". number_format($ca['valor'], 2, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="qtd" id="qtd_cafe" placeholder="Quant." min="1" max="999" step="1" value="1" required>
        <br><br>
        <input type="submit" value="Adicionar Café">
    </form>

    <br>

    <form action="dados.php" method="get">
        <label for="comida"> Comidas: </label>
        <br>
        <select name="comida" id="comida" required>
            <option value=""> Selecione algo para comer: </option>
            <?php foreach( $listaComidas as $co ) : ?>
                <option value="<?= $co['nome'] ."*". $co['valor'] ?>">
                    <?= $co['nome'] ." | R$ ". number_format($co['valor'], 2, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="qtde" id="qtde_comida" placeholder="Quantia" min="1" max="999" step="1" value="1" required>
        <br><br>
        <input type="submit" value="Adicionar Comida">
    </form>

    <br>

    <?php if( count( $_SESSION["carrinho"] ) > 0 ) : ?>
        <a href="dados.php"> Ver itens do carrinho </a>
    <?php endif; ?>

</body>
</html>