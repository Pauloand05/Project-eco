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

// Verificar se o ID da publicação foi passado
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Conectar ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Consultar a publicação com o ID fornecido
    $sql = "SELECT * FROM publicacoes WHERE id = $id AND admin_codigo = '$admin_codigo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Carregar os dados da publicação
        $row = $result->fetch_assoc();
        $titulo = $row['titulo'];
        $conteudo = $row['conteudo'];
        $status = $row['status'];
        $link = $row['link'];
        $imagem = $row['imagem'];
    } else {
        echo "Publicação não encontrada.";
        exit;
    }
}

// Atualizar os dados da publicação quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $status = $_POST['status'];
    $link = $_POST['link'];
    $imagem = $_POST['imagem'];

    // Atualizar os dados no banco
    $sql_update = "UPDATE publicacoes SET titulo = ?, conteudo = ?, status = ?, link = ?, imagem = ?, data_atualizacao = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('sssssi', $titulo, $conteudo, $status, $link, $imagem, $id);

    if ($stmt->execute()) {
        // Redirecionar para a lista de publicações após salvar
        header("Location: publicacoes_listar.php");
        exit;
    } else {
        echo "Erro ao atualizar publicação: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicação</title>
    <style>
        /* Reset básico de estilo */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Corpo e layout geral */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 2em;
            color: #333;
        }

        header .btn {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        header .btn:hover {
            background-color: #0056b3;
        }

        /* Formulário de edição */
        .form-publicacao {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 1.1em;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            margin-top: 5px;
        }

        .form-group textarea {
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Publicação</h1>
            <a href="publicacoes_listar.php" class="btn">Voltar para a lista</a>
        </header>

        <form action="publicacoes_editar.php?id=<?php echo $id; ?>" method="POST" class="form-publicacao">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
            </div>

            <div class="form-group">
                <label for="conteudo">Conteúdo</label>
                <textarea id="conteudo" name="conteudo" rows="5" required><?php echo htmlspecialchars($conteudo); ?></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="ativo" <?php if ($status == 'ativo') echo 'selected'; ?>>Ativo</option>
                    <option value="inativo" <?php if ($status == 'inativo') echo 'selected'; ?>>Inativo</option>
                    <option value="rascunho" <?php if ($status == 'rascunho') echo 'selected'; ?>>Rascunho</option>
                </select>
            </div>

            <div class="form-group">
                <label for="link">Link</label>
                <input type="url" id="link" name="link" value="<?php echo htmlspecialchars($link); ?>">
            </div>

            <div class="form-group">
                <label for="imagem">Imagem (URL)</label>
                <input type="text" id="imagem" name="imagem" value="<?php echo htmlspecialchars($imagem); ?>">
            </div>

            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>