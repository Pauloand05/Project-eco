<?php
// Informações do banco de dados
$servername = "localhost"; // Ou o nome do servidor de banco de dados
$username = "root";        // Seu nome de usuário do banco de dados (comum: root)
$password = "root";            // Sua senha do banco de dados (comum: em branco no localhost)
$dbname = "eco";           // O nome do banco de dados que você deseja acessar

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definir o charset para garantir que caracteres especiais sejam tratados corretamente
$conn->set_charset("utf8mb4");
?>
