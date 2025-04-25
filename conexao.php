<?php
$host = 'localhost'; // ou o endereço do seu servidor
$db = 'eco'; // nome do banco de dados
$user = 'root'; // seu usuário do banco de dados
$pass = 'root'; // sua senha do banco de dados

// Cria a conexão
$conn = new mysqli($host, $user, $pass, $db);


// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

?>