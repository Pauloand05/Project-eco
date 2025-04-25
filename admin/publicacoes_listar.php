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

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para listar as publicações
$sql = "SELECT * FROM publicacoes WHERE admin_codigo = '$admin_codigo'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Publicações</title>
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
            max-width: 1100px;
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

        .btn {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .publicacoes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .publicacoes-table th,
        .publicacoes-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .publicacoes-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .publicacoes-table tr:hover {
            background-color: #f9f9f9;
        }

        .publicacoes-table .btn {
            font-size: 0.9em;
            padding: 5px 10px;
        }

        .publicacoes-table .edit {
            background-color: #28a745;
        }

        .publicacoes-table .delete {
            background-color: #dc3545;
        }

        .publicacoes-table .edit:hover {
            background-color: #218838;
        }

        .publicacoes-table .delete:hover {
            background-color: #c82333;
        }

        /* Mensagens de erro ou aviso */
        p {
            font-size: 1.2em;
            color: #666;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Lista de Publicações</h1>
            <!-- Botão Voltar -->
            <a href="index.php" class="back-btn">
                    <button type="button">Voltar</button>
            </a>
            <a href="publicacoes_create.php" class="btn">Criar Nova Publicação</a>
        </header>

        <?php if ($result->num_rows > 0): ?>
        <table class="publicacoes-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["titulo"]; ?></td>
                    <td><?php echo ucfirst($row["status"]); ?></td>
                    <td>
                        <a href="publicacoes_editar.php?id=<?php echo $row['id']; ?>" class="btn-editar">Editar</a>
                        <a href="publicacoes_delete.php?id=<?php echo $row['id']; ?>" class="btn-deletar" onclick="return confirm('Tem certeza que deseja excluir esta publicação?')">Deletar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Nenhuma publicação encontrada.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>
</body>
</html>