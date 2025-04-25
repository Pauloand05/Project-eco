<?php
session_start();
include 'conexao.php';
include_once "utils/api.php";

$message = ""; // Mensagem de feedback
$remainingMinutes = 5; // Tempo padrão
$remainingSeconds = 0; // Tempo padrão

function getUser($conn, $email) {
    if ($email) {
        $sql = "SELECT nome FROM usuario WHERE email = ?;";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            return ["success" => false, "error" => "Falha ao preparar a consulta."];
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        if ($data === null) {
            return ["success" => false, "error" => "Este email não está armazenado no nosso sistema."];
        }   
        
        return ["success" => true, "nome" => $data['nome'], "email" => $email];
    }
}

function deleteToken($conn, $token) {
    if ($token) {
        $sql = "DELETE FROM reset_senha WHERE token = ?;";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            return;
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
       

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return;
        }   
         $stmt->close();
        return;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    
    $sqlCheck = "SELECT token, data_criacao FROM reset_senha WHERE email = ?"; 
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    
    if ($resultCheck->num_rows > 0) {
            
        $timezone = new DateTimeZone('America/Sao_Paulo');
        
        $rowCheck = $resultCheck->fetch_assoc();
            
        $createdAt = new DateTime($rowCheck['data_criacao'], $timezone);
            
        $now = new DateTime('now', $timezone);
            
        $interval = $createdAt->diff($now);
            
        $elapsedTimeInSeconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            
        if ($elapsedTimeInSeconds >= 300) {
            $message = "O tempo para redefinir a senha expirou.";
            deleteToken($conn, $rowCheck['token']);

        } else {
            $remainingTimeInSeconds = 300 - $elapsedTimeInSeconds;
            $remainingMinutes = floor($remainingTimeInSeconds / 60);
            $remainingSeconds = $remainingTimeInSeconds % 60;
                
            $message = "Uma solicitação para redefinir a senha já foi enviada para este email. Restam {$remainingMinutes} minutos e {$remainingSeconds} segundos.";
        }

    } else {
       
        $token = bin2hex(random_bytes(50));

        
        $sqlInsert = "INSERT INTO reset_senha (email, token, data_criacao) VALUES (?, ?, NOW())"; // Mudança aqui para 'data_criacao'
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("ss", $email, $token);

    
        $sqlUser = getUser($conn, $email);
        $data = $sqlUser;

        if ($stmtInsert->execute() && $data['success']) {
            $resultado_de_mudar_senha = mudarSenha($data['nome'], $data['email'], $token);
        

            if ($resultado_de_mudar_senha['success']) {
                $message = $resultado_de_mudar_senha['message'];
            } else {
                deleteToken($conn, $token);
                $message = $resultado_de_mudar_senha['error'];
            }
        } else {
            $message = $data['error'];
        }
    }

    // Fechando as declarações e a conexão
    $stmtCheck->close();
    if (isset($stmtInsert) && $stmtInsert) {
        $stmtInsert->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a Senha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos Gerais */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centraliza verticalmente */
            align-items: center; /* Centraliza horizontalmente */
            font-family: 'Arial', sans-serif;
            background-image: url('img/lua.jpg'); /* Adicione sua imagem aqui */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.7);
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

        /* Formulário de Redefinição */
        .reset-form {
            margin-top: 80px; /* Espaço para a navbar */
            background-color: rgba(0, 0, 0, 0.6); /* 60% opaco para o fundo */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: #ffffff;
            animation: slideIn 0.6s ease forwards; /* Aplica animação de deslizar */
        }

        .reset-form h1 {
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #000000;
            outline: none;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .btn {
            background-color: #007BFF;
            color: #ffffff;
            padding: 15px 0;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            font-weight: bold;
        }

        /* Mensagens */
        .message {
            margin-bottom: 15px;
            font-weight: bold;
            color: #ff4d4d;
        }

        /* Links */
        p {
            margin-top: 20px;
            color: #ffffff;
        }

        p a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Contagem Regressiva */
        #countdown {
            margin-top: 15px;
            color: #ffffff;
        }
    </style>
</head>
<body>    
    <div class="reset-form">
        <h1>Esqueci a Senha</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
            <label><i class="fas fa-lock"></i> Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Enviar</button>
        </form>

        <div id="countdown">
            <p id="countdown-timer"><?php echo $remainingMinutes . " minutos e " . $remainingSeconds . " segundos restantes."; ?></p>
        </div>

        <p><a href="login.php">Voltar ao Login</a></p>
    </div>

    <script>
        var minutes = <?php echo $remainingMinutes; ?>;
        var seconds = <?php echo $remainingSeconds; ?>;
        
        function updateCountdown() {
            if (seconds < 0) {
                seconds = 59;
                minutes--;
            }
            if (minutes < 0) {
                document.getElementById("countdown-timer").innerHTML = "O tempo para redefinir a senha expirou.";
                return;
            }
            document.getElementById("countdown-timer").innerHTML = minutes + " minutos e " + seconds + " segundos restantes.";
            seconds--;
            setTimeout(updateCountdown, 1000);
        }
        updateCountdown();
    </script>
</body>
</html>