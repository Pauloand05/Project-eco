<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Capturar o ID do jogo de maneira segura
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $jogo_id = intval($_GET['id']); // Garantir que o id seja um número
} else {
    echo "ID do jogo inválido!";
    exit();
}

// Buscar detalhes do jogo
$sql_jogo = "SELECT * FROM jogos WHERE id = ?";
$stmt_jogo = $conn->prepare($sql_jogo);
$stmt_jogo->bind_param("i", $jogo_id);
$stmt_jogo->execute();
$jogo_result = $stmt_jogo->get_result();
$jogo = $jogo_result->fetch_assoc();

// Verificar se o formulário de avaliação foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['avaliacao']) && isset($_POST['comentario'])) {
        $avaliacao = $_POST['avaliacao'];
        $comentario = $_POST['comentario'];
        $usuario_cpf = $_SESSION['user_id']; // Alterado para 'usuario_cpf'

        // Verificar se o usuário já fez uma avaliação para este jogo
        $sql_check = "SELECT * FROM avaliacoes WHERE jogos_id = ? AND usuario_cpf = ?"; // Usando 'usuario_cpf'
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("is", $jogo_id, $usuario_cpf);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows == 0) {
            // Inserir nova avaliação se o usuário nunca avaliou
            $sql_avaliacao = "INSERT INTO avaliacoes (jogos_id, usuario_cpf, avaliacao, comentario) VALUES (?, ?, ?, ?)";
            $stmt_avaliacao = $conn->prepare($sql_avaliacao);
            $stmt_avaliacao->bind_param("isis", $jogo_id, $usuario_cpf, $avaliacao, $comentario); // Alterado para 'usuario_cpf'
            $stmt_avaliacao->execute();
            $stmt_avaliacao->close();
            echo "Avaliação enviada com sucesso!";
        } else {
            // Caso contrário, atualiza a avaliação existente
            $sql_update = "UPDATE avaliacoes SET avaliacao = ?, comentario = ? WHERE jogos_id = ? AND usuario_cpf = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sisi", $avaliacao, $comentario, $jogo_id, $usuario_cpf); // Alterado para 'usuario_cpf'
            $stmt_update->execute();
            $stmt_update->close();
            echo "Avaliação atualizada com sucesso!";
        }
    }
}

// Deletar avaliação - Verificar se o botão de exclusão foi pressionado via POST
if (isset($_POST['delete']) && isset($_SESSION['user_id'])) {
    $avaliacao_id = $_POST['delete'];
    $usuario_id = $_SESSION['user_id']; // Usando 'user_id' para consistência

    // Verificar se a avaliação pertence ao usuário
    $sql_check = "SELECT * FROM avaliacoes WHERE id = ? AND usuario_cpf = ?"; 
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("is", $avaliacao_id, $usuario_id); // Garantir que a consulta esteja correta
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        // Excluir avaliação
        $sql_delete = "DELETE FROM avaliacoes WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $avaliacao_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        echo "Avaliação excluída com sucesso!";

        // Redirecionar para atualizar os detalhes do jogo
        header("Location: jogos_detalhes.php?id=$jogo_id");
        exit();
    } else {
        echo "Você não tem permissão para excluir esta avaliação.";
    }
}

// Buscar avaliações para o jogo
$sql_avaliacoes = "SELECT a.id, a.avaliacao, a.comentario, a.usuario_cpf, u.nome AS usuario_nome 
FROM avaliacoes a 
JOIN usuario u ON a.usuario_cpf = u.cpf 
WHERE a.jogos_id = ?";
$stmt_avaliacoes = $conn->prepare($sql_avaliacoes);
$stmt_avaliacoes->bind_param("i", $jogo_id);
$stmt_avaliacoes->execute();
$avaliacoes_result = $stmt_avaliacoes->get_result();

// Calcular a média da avaliação
$sql_media = "SELECT AVG(avaliacao) AS media FROM avaliacoes WHERE jogos_id = ?";
$stmt_media = $conn->prepare($sql_media);
$stmt_media->bind_param("i", $jogo_id);
$stmt_media->execute();
$media_result = $stmt_media->get_result();
$media = $media_result->fetch_assoc()['media'];

