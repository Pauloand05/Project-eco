<?php
// Iniciar a sessão
session_start();

// Verificar se o admin está logado e tem hierarquia 1
if (isset($_SESSION['admin']) && $_SESSION['admin_logged_in'] === true) {

    $admin_codigo = $_SESSION['admin_codigo']; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário
    $cnpj = $_POST['cnpj'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $senha = $_POST['senha'];
    $endereco_cep = $_POST['endereco_cep'];

    // Aqui você pode adicionar validação e sanitização dos dados antes de inseri-los no banco de dados

    // Exemplo de código para salvar no banco (supondo que você já tenha a conexão com o banco configurada)
    include 'db_connection.php'; // Inclui a conexão com o banco de dados

    // Inserir dados na tabela `empresa`
    $sql = "INSERT INTO empresa (cnpj, nome, email, telefone, senha, endereco_cep) 
            VALUES ('$cnpj', '$nome', '$email', '$telefone', '$senha', '$endereco_cep')";

    if ($conn->query($sql) === TRUE) {
        echo "Empresa cadastrada com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>
