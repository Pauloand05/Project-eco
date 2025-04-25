<?php
// Iniciar a sessão no início do arquivo
session_start();

// Incluir a conexão com o banco de dados
include 'db_connection.php';  // Conexão com o banco de dados

// Verificar se o administrador está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Caso não esteja logado, redireciona para a página de login
    header("Location: admin_login.php");
    exit;
}

// Obter o código do administrador da sessão
$admin_codigo = $_SESSION['admin_codigo']; 

// Processar o formulário quando ele for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $data_atendimento = $_POST['data_atendimento'];
    $status = $_POST['status'];
    $admin_codigo = $_POST['admin_codigo'];
    $denuncia_id = $_POST['denuncia_id'];

    // Preparar e vincular
    $stmt = $conn->prepare("INSERT INTO atendimento (data_atendimento, status, admin_codigo, denuncia_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $data_atendimento, $status, $admin_codigo, $denuncia_id);

    // Executar a inserção
    if ($stmt->execute()) {
        echo "Atendimento criado com sucesso!";
    } else {
        echo "Erro ao criar atendimento: " . $stmt->error;
    }

    // Fechar a conexão com o banco de dados
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Atendimento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="date"], select {
            cursor: pointer;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Criar Novo Atendimento</h2>

    <!-- Formulário para inserir atendimento -->
    <form action="atendimento_create.php" method="POST">
        <label for="data_atendimento">Data do Atendimento:</label>
        <input type="date" id="data_atendimento" name="data_atendimento" required><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="aberto">Aberto</option>
            <option value="em atendimento">Em Atendimento</option>
            <option value="atendida">Atendida</option>
        </select><br>

        <label for="admin_codigo">Código do Administrador:</label>
        <input type="text" id="admin_codigo" name="admin_codigo" required><br>

        <label for="denuncia_id">ID da Denúncia:</label>
        <input type="number" id="denuncia_id" name="denuncia_id" required><br>

        <input type="submit" value="Criar Atendimento">
    </form>
</div>

</body>
</html>