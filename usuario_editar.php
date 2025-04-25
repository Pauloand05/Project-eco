<?php
session_start();
include 'conexao.php'; 

// Regenera o ID da sessão para evitar ataque de fixação de sessão
session_regenerate_id(true); 

// Verifica se o usuário está logado e autorizado a editar seu próprio perfil
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userCpf = $_SESSION['user_id']; 

// Obtém os dados do usuário a partir do CPF
$sql = "SELECT * FROM usuario WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userCpf);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $nickname = $_POST['nickname'];

    // Validação e sanitização dos dados
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email inválido!";
    } elseif (!preg_match('/^\d{10,15}$/', $telefone)) {
        $message = "Telefone inválido. Deve ter entre 10 e 15 dígitos.";
    } elseif (strlen($nickname) > 50 || strlen($nome) > 100) {
        $message = "Nome ou nickname muito longo.";
    } else {
        // Verifica se o nickname já está em uso
        $sqlCheck = "SELECT * FROM usuario WHERE nickname = ? AND cpf != ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ss", $nickname, $userCpf);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $message = "Esse nickname já está em uso. Por favor, escolha outro.";
        } else {
            // Atualiza os dados do usuário
            $sqlUpdate = "UPDATE usuario SET nome = ?, email = ?, telefone = ?, nickname = ? WHERE cpf = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssss", $nome, $email, $telefone, $nickname, $userCpf);

            if ($stmtUpdate->execute()) {
                header('Location: perfil.php?msg=Usuário atualizado com sucesso!');
                exit();
            } else {
                $message = "Erro ao atualizar usuário. Tente novamente mais tarde.";
            }

            $stmtUpdate->close();
        }

        $stmtCheck->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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
            background-image: url('img/lua.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.5); /* Menos brilho na imagem para maior contraste */
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

        /* Formulário de Edição */
        .reset-form {
            background-color: rgba(0, 0, 0, 0.8); /* Fundo mais escuro para aumentar o contraste */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 450px;
            text-align: center;
            color: #ffffff;
            animation: slideIn 0.6s ease forwards;
        }

        .reset-form h1 {
            margin-bottom: 30px;
            font-size: 2.5em;
            font-weight: bold;
            color: #fff; /* Garantir boa visibilidade do título */
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
            font-weight: bold;
            color: #ffffff; /* Garantir que as labels sejam visíveis */
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #fff; /* Borda branca para contraste */
            border-radius: 30px;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.3); /* Fundo mais claro para os campos */
            color: #ffffff;
            outline: none;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.4); /* Fundo mais claro ao focar */
            border-color: #007bff; /* Borda azul ao focar */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Sombra azul ao focar */
        }

        .btn {
            background-color: #007BFF; /* Cor vibrante para o botão */
            color: #ffffff;
            padding: 15px 0;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3; /* Cor mais escura ao passar o mouse */
        }

        /* Mensagens */
        .message {
            margin-bottom: 20px;
            font-weight: bold;
            color: #ff4d4d; /* Mensagens de erro em vermelho */
        }

        /* Links */
        p {
            margin-top: 20px;
            color: #ffffff;
            font-size: 1.1em; /* Aumenta o tamanho da fonte do texto */
        }

        p a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="reset-form">
    <h1>Editar Usuário</h1>
    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-user-tag"></i> Nickname:</label>
            <input type="text" name="nickname" class="form-control" value="<?php echo htmlspecialchars($user['nickname']); ?>" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-phone"></i> Telefone:</label>
            <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($user['telefone']); ?>" required>
        </div>
        <button type="submit" class="btn"><i class="fas fa-redo-alt"></i> Atualizar</button>
    </form>
    <p><a href="perfil.php">Voltar para Perfil</a></p>
</div>

</body>
</html>