<?php
// Iniciar a sessão
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

// Verificar se o ID da publicação foi passado via URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Conectar ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Preparar a consulta para deletar a publicação
    $sql = "DELETE FROM publicacoes WHERE id = ? AND admin_codigo = ?"; // Adiciona a verificação do admin_codigo

    $stmt = $conn->prepare($sql);
    
    // Vincular os parâmetros para a consulta (id e admin_codigo)
    $stmt->bind_param('ii', $id, $admin_codigo);  // 'i' para inteiro, pois 'id' e 'admin_codigo' são inteiros

    if ($stmt->execute()) {
        // Redireciona para a lista de publicações após a exclusão
        header("Location: publicacoes_listar.php");
        exit;
    } else {
        echo "Erro ao excluir publicação: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID inválido!";
}
?>