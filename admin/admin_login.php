<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir o arquivo de conexão com o banco de dados
include 'db_connection.php';  // Conexão com o banco de dados

// Variáveis de erro
$erro = '';

// Verificar se o administrador já está logado (evitar login repetido)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Se já estiver logado, redireciona para o painel administrativo
    header("Location: index.php");
    exit();
}

// Verificar se o formulário de login foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegar os dados do formulário
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Consultar o banco de dados para verificar se o email existe
    $sql = "SELECT codigo, senha FROM eco.admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Verificar se o email foi encontrado
    if ($stmt->num_rows > 0) {
        // O email foi encontrado, agora vamos buscar a senha
        $stmt->bind_result($codigo, $senha_hash);
        $stmt->fetch();

        // Primeira verificação sem hash (comparando diretamente as senhas)
        if ($senha === $senha_hash) {
            // A senha fornecida é igual à senha armazenada (sem considerar hash)
            $_SESSION['admin_codigo'] = $codigo;  // Salvar o código do admin na sessão
            $_SESSION['admin_email'] = $email;    // Salvar o email na sessão
            $_SESSION['admin_logged_in'] = true;  // Marcar que o administrador está logado

            // Redirecionar para a página do painel administrativo
            header("Location: index.php");
            exit();
        } else {
            // Segunda verificação com hash (usando password_verify)
            if (password_verify($senha, $senha_hash)) {
                // A senha fornecida corresponde ao hash armazenado
                $_SESSION['admin_codigo'] = $codigo;  // Salvar o código do admin na sessão
                $_SESSION['admin_email'] = $email;    // Salvar o email na sessão
                $_SESSION['admin_logged_in'] = true;  // Marcar que o administrador está logado

                // Redirecionar para a página do painel administrativo
                header("Location: index.php");
                exit();
            } else {
                // A senha está incorreta
                $erro = "Senha incorreta!";
            }
        }
    } else {
        // O email não foi encontrado
        $erro = "Email não encontrado!";
    }

    $stmt->close();  // Fechar a consulta
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            text-align: left;
            font-weight: bold;
            color: #555;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        p {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login de Administrador</h2>

        <!-- Formulário de login -->
        <form method="POST" action="">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div>
                <button type="submit">Entrar</button>
            </div>

            <!-- Exibir erro, se houver -->
            <?php if ($erro) { echo "<p>$erro</p>"; } ?>
        </form>
    </div>
</body>
</html>