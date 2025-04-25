<?php
session_start();
include 'conexao.php'; // Inclui o arquivo de conexão

// Função para redirecionar com mensagem
function redirectWithMessage($message, $redirectUrl = 'denuncia_listar.php') {
    $_SESSION['message'] = $message;
    header("Location: $redirectUrl");
    exit();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    redirectWithMessage("Você precisa estar logado para acessar essa página.", "login.php");
}

// Obtém o CPF do usuário da sessão
$user_id = $_SESSION['user_id']; // Assegure-se de que este valor é o CPF

// Paginação
$results_per_page = 10; // Resultados por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Busca o total de denúncias do usuário
$total_sql = "SELECT COUNT(*) FROM eco.denuncia WHERE usuario_cpf = ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("s", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);

// Consulta as denúncias com a limitação de resultados por página
$sql = "SELECT id, titulo, data_criacao, status FROM eco.denuncia WHERE usuario_cpf = ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $user_id, $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Limitação de exibição de páginas no controle de navegação
$max_links = 5; // Número máximo de links para exibir (antes e depois da página atual)
$start_page = max(1, $page - $max_links); // Página inicial do intervalo
$end_page = min($total_pages, $page + $max_links); // Página final do intervalo
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Denúncias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('img/denuncia_listar.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            color: #ecf0f1;
        }

        .minhas-denuncias {
            background-color: rgba(44, 62, 80, 0.9);
            padding: 20px;
            width: 1000px;
            margin: 108px auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        h1 {
            text-align: center;
            color: #1abc9c;
            margin-bottom: 20px;
            font-size: 32px; /* Tamanho maior para h1 */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }

        th {
            background-color: #34495e;
            color: #ecf0f1;
        }

        tr:hover {
            background-color: #3f566e;
        }

        .button {
            display: inline-block;
            text-align: center;
            margin: 5px;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .button-vermelho {
            background-color: #e74c3c;
            color: #fff;
        }

        .button-verde {
            background-color: #2ecc71;
            color: #fff;
        }

        .button-laranja {
            background-color: #e67e22;
            color: #fff;
        }

        .button:hover {
            opacity: 0.8;
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            .minhas-denuncias {
                max-width: 90%;
            }

            h1 {
                font-size: 28px; /* Ajusta tamanho do título */
            }

            table {
                font-size: 14px; /* Ajusta o tamanho das células da tabela */
            }

            .button {
                padding: 8px 12px; /* Ajusta os botões para telas menores */
                font-size: 14px;
            }
        }

        @media (max-width: 900px) {
            .minhas-denuncias {
                max-width: 90%; /* Flexível em telas ainda menores */
                margin: 20px;
            }

            h1 {
                font-size: 24px; /* Menor tamanho para dispositivos móveis */
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px; /* Menos espaço nas células */
            }

            .button {
                padding: 8px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 600px) {
            .minhas-denuncias {
                max-width: 90%;
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 6px; /* Reduzindo o espaçamento */
            }

            .button {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        /* Mensagem de sucesso ou erro */
        .msg {
            text-align: center;
            margin: 10px 0;
            color: yellow;
            font-size: 18px;
            font-weight: bold;
        }

        .msg-success {
            background-color: #2ecc71;
            color: white;
        }

        .msg-error {
            background-color: #e74c3c;
            color: white;
        }

        .msg-info {
            background-color: #f39c12;
            color: white;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap; /* Permite que os itens da página "quebrem" para a próxima linha se necessário */
        }

        .pagination a {
            color: #1abc9c;
            margin: 0 5px;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px; /* Tamanho ajustado */
        }

        .pagination a:hover {
            background-color: #1abc9c;
            color: #fff;
        }

        .pagination .current-page {
            background-color: #2ecc71;
            color: #fff;
        }

        .pagination .disabled {
            color: #ccc;
            pointer-events: none;
        }

        /* Responsividade para dispositivos menores */
        @media (max-width: 600px) {
            .minhas-denuncias{
                max-width: 90%;
            }
            .pagination {
                font-size: 12px; /* Reduz o tamanho da fonte na navegação */
                margin-top: 10px;
                justify-content: space-between; /* Espalha os links de paginação */
            }

            .pagination a {
                padding: 5px 10px;
                font-size: 12px; /* Ajuste de tamanho para links */
            }

            /* Limite o número de links visíveis */
            .pagination a {
                display: inline-block;
                min-width: 24px; /* Garante que os botões de página tenham largura mínima */
            }

            .pagination .current-page {
                background-color: #e67e22;
                color: white;
            }

            /* Oculta os links se eles não são necessários */
            .pagination a.disabled {
                display: none;
            }
        }

        /* Ajustes adicionais para telas grandes */
        @media (max-width: 900px) {
            .minhas-denuncias{
                max-width: 90%;
            }
            .pagination a {
                padding: 6px 8px;
            }
            .pagination {
                font-size: 14px; /* Para dispositivos com tela entre 600px e 900px */
            }
        }
        /* Cores para status */
        .status-pendente {
            font-weight: bold;
            color: #f39c12;
        }

        .status-em {
            font-weight: bold;
            color: lightblue;
        }

        .status-concluida {
            font-weight: bold;
            color: #2ecc71;
        }

        .status-rejeitada {
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="minhas-denuncias">
    <h1><i class="fas fa-exclamation-triangle"></i> Minhas Denúncias</h1>

    <!-- Exibe mensagem de sucesso ou erro -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="msg <?php echo ($_SESSION['msg_type'] ?? 'msg-info'); ?>">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
        </div>
    <?php endif; ?>

    <a href="denuncia_create.php" class="button button-verde"><i class="fas fa-plus-circle"></i> Criar Denúncia</a>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td><?php echo (new DateTime($row['data_criacao']))->format('d/m/Y'); ?></td>
                        <td class="status-<?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="denuncia_editar.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="button button-laranja"><i class="fas fa-edit"></i> Editar</a>
                            <?php if ($row['status'] == 'pendente'): ?>
                                <form action="denuncia_delete.php" method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" class="button button-vermelho" onclick="return confirm('Tem certeza que deseja deletar esta denúncia?');"><i class="fas fa-trash-alt"></i> Deletar</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #e67e22;"> </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhuma denúncia encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="denuncia_listar.php?page=<?php echo $page - 1; ?>">Anterior</a>
        <?php endif; ?>

        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="denuncia_listar.php?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'current-page' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="denuncia_listar.php?page=<?php echo $page + 1; ?>">Próxima</a>
        <?php endif; ?>
    </div>

    <a href="perfil.php" class="button button-verde"><i class="fas fa-arrow-left"></i> Voltar para o Perfil</a>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

<?php 
// Fecha a declaração e a conexão
$stmt->close(); 
$conn->close(); 
?>