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

// Consultar o banco de dados para obter o nome e o nível do administrador
// Primeiro, buscar a hierarquia
$sql = "SELECT hierarquia FROM eco.hierarquia WHERE admin_codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_codigo);  // Usar "s" para código de administrador que é string
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Armazenar o nível hierárquico na sessão
    $stmt->bind_result($hierarquia);
    $stmt->fetch();
    $_SESSION['hierarquia'] = $hierarquia;  // Armazenar o nível na sessão
} else {
    // Caso não encontre o administrador na tabela hierarquia, redireciona para o login
    header("Location: admin_login.php");
    exit;
}

$stmt->close();  // Fechar a declaração após usar

// Agora, buscar o nome do administrador
$sql = "SELECT nome FROM eco.admin WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_codigo);  // Usar "s" para código de administrador que é string
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Armazenar o nome na sessão
    $stmt->bind_result($nome);
    $stmt->fetch();
    $_SESSION['nome'] = $nome;  // Armazenar o nome na sessão
} else {
    // Caso não encontre o administrador, redireciona para o login
    header("Location: admin_login.php");
    exit;
}

$stmt->close();  // Fechar a declaração após usar
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #333;
        }

        header p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .menu-item {
            width: 100%;
            margin: 10px 0;
        }

        .menu-item a {
            display: block;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            text-align: center;
            font-size: 18px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .menu-item a:hover {
            background-color: #45a049;
        }

        footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

        footer p {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Bem-vindo ao Sistema de Gestão</h1>
            <p>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>! O que você gostaria de fazer?</p>
        </header>
        
        <div class="menu">
            <!-- Exibir opções de menu com base na hierarquia do administrador -->

            <!-- Permitir cadastro de administradores apenas para níveis 1 e 2 -->
            <?php if ($_SESSION['hierarquia'] == 1 || $_SESSION['hierarquia'] == 2): ?>
                <div class="menu-item">
                    <a href="admin_cadastro.php">Cadastrar Administrador</a>
                </div>
            <?php endif; ?>

            <!-- Permitir gerenciamento de denúncias para níveis 2, 3 e 4 -->
            <?php if ($_SESSION['hierarquia'] >= 1 && $_SESSION['hierarquia'] <= 4): ?>
                <div class="menu-item">
                    <a href="denuncias_gerenciar.php">Gerenciar Denúncias</a>
                </div>
            <?php endif; ?>

            <!-- Permitir criação de publicações apenas para nível 5 -->
            <?php if ($_SESSION['hierarquia'] == 1 || $_SESSION['hierarquia'] == 5): ?>
                <div class="menu-item">
                    <a href="publicacoes_listar.php">Publicações</a>
                </div>
            <?php endif; ?>

            <!-- Todos os níveis podem visualizar relatórios -->
            <div class="menu-item">
                <a href="relatorios_visualizar.php">Visualizar Relatórios</a>
            </div>

            <!-- Link para logout -->
            <div class="menu-item">
                <a href="logout.php">Sair</a>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 Sistema de Gestão. Todos os direitos reservados.</p>
        </footer>
    </div>
</body>
</html>