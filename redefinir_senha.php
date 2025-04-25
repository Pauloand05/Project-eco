<?php
session_start();
include 'conexao.php';
include_once "utils/api.php";

function deleteToken($conn, $token) {
    if ($token) {
        $sql = "DELETE FROM reset_senha WHERE token = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Erro ao preparar a consulta para deletar o token.");
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();

        // Verifica se o token foi realmente removido
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        }
        $stmt->close();
    }
    return false;
}

function verifyToken($conn, $token) {
    $sql = "SELECT token, email, data_criacao FROM reset_senha WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Token inválido ou expirado.");
    }

    $row = $result->fetch_assoc();

    $timezone = new DateTimeZone('America/Sao_Paulo');
    $createdAt = new DateTime($row['data_criacao'], $timezone);
    $now = new DateTime('now', $timezone);
    $interval = $createdAt->diff($now);
    $elapsedTimeInSeconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

    if ($elapsedTimeInSeconds >= 300) {
        deleteToken($conn, $row['token']);  
        echo "<script>alert('O tempo para redefinir a senha expirou.'); window.location.href = 'pagina_de_redefinicao.php';</script>";
        exit();
    }

    return $row;
}

function selectUser($conn, $email) {
    $sql = "SELECT nome FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Token inválido ou expirado.");
    }

    $row = $result->fetch_assoc();
    return $row;
}

function updatePassword($conn, $email, $nova_senha) {
    $sql = "UPDATE usuario SET senha = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Erro na preparação da consulta para atualizar a senha.');
    }

    $hashedPassword = password_hash($nova_senha, PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $hashedPassword, $email);

    if (!$stmt->execute()) {
        die('Erro ao atualizar a senha: ' . $stmt->error);
    }

    $stmt->close();
}

function respondToPasswordChange($email, $nome) {
    $resultado_de_alterar_senha = alterarSenha($nome, $email);
    if ($resultado_de_alterar_senha['success']) {
        echo $resultado_de_alterar_senha['message'];
    } else {
        echo $resultado_de_alterar_senha['error'];
    }
}


if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $rowCheck = verifyToken($conn, $token);  

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nova_senha = trim($_POST['nova_senha']);
        $confirmar_senha = trim($_POST['confirmar_senha']);
        
        if (empty($nova_senha)) {
            die("A nova senha não pode estar vazia.");
        }
        
        // Verifica se as senhas coincidem
        if ($nova_senha !== $confirmar_senha) {
            die("As senhas não coincidem. Por favor, tente novamente.");
        }
    
        updatePassword($conn, $rowCheck['email'], $nova_senha);
    
        deleteToken($conn, $token);
    
        respondToPasswordChange($rowCheck['email'], selectUser($conn, $rowCheck['email'])['nome']);    
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Container centralizado */
        .container {
            width: 100%;
            max-width: 400px;
            margin: 206px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 50px;
        }

        /* Título */
        h2 {
            text-align: center;
            color: #333;
        }

        /* Formulário */
        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            color: #555;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Responsividade */
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
    <script>
        // Valida se as senhas coincidem antes de enviar o formulário
        function validatePasswords() {
            var senha = document.getElementById("nova_senha").value;
            var confirmacao = document.getElementById("confirmar_senha").value;
            if (senha !== confirmacao) {
                alert("As senhas não coincidem. Tente novamente.");
                return false;  // Não envia o formulário
            }
            return true;  // Envia o formulário
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Redefinir Senha</h2>
        <form method="POST" action="" onsubmit="return validatePasswords()">
            <div class="form-group">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <button type="submit">Redefinir Senha</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
