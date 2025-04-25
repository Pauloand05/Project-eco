<?php
// Incluir o arquivo de conexão com o banco de dados
include 'db_connection.php';

// Pega o ID da denúncia da URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para pegar os detalhes da denúncia
    $sql = "SELECT denuncia.*, usuario.nome AS usuario_nome, endereco.estado, endereco.cidade, endereco.bairro, endereco.logradouro 
            FROM denuncia
            JOIN usuario ON denuncia.usuario_cpf = usuario.cpf
            JOIN endereco ON denuncia.endereco_cep = endereco.cep
            WHERE denuncia.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $denuncia = $result->fetch_assoc();
    } else {
        echo "Denúncia não encontrada.";
        exit;
    }

    // Verifica se foi enviado o pedido para aplicar atendimento
    if (isset($_POST['atendimento'])) {
        // Atualiza o status da denúncia para 'em atendimento'
        $update_sql = "UPDATE denuncia SET status = 'em atendimento' WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $id);
        $update_stmt->execute();
        $update_stmt->close();

        // Redireciona para a página de gerenciamento após a ação
        header('Location: denuncias_gerenciar.php');
        exit;
    }

    // Verifica se foi enviado o pedido para excluir a denúncia
    if (isset($_POST['excluir'])) {
        // Exclui a denúncia
        $delete_sql = "DELETE FROM denuncia WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Redireciona para a página de gerenciamento após a exclusão
        header('Location: denuncias_gerenciar.php');
        exit;
    }

    // Verifica se foi enviado o pedido para definir a prioridade
    if (isset($_POST['prioridade'])) {
        $prioridade = $_POST['prioridade'];

        // Validação da prioridade
        if (in_array($prioridade, ['alta', 'media', 'baixa'])) {
            // Atualiza o campo de prioridade da denúncia
            $sql = "UPDATE denuncia SET prioridade = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $prioridade, $id);
            $stmt->execute();
            $stmt->close();

            // Redireciona para a página de detalhes após definir a prioridade
            header('Location: ver_denuncia.php?id=' . $id);
            exit;
        } else {
            echo "Prioridade inválida.";
            exit;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID de denúncia inválido.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Denúncia</title>
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
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Melhorando o visual dos botões */
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .btn-atendimento {
            background-color: #28a745;
            color: white;
            margin-right: 10px;
        }

        .btn-atendimento:hover {
            background-color: #218838;
        }

        .btn-excluir {
            background-color: #dc3545;
            color: white;
            margin-right: 10px;
        }

        .btn-excluir:hover {
            background-color: #c82333;
        }

        .btn-prioridade {
            background-color: #ffc107;
            color: white;
        }

        .btn-prioridade:hover {
            background-color: #e0a800;
        }

        .btn-definir {
            background-color: #007bff;
            color: white;
        }

        .btn-definir:hover {
            background-color: #0056b3;
        }

        select {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                width: 100%;
                padding: 15px;
            }

            h1 {
                font-size: 24px;
            }

            table, th, td {
                font-size: 14px;
            }

            button, select {
                width: 100%;
                margin-top: 10px;
            }

            .btn-atendimento, .btn-excluir, .btn-definir, .btn-prioridade {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detalhes da Denúncia</h1>
        <p><strong>Título:</strong> <?php echo $denuncia['titulo']; ?></p>
        <p><strong>Descrição:</strong> <?php echo nl2br($denuncia['descricao']); ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($denuncia['status']); ?></p>
        <p><strong>Usuário:</strong> <?php echo $denuncia['usuario_nome']; ?></p>
        <p><strong>Endereço:</strong> <?php echo $denuncia['logradouro'] . ', ' . $denuncia['bairro'] . ', ' . $denuncia['cidade'] . ' - ' . $denuncia['estado']; ?></p>

        <div class="actions">
            <form method="POST">
                <!-- Botão de Aplicar Atendimento -->
                <?php if ($denuncia['status'] != 'em atendimento') { ?>
                    <button class="btn-atendimento" type="submit" name="atendimento">Aplicar Atendimento</button>
                <?php } ?>

                <!-- Botão de Excluir Denúncia -->
                <button type="submit" name="excluir" class="btn-excluir" onclick="return confirm('Você tem certeza que deseja excluir esta denúncia?')">Excluir Denúncia</button>

                <!-- Seleção de Prioridade -->
                <select name="prioridade" class="btn-prioridade">
                    <option value="alta" <?php echo ($denuncia['prioridade'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
                    <option value="media" <?php echo ($denuncia['prioridade'] == 'media') ? 'selected' : ''; ?>>Média</option>
                    <option value="baixa" <?php echo ($denuncia['prioridade'] == 'baixa') ? 'selected' : ''; ?>>Baixa</option>
                </select>
                <button type="submit" name="prioridade" class="btn-definir">Definir Prioridade</button>
            </form>
        </div>

        <a href="denuncias_gerenciar.php" class="btn-definir">Voltar</a>
    </div>
</body>
</html>