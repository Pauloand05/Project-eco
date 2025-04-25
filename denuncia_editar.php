<?php
session_start();
include 'conexao.php'; // Inclui o arquivo de conexão

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Validação do ID da denúncia
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = $_GET['id'];

// Consulta para buscar os dados da denúncia
$query = "SELECT * FROM `eco`.`denuncia` WHERE id = ? AND usuario_cpf = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('is', $id, $_SESSION['user_id']); // Usa o CPF do usuário logado para garantir que ele só edite suas próprias denúncias
$stmt->execute();
$result = $stmt->get_result();

// Verifica se a consulta retornou um resultado
if ($result->num_rows === 0) {
    die("Denúncia não encontrada ou você não tem permissão para editar.");
}

$row = $result->fetch_assoc();

// Verifica o status da denúncia
if ($row['status'] != 'pendente') {
    die("Esta denúncia não pode ser editada, pois não está no status 'pendente'.");
}

// Processa o formulário de atualização quando o método for POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Valida os dados recebidos no formulário
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    
    if (empty($titulo) || empty($descricao)) {
        $error = "Todos os campos são obrigatórios.";
    } else {
        // Atualiza a denúncia, mantendo o status, a data de criação e o endereço
        $update_query = "UPDATE `eco`.`denuncia` 
                         SET titulo = ?, descricao = ?, data_atualizacao = NOW()
                         WHERE id = ? AND usuario_cpf = ?";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('ssis', $titulo, $descricao, $id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Denúncia atualizada com sucesso!";
            header("Location: denuncia_listar.php");
            exit();
        } else {
            $error = "Erro ao atualizar a denúncia: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Denúncia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('img/denuncia_listar.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            color: #ecf0f1;
            padding: 0;
        }

        .container {
            background-color: rgba(44, 62, 80, 0.9);
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        h1 {
            text-align: center;
            color: #1abc9c;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], textarea {
            width: 96%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #34495e;
            border-radius: 5px;
            background-color: #ecf0f1;
            color: #2c3e50;
            resize: vertical;
            max-height: 500px;
        }

        button[type="submit"] {
            background-color: #2ecc71;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #27ae60;
        }

        .endereco {
            padding: 10px;
            margin-bottom: 15px;
            background-color: black;
            color: #fff;
            border-radius: 5px;
            text-align: center;
        }

        .status {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f39c12;
            color: #fff;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .msg {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
        }

        .msg-success {
            background-color: #2ecc71;
            color: white;
        }

        .msg-error {
            background-color: #e74c3c;
            color: white;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #fff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container{
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-edit"></i> Editar Denúncia</h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="msg msg-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <label><i class="fas fa-tag"></i> Título:</label>
        <input type="text" name="titulo" value="<?php echo htmlspecialchars($row['titulo']); ?>" required maxlength="45">

        <label><i class="fas fa-pencil-alt"></i> Descrição:</label>
        <textarea name="descricao" required maxlength="2499"><?php echo htmlspecialchars($row['descricao']); ?></textarea>

        <label><i class="fas fa-map-marker-alt"></i> Endereço (CEP):</label>
        <div class="endereco"><?php echo htmlspecialchars($row['endereco_cep']); ?></div>

        <label><i class="fas fa-exclamation-circle"></i> Status:</label>
        <div class="status"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></div>

        <button type="submit"><i class="fas fa-redo-alt"></i> Atualizar Denúncia</button>
    </form>

    <a href="denuncia_listar.php"><i class="fas fa-arrow-left"></i> Voltar para Lista</a>
</div>

</body>
</html>