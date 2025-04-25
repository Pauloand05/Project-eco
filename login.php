<?php
session_start();
include 'conexao.php'; // Inclua o arquivo de conexão aqui

$error = ""; // Para mensagens de erro
$email = ""; // Inicializa a variável para evitar o erro "undefined variable"

// Função para registrar a tentativa de login
function registrarTentativa($conn, $ip) {
    $sql = "INSERT INTO tentativas_login (ip) VALUES (?) ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_login = NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ip);
    $stmt->execute();
}

// Função para verificar se o IP está bloqueado
function verificarBloqueio($conn, $ip) {
    $sql = "SELECT tentativas, ultimo_login FROM tentativas_login WHERE ip = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tentativas = $row['tentativas'];
        $ultimo_login = strtotime($row['ultimo_login']);
        
        // Se o número de tentativas for maior que 5 e o último login for dentro de 15 minutos
        if ($tentativas >= 5 && (time() - $ultimo_login) < 900) {
            return true; // IP bloqueado
        }
    }
    return false; // IP não está bloqueado
}

// Se o método for POST (login)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Agora a variável $email é definida aqui
    $senha = $_POST['senha'];
    $ip = $_SERVER['REMOTE_ADDR']; // Pega o IP do cliente

    // Verifica se o IP está bloqueado
    if (verificarBloqueio($conn, $ip)) {
        $error = "Muitas tentativas falhas. Tente novamente em 15 minutos.";
    } else {
        // Valida o formato do email no lado do servidor
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email inválido!";
        } else {
            // Busca o usuário pelo email
            $sql = "SELECT * FROM usuario WHERE email = ?"; 
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email); 
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verifica a senha
                if (password_verify($senha, $user['senha'])) { 
                    // Resetar tentativas após login bem-sucedido
                    $sql_reset = "DELETE FROM tentativas_login WHERE ip = ?";
                    $stmt_reset = $conn->prepare($sql_reset);
                    $stmt_reset->bind_param("s", $ip);
                    $stmt_reset->execute();

                    session_regenerate_id(true); // Garante que o ID da sessão seja novo para evitar ataques de fixação de sessão
                    $_SESSION['user_id'] = $user['cpf']; 
                    $_SESSION['user_nickname'] = $user['nickname']; 

                    // Se o usuário marcou o "lembrar-me", cria um cookie para lembrar o email
                    if (isset($_POST['lembrar_me'])) {
                        setcookie('user_email', $email, time() + 60 * 60 * 24 * 30, "/", "", isset($_SERVER['HTTPS']), true);
                    } else {
                        setcookie('user_email', "", time() - 3600, "/", "", isset($_SERVER['HTTPS']), true);
                    }

                    header("Location: perfil.php"); 
                    exit();
                } else {
                    // Senha incorreta, registrar a tentativa
                    registrarTentativa($conn, $ip);
                    $error = "Email ou senha incorretos!";
                }
            } else {
                // E-mail não encontrado, registrar a tentativa
                registrarTentativa($conn, $ip);
                $error = "Email ou senha incorretos!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos globais e responsivos */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
            background-image: url('img/lua.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.5);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-form {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: #ffffff;
            animation: slideIn 0.6s ease forwards;
        }

        .login-form h1 {
            margin-bottom: 20px;
            font-size: 2.5em;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
            font-weight: bold;
            color: #ffffff;
        }

        .form-control {
            width: 85%;
            padding: 15px;
            border: 2px solid #fff;
            border-radius: 30px;
            font-size: 18px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            outline: none;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
            padding-left: 40px;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.3);
            border-color: #007bff;
        }

        .remember-forgot {
            margin: -15px 0 15px;
            font-size: 1em;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }

        .remember-forgot p a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .btn {
            background-color: #007BFF;
            color: #ffffff;
            padding: 15px 0;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 20px;
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-weight: bold;
        }

        p {
            margin-top: 20px;
            color: #ffffff;
            font-size: 1em;
        }

        p a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .login-form {
                padding: 20px;
                width: 90%;
            }
        }

    </style>
</head>
<body>

<div class="login-form">
<h1>Login</h1>

    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Digite seu email" value="<?php echo htmlspecialchars($email); ?>" required autofocus aria-label="Digite seu email">
        </div>
        <div class="form-group">
            <label for="senha"><i class="fas fa-lock"></i> Senha:</label>
            <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha" required aria-label="Digite sua senha">
        </div>

        <div class="remember-forgot">
            <label>
                <input type="checkbox" name="lembrar_me" <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>> Lembrar-me
            </label>
            <p><a href="esqueci_senha.php">Esqueci minha senha</a></p>
        </div>

        <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Entrar</button>
    </form>

    <p><a href="cadastro.php">Não possui conta? Cadastrar-se</a></p>
</div>

</body>
</html>