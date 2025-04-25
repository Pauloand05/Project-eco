<?php
session_start();
include 'conexao.php'; // Inclui o arquivo de conexão

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Obtém o CPF do usuário da sessão (no banco o identificador é o CPF)
$user_cpf = $_SESSION['user_id']; // O campo 'user_id' deve conter o CPF do usuário

// Verifica se o usuário realmente existe na base de dados
$sql = "SELECT 1 FROM usuario WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_cpf);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    session_destroy(); // Destrói a sessão se o usuário não existir
    header("Location: login.php");
    exit();
}

// Busca as informações do usuário no banco de dados
$sql = "SELECT nome, email, telefone, nickname FROM usuario WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_cpf); // Ajuste o tipo para "s" (string) já que CPF é um VARCHAR
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $nome = $user['nome'];
    $email = $user['email'];
    $telefone = $user['telefone'];
    $nickname = $user['nickname'];
} else {
    echo "Usuário não encontrado.";
    exit();
}

// Fecha a declaração e a conexão
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <style>
        :root {
            --cor-principal: #007bff; /* Azul */
            --cor-secundaria: #0056b3; /* Azul escuro */
            --cor-success: #2ecc71; /* Verde */
            --cor-error: #e74c3c; /* Vermelho */
            --cor-texto: white; /* Branco */
            --cor-fundo: #2c3e50; /* Fundo escuro */
            --cor-fundo-container: rgba(52, 73, 94, 0.8) url('img/roxo.jpg'); /* Fundo do contêiner com imagem */
            --cor-fundo-imagem: url('img/roxo.jpg'); /* Imagem de fundo */
        }

        /* Layout */
        .perfil-page {
            display: flex;
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/roxo.jpg');
            background-size: cover;
            background-position: center;
            color: var(--cor-texto);
            padding: 20px;
        }

        .content {
            width: 100%;
            padding: 40px;
        }

        .content h1 {
            color: var(--cor-principal);
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            opacity: 0;
            transform: scale(0.8);
            animation: fadeIn 0.8s forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .content h1:hover {
            color: var(--cor-secundaria);
            transform: translateY(-5px);
        }

        .container {
            background: rgba(52, 73, 94, 0.8);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.6s forwards;
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container h2 {
            color: var(--cor-principal);
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: 600;
        }

        .container p {
            font-size: 18px;
            margin: 10px 0;
            color: var(--cor-texto);
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            background: var(--cor-principal);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .btn:hover {
            background: var(--cor-secundaria);
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .perfil-page {
                flex-direction: column;
                animation: fadeInContent 1s ease-out;
            }

            .content {
                padding: 20px;
                opacity: 0;
                animation: fadeInContent 0.6s forwards;
            }

            .container {
                padding: 15px;
                opacity: 0;
                animation: fadeInContent 0.7s forwards;
            }

            .actions {
                flex-direction: column;
                align-items: center;
            }
        }

        @keyframes fadeInContent {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- Navbar inalterável -->

    <div class="perfil-page">
        <!-- Conteúdo Principal -->
        <div class="content">
            <h1>Bem-vindo ao seu perfil, <?php echo htmlspecialchars($nickname); ?>!</h1>
            <p>Aqui você pode gerenciar suas informações e configurações.</p>

            <!-- Cartões de Informações -->
            <div class="container">
                <h2>Suas Informações</h2>
                <p><strong>Nickname:</strong> <?php echo htmlspecialchars($nickname); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($telefone); ?></p>
            </div>

            <!-- Ações -->
            <div class="actions">
                <a href="usuario_editar.php"><button class="btn"><i class="fas fa-edit"></i>Editar Perfil</button></a>
                <form method="POST" action="usuario_delete.php" style="display:inline;">
                    <button class="btn"><i class="fas fa-trash-alt"></i>Deletar Conta</button>
                </form>
                <a href="logout.php"><button class="btn"><i class="fas fa-sign-out-alt"></i>Sair</button></a>
                <a href="denuncia_listar.php"><button class="btn"><i class="fas fa-bell"></i>Ver Minhas Denúncias</button></a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>