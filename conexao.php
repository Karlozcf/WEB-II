<?php
// Configurações do Banco de Dados
$host = 'localhost'; // Geralmente 'localhost'
$dbname = 'graonobre'; // Substitua pelo nome do seu banco de dados
$user = 'root'; // O usuário padrão do XAMPP para MySQL
$password = ''; // Deixe a senha vazia se você não a configurou

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

// Opções do PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Cria a conexão PDO
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    // Em caso de falha na conexão, exibe uma mensagem de erro
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>