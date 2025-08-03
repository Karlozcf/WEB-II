<?php
session_start();

// Inclui o arquivo de conexão. Certifique-se de que ele esteja no mesmo diretório.
require_once 'conexao.php';

// Esta é a nova lógica de manipulação de dados, agora usando o banco de dados
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Lógica para ADICIONAR produto
    if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar_produto') {
        $tipo = $_POST['tipo_produto'];
        $nome = trim($_POST['nome_produto']);
        // Substitui vírgula por ponto para o banco de dados
        $valor = floatval(str_replace(',', '.', $_POST['valor_produto']));

        if (!empty($nome) && $valor > 0) {
            try {
                // Prepara a consulta para evitar SQL Injection
                $stmt = $pdo->prepare("INSERT INTO produtos (nome, valor, tipo) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $valor, $tipo]);
            } catch (PDOException $e) {
                // Em um ambiente de produção, você salvaria o erro em um log.
                // Para depuração, você pode exibir a mensagem.
                echo "Erro ao adicionar produto: " . $e->getMessage();
            }
        }
        header("Location: adm.php");
        exit();
    }

    // Lógica para EDITAR produto
    if (isset($_POST['acao']) && $_POST['acao'] === 'editar_produto') {
        $id = intval($_POST['id_produto_edit']); // Usamos o ID do banco, não o índice do array
        $nome = trim($_POST['nome_produto_edit']);
        $valor = floatval(str_replace(',', '.', $_POST['valor_produto_edit']));

        if (!empty($nome) && $valor > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, valor = ? WHERE id = ?");
                $stmt->execute([$nome, $valor, $id]);
            } catch (PDOException $e) {
                echo "Erro ao editar produto: " . $e->getMessage();
            }
        }
        header("Location: adm.php");
        exit();
    }

    // Lógica para REMOVER produto
    if (isset($_POST['acao']) && $_POST['acao'] === 'remover_produto') {
        $id = intval($_POST['id_produto_remove']); // Usamos o ID do banco
        try {
            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            echo "Erro ao remover produto: " . $e->getMessage();
        }
        header("Location: adm.php");
        exit();
    }
}

// Lógica de LEITURA dos produtos, agora do banco de dados
$listaCafes = [];
$listaComidas = [];
try {
    $stmt = $pdo->query("SELECT id, nome, valor FROM produtos WHERE tipo = 'cafe' ORDER BY nome");
    $listaCafes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT id, nome, valor FROM produtos WHERE tipo = 'comida' ORDER BY nome");
    $listaComidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar produtos: " . $e->getMessage();
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
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Valor (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaCafes)): ?>
                    <tr><td colspan="4">Nenhum café cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaCafes as $cafe): ?>
                        <tr>
                            <td><?= $cafe['id'] ?></td>
                            <td><?= $cafe['nome'] ?></td>
                            <td><?= number_format($cafe['valor'], 2, ',', '.') ?></td>
                            <td>
                                <div class="admin-actions">
                                    <form action="adm.php" method="post" class="form-inline admin-edit-form">
                                        <input type="hidden" name="acao" value="editar_produto">
                                        <input type="hidden" name="id_produto_edit" value="<?= $cafe['id'] ?>">
                                        <input type="text" name="nome_produto_edit" value="<?= $cafe['nome'] ?>" placeholder="Nome" required class="input-admin-nome">
                                        <input type="text" name="valor_produto_edit" value="<?= number_format($cafe['valor'], 2, ',', '.') ?>" placeholder="Valor" pattern="[0-9]+([,\.][0-9]{1,2})?" required class="input-admin-valor">
                                        <button type="submit" class="btn-acao btn-atualizar">Editar</button>
                                    </form>

                                    <form action="adm.php" method="post" class="form-inline admin-remove-form">
                                        <input type="hidden" name="acao" value="remover_produto">
                                        <input type="hidden" name="id_produto_remove" value="<?= $cafe['id'] ?>">
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
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Valor (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaComidas)): ?>
                    <tr><td colspan="4">Nenhuma comida cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaComidas as $comida): ?>
                        <tr>
                            <td><?= $comida['id'] ?></td>
                            <td><?= htmlspecialchars($comida['nome']) ?></td>
                            <td><?= number_format($comida['valor'], 2, ',', '.') ?></td>
                            <td>
                                <div class="admin-actions">
                                    <form action="adm.php" method="post" class="form-inline admin-edit-form">
                                        <input type="hidden" name="acao" value="editar_produto">
                                        <input type="hidden" name="id_produto_edit" value="<?= $comida['id'] ?>">
                                        <input type="text" name="nome_produto_edit" value="<?= $comida['nome'] ?>" placeholder="Nome" required class="input-admin-nome">
                                        <input type="text" name="valor_produto_edit" value="<?= number_format($comida['valor'], 2, ',', '.') ?>" placeholder="Valor" pattern="[0-9]+([,\.][0-9]{1,2})?" required class="input-admin-valor">
                                        <button type="submit" class="btn-acao btn-atualizar">Editar</button>
                                    </form>

                                    <form action="adm.php" method="post" class="form-inline admin-remove-form">
                                        <input type="hidden" name="acao" value="remover_produto">
                                        <input type="hidden" name="id_produto_remove" value="<?= $comida['id'] ?>">
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