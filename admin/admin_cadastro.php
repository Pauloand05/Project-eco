<?php 
// Iniciar a sessão
session_start();

// Verificar se o admin está logado e tem hierarquia 1
if (isset($_SESSION['admin']) && $_SESSION['admin_logged_in'] === true) {

    $admin_codigo = $_SESSION['admin_codigo']; 

    $sql = "SELECT hierarquia FROM hierarquia WHERE admin_codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_codigo); // Usando o código do admin como identificador
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Se o admin existir, pega a hierarquia
        $admin = $result->fetch_assoc();
        $hierarquia = $admin['hierarquia'];

        // Verifica se a hierarquia do admin é 1 (ou a hierarquia necessária)
        if ($hierarquia != 1) {
            // Caso não seja hierarquia 1, redireciona para a página de login
            header("Location: admin_login.php");
            exit();
        }
    }
}

// Conectar ao banco de dados
include 'db_connection.php'; // Assume que você tem uma conexão configurada

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber dados do formulário
    $codigo = $_POST['codigo']; // Código do administrador (ID)
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $empresa_cnpj = $_POST['empresa_cnpj'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);  // Hash da senha
    $hierarquia = $_POST['hierarquia']; // Definir hierarquia do novo admin

    // 1. Inserir o novo administrador na tabela 'admin'
    $sql_admin = "INSERT INTO eco.admin (codigo, nome, email, telefone, empresa_cnpj, senha)
                  VALUES (?, ?, ?, ?, ?, ?)";

    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("ssssss", $codigo, $nome, $email, $telefone, $empresa_cnpj, $senha);

    // 2. Verificar se a inserção foi bem-sucedida
    if ($stmt_admin->execute()) {
        // Agora que o admin foi inserido, inserimos na tabela 'hierarquia'

        $sql_hierarquia = "INSERT INTO eco.hierarquia (admin_codigo, hierarquia)
                           VALUES (?, ?)";

        $stmt_hierarquia = $conn->prepare($sql_hierarquia);
        $stmt_hierarquia->bind_param("ss", $codigo, $hierarquia);

        if ($stmt_hierarquia->execute()) {
            header("Location: admin_login.php");
        } else {
            echo "Erro ao cadastrar hierarquia do admin!";
        }
    } else {
        echo "Erro ao cadastrar admin!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Administrador</title>
    <style>
        /* Resetando o padding e margin para garantir que o layout seja consistente */
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
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            color: #333;
        }

        input:focus, select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            margin: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        button:active {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Administrador</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="msg <?php echo ($_SESSION['msg_type'] ?? 'msg-info'); ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="codigo">Código (ID):</label>
            <input type="text" id="codigo" name="codigo" required placeholder="Digite o código do administrador">
            
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required placeholder="Digite o nome completo">
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Digite o email">
            
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" required placeholder="Digite o telefone (11 dígitos)">
            
            <label for="empresa_cnpj">CNPJ da Empresa:</label>
            <input type="text" id="empresa_cnpj" name="empresa_cnpj" required placeholder="Digite o CNPJ da empresa">
            
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required placeholder="Digite a senha do administrador">
            
            <!-- Novo campo para selecionar a hierarquia -->
            <label for="hierarquia">Hierarquia:</label>
            <select id="hierarquia" name="hierarquia" required>
                <option value="1">Administrador - Hierarquia 1</option>
                <option value="2">Supervisor - Hierarquia 2</option>
                <option value="3">Usuário - Hierarquia 3</option>
                <option value="4">Usuário - Hierarquia 3</option>
                <option value="5">Noticias - Hierarquia 3</option>
            </select>
            
            <button type="submit">Cadastrar Admin</button>
        </form>
            <!-- Botão Voltar -->
            <a href="index.php" class="back-btn">
                <button type="button">Voltar</button>
            </a>
    </div>
</body>
</html>