// Exibir detalhes do jogo
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Jogo</title>
    <style>
        /* Estilos para a página */
        body {
            background-color: #2c3e50;
            color: #ecf0f1;
            font-family: Arial, sans-serif;
        }

        .game-details {
            background-color: #34495e;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            max-width: 800px;
            margin: 35px auto;
        }

        .game-details h1 {
            color: #1abc9c;
            font-size: 24px;
        }

        .game-details p {
            color: #bdc3c7;
            font-size: 16px;
        }

        .game-details h3 {
            color: #1abc9c;
            font-size: 18px;
            margin-top: 20px;
        }

        .game-details .form-container {
            background-color: #2d3e50;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }

        .game-details .form-container input,
        .game-details .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #bdc3c7;
            background-color: #34495e;
            color: #ecf0f1;
        }

        button {
            background-color: #1abc9c;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #16a085;
        }

        .stars {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .star {
            font-size: 30px;
            color: #bdc3c7;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }

        .star.selected {
            color: #f39c12;
        }

        .star.hover {
            color: #f39c12;
        }

        .user-review {
            background-color: #16a085;
            padding: 10px;
            margin-top: 20px;
            border-radius: 8px;
        }

        .user-review a {
            color: white;
            text-decoration: none;
            padding: 5px;
            border-radius: 5px;
            background-color: #e67e22;
            margin-top: 5px;
            margin-left: 10px;
        }

        .user-review a:hover {
            background-color: #d35400;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li strong {
            color: #1abc9c;
            font-size: 18px;
        }

        li em {
            color: #bdc3c7;
            font-style: normal;
            font-size: 14px;
        }

        h3 {
            font-size: 18px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="game-details">
    <h1>Detalhes do Jogo: <?php echo htmlspecialchars($jogo['nome']); ?></h1>
    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($jogo['genero']); ?></p>
    <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($jogo['descricao'])); ?></p>
    <p><strong>Data de Lançamento:</strong> <?php echo date("d/m/Y", strtotime($jogo['data_lancamento'])); ?></p>

    <h3>Média de Avaliação: 
        <?php echo ($media) ? number_format($media, 2) : "Nenhuma avaliação ainda"; ?>
    </h3>

    <?php
    // Verifica se o usuário já fez uma avaliação para o jogo
    $user_id = $_SESSION['user_id'];
    $sql_user_review = "SELECT * FROM avaliacoes WHERE jogos_id = ? AND usuario_cpf = ?";
    $stmt_user_review = $conn->prepare($sql_user_review);
    $stmt_user_review->bind_param("ii", $jogo_id, $user_id);
    $stmt_user_review->execute();
    $user_review_result = $stmt_user_review->get_result();
    $user_review = $user_review_result->fetch_assoc();
    ?>

    <?php if (!$user_review): ?>
        <div class="form-container">
            <form method="POST" action="jogos_detalhes.php?id=<?php echo $jogo_id; ?>">
                <h3>Deixe sua Avaliação:</h3>

                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?php echo $i; ?>">&#9733;</span>
                    <?php endfor; ?>
                </div>

                <textarea name="comentario" placeholder="Deixe seu comentário..." rows="4" required></textarea>
                <input type="hidden" name="avaliacao" id="avaliacao">

                <button type="submit">Enviar Avaliação</button>
            </form>
        </div>
    <?php else: ?>
        <div class="user-review">
            <h3>Sua Avaliação:</h3>
            <p><strong>Avaliação:</strong> <?php echo $user_review['avaliacao']; ?>/5</p>
            <p><strong>Comentário:</strong> <?php echo nl2br(htmlspecialchars($user_review['comentario'])); ?></p>

            <!-- Formulário para excluir a avaliação -->
            <form action="jogos_detalhes.php?id=<?php echo $jogo_id; ?>" method="POST">
                <input type="hidden" name="delete" value="<?php echo $user_review['id']; ?>"> <!-- ID da avaliação -->
                <button type="submit" name="delete_button">Excluir Avaliação</button>
            </form>
        </div>
    <?php endif; ?>
    <h3>Avaliações dos Usuários</h3>
    <ul>
        <?php while ($avaliacao = $avaliacoes_result->fetch_assoc()): ?>
            <li>
                <strong><?php echo htmlspecialchars($avaliacao['usuario_nome']); ?>:</strong>
                <em><?php echo nl2br(htmlspecialchars($avaliacao['comentario'])); ?></em>
                <br>
                <small>Avaliação: <?php echo $avaliacao['avaliacao']; ?>/5</small>
                <br>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<script>
    // Interatividade para as estrelas de avaliação
    const stars = document.querySelectorAll('.star');
    const hiddenInput = document.getElementById('avaliacao');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.getAttribute('data-value');
            hiddenInput.value = value;
            updateStarClasses(value);
        });

        star.addEventListener('mouseover', () => {
            const value = star.getAttribute('data-value');
            updateStarClasses(value);
        });

        star.addEventListener('mouseout', () => {
            const value = hiddenInput.value;
            updateStarClasses(value);
        });
    });

    function updateStarClasses(value) {
        stars.forEach(star => {
            const starValue = star.getAttribute('data-value');
            if (starValue <= value) {
                star.classList.add('hover');
            } else {
                star.classList.remove('hover');
            }
        });
    }
</script>

<?php include 'footer.php'; ?>

</body>
</html>