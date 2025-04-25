<?php
// Incluir o arquivo de conexão com o banco de dados
include 'db_connection.php';

// Consulta para pegar todas as denúncias
$sql = "SELECT denuncia.id, denuncia.titulo, denuncia.status, usuario.nome AS usuario_nome 
        FROM denuncia 
        JOIN usuario ON denuncia.usuario_cpf = usuario.cpf";

$result = $conn->query($sql);

// Verifica se há denúncias
if ($result->num_rows > 0) {
    $denuncias = $result->fetch_all(MYSQLI_ASSOC); // Pega todas as denúncias em um array associativo
} else {
    $denuncias = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Denúncias</title>
    <style>
        /* Resetando o padding e margin para garantir que o layout seja consistente */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn-ver {
            display: inline-block;
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn-ver:hover {
            background-color: #218838;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            table {
                width: 100%;
                overflow-x: auto;
                display: block;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }

            h1 {
                font-size: 24px;
            }

            .container {
                padding: 20px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciador de Denúncias</h1>
        <!-- Botão Voltar -->
        <a href="index.php" class="back-btn">
                <button type="button">Voltar</button>
        </a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($denuncias)) { ?>
                    <?php foreach ($denuncias as $denuncia) { ?>
                        <tr>
                            <td><?php echo $denuncia['id']; ?></td>
                            <td><?php echo $denuncia['titulo']; ?></td>
                            <td><?php echo $denuncia['usuario_nome']; ?></td>
                            <td><?php echo ucfirst($denuncia['status']); ?></td>
                            <td>
                                <a href="ver_denuncia.php?id=<?php echo $denuncia['id']; ?>" class="btn-ver">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">Não há denúncias registradas.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>