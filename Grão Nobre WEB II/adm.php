<?php
session_start();

$produtosLista = 'produtos.json';

function lerProdutos($lista) {
    if (!file_exists($lista) || filesize($lista) == 0) {
        return ['cafes' => [], 'comidas' => []]; 
    }
    $json = file_get_contents($lista);
    return json_decode($json, true); 
}

function salvarProdutos($lista, $data) {
    file_put_contents($lista, json_encode($data, JSON_PRETTY_PRINT));
}

$produtos = lerProdutos($produtosLista);
$listaCafes = $produtos['cafes'];
$listaComidas = $produtos['comidas'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar_produto') {
        $tipo = $_POST['tipo_produto'];
        $nome = trim($_POST['nome_produto']);
        $valor = floatval(str_replace(',', '.', $_POST['valor_produto']));
        
        if (!empty($nome) && $valor > 0) {
            $novoProduto = ['nome' => $nome, 'valor' => $valor];
            if ($tipo === 'cafe') {
                $produtos['cafes'][] = $novoProduto;
            } elseif ($tipo === 'comida') {
                $produtos['comidas'][] = $novoProduto;
            }
            salvarProdutos($produtosLista, $produtos);
            header("Location: adm.php");
            exit();
        }
    }

    if (isset($_POST['acao']) && $_POST['acao'] === 'editar_produto') {
        $tipo = $_POST['tipo_produto_edit'];
        $index = intval($_POST['index_produto_edit']);
        $nome = trim($_POST['nome_produto_edit']);
        $valor = floatval(str_replace(',', '.', $_POST['valor_produto_edit']));

        if (!empty($nome) && $valor > 0) {
            if ($tipo === 'cafe' && isset($produtos['cafes'][$index])) {
                $produtos['cafes'][$index]['nome'] = $nome;
                $produtos['cafes'][$index]['valor'] = $valor;
            } elseif ($tipo === 'comida' && isset($produtos['comidas'][$index])) {
                $produtos['comidas'][$index]['nome'] = $nome;
                $produtos['comidas'][$index]['valor'] = $valor;
            }
            salvarProdutos($produtosLista, $produtos);
            header("Location: adm.php");
            exit();
        }
    }

    if (isset($_POST['acao']) && $_POST['acao'] === 'remover_produto') {
        $tipo = $_POST['tipo_produto_remove'];
        $index = intval($_POST['index_produto_remove']);

        if ($tipo === 'cafe' && isset($produtos['cafes'][$index])) {
            array_splice($produtos['cafes'], $index, 1);
        } elseif ($tipo === 'comida' && isset($produtos['comidas'][$index])) {
            array_splice($produtos['comidas'], $index, 1);
        }
        salvarProdutos($produtosLista, $produtos);
        header("Location: adm.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Painel de Administração</h1>

    <div class="admin-section">
        <h2>Adicionar Novo Produto</h2>
        <form action="adm.php" method="post" class="add-product-form">
            <input type="hidden" name="acao" value="adicionar_produto">
            
            <label for="tipo_produto">Tipo:</label>
            <select name="tipo_produto" id="tipo_produto" required>
                <option value="cafe">Café</option>
                <option value="comida">Comida</option>
            </select>

            <label for="nome_produto">Nome do Produto:</label>
            <input type="text" name="nome_produto" id="nome_produto" placeholder="Ex: Latte Gelado" required>

            <label for="valor_produto">Valor (R$):</label>
            <input type="text" name="valor_produto" id="valor_produto" placeholder="Ex: 15.50" pattern="[0-9]+([,\.][0-9]{1,2})?" title="Use ponto ou vírgula como separador decimal." required>

            <button type="submit" class="btn-acao btn-adicionar">Adicionar Produto</button>
        </form>
    </div>

    <div class="admin-section">
        <h2>Gerenciar Cafés</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Valor (R$)</th>
                    <th>Produto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaCafes)): ?>
                    <tr><td colspan="4">Nenhum café cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaCafes as $index => $cafe): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $cafe['nome'] ?></td>
                            <td><?= number_format($cafe['valor'], 2, ',', '.') ?></td>
                            <td>
                                <div class="admin-actions">
                                    <form action="adm.php" method="post" class="form-inline admin-edit-form">
                                        <input type="hidden" name="acao" value="editar_produto">
                                        <input type="hidden" name="tipo_produto_edit" value="cafe">
                                        <input type="hidden" name="index_produto_edit" value="<?= $index ?>">
                                        <input type="text" name="nome_produto_edit" value="<?= $cafe['nome'] ?>" placeholder="Nome" required class="input-admin-nome">
                                        <input type="text" name="valor_produto_edit" value="<?= number_format($cafe['valor'], 2, ',', '.') ?>" placeholder="Valor" pattern="[0-9]+([,\.][0-9]{1,2})?" required class="input-admin-valor">
                                        <button type="submit" class="btn-acao btn-atualizar">Editar</button>
                                    </form>

                                    <form action="adm.php" method="post" class="form-inline admin-remove-form">
                                        <input type="hidden" name="acao" value="remover_produto">
                                        <input type="hidden" name="tipo_produto_remove" value="cafe">
                                        <input type="hidden" name="index_produto_remove" value="<?= $index ?>">
                                        <button type="submit" class="btn-acao btn-remover" onclick="return confirm('Tem certeza que deseja remover este café?');">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-section">
        <h2>Gerenciar Comidas</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Valor (R$)</th>
                    <th>Produto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaComidas)): ?>
                    <tr><td colspan="4">Nenhuma comida cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaComidas as $index => $comida): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($comida['nome']) ?></td>
                            <td><?= number_format($comida['valor'], 2, ',', '.') ?></td>
                            <td>
                                <div class="admin-actions">
                                    <form action="adm.php" method="post" class="form-inline admin-edit-form">
                                        <input type="hidden" name="acao" value="editar_produto">
                                        <input type="hidden" name="tipo_produto_edit" value="comida">
                                        <input type="hidden" name="index_produto_edit" value="<?= $index ?>">
                                        <input type="text" name="nome_produto_edit" value="<?= $comida['nome'] ?>" placeholder="Nome" required class="input-admin-nome">
                                        <input type="text" name="valor_produto_edit" value="<?= number_format($comida['valor'], 2, ',', '.') ?>" placeholder="Valor" pattern="[0-9]+([,\.][0-9]{1,2})?" required class="input-admin-valor">
                                        <button type="submit" class="btn-acao btn-atualizar">Editar</button>
                                    </form>

                                    <form action="adm.php" method="post" class="form-inline admin-remove-form">
                                        <input type="hidden" name="acao" value="remover_produto">
                                        <input type="hidden" name="tipo_produto_remove" value="comida">
                                        <input type="hidden" name="index_produto_remove" value="<?= $index ?>">
                                        <button type="submit" class="btn-acao btn-remover" onclick="return confirm('Tem certeza que deseja remover esta comida?');">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br>
    <button onclick="window.location.href='index.php'">Voltar para o Cardápio</button>

</body>
</html>