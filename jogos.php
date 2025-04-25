<?php
session_start();
include 'conexao.php'; // Inclui o arquivo de conexão

// Definindo variáveis de página e filtro de gênero
$limite_por_pagina = 12; // Número de jogos por página
$pagina_atual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1; // Página atual (garante que seja um inteiro)
$offset = ($pagina_atual - 1) * $limite_por_pagina; // Calcula o deslocamento para a consulta

// Verificar se um filtro de gênero foi aplicado
$genero_filtro = isset($_GET['genero']) ? htmlspecialchars($_GET['genero'], ENT_QUOTES, 'UTF-8') : '';

// Consulta SQL para contar o número total de jogos com o filtro de gênero
$sql_count = "SELECT COUNT(*) AS total FROM jogos";
if ($genero_filtro != '') {
    $sql_count .= " WHERE genero = ?";
}
$stmt_count = $conn->prepare($sql_count);
if ($genero_filtro != '') {
    $stmt_count->bind_param("s", $genero_filtro);
}
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_jogos = $count_result->fetch_assoc()['total'];
$stmt_count->close();

// Consulta SQL para pegar os jogos com o filtro de gênero e paginação
$sql = "SELECT * FROM jogos";
if ($genero_filtro != '') {
    $sql .= " WHERE genero = ?";
}
$sql .= " LIMIT ? OFFSET ?"; // Limita os resultados e aplica o offset
$stmt = $conn->prepare($sql);
if ($genero_filtro != '') {
    $stmt->bind_param("sii", $genero_filtro, $limite_por_pagina, $offset);
} else {
    $stmt->bind_param("ii", $limite_por_pagina, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogos</title>
    <style>
        /* Estilos para a página de jogos */
        body {
            font-family: Arial, sans-serif;
            background-color: #2c3e50; /* Fundo escuro */
            color: #ecf0f1; /* Texto claro */
        }

        .jogos-page {
            padding: 30px;
            max-width: 90%;
            margin:50px auto; /* Centraliza o conteúdo */
            background-color: #34495e;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .jogos-page h1 {
            text-align: center;
            color: #1abc9c;
            margin: 0;
        }

        /* Filtro de Gênero - Estilo Ajustado */
        .filter-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin: 20px;
        }

        .filter-container label {
            margin-right: 10px;
            color: #ecf0f1;
        }

        .filter-container select {
            padding: 10px;
            background-color: #2d3e50;
            border: none;
            color: #ecf0f1;
            font-size: 1rem;
            border-radius: 5px;
        }

        .jogos-page .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px;
        }

        .card {
            background-color: #2d3e50;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
        }

        .card h2 {
            color: #ecf0f1;
            font-size: 1.5rem;
        }

        .card p {
            color: #bdc3c7;
            font-size: 1rem;
            margin: 5px 0;
        }

        .card a {
            color: #ff6347;
            text-decoration: none;
            font-weight: bold;
        }

        .card a:hover {
            text-decoration: underline;
            color: #e67e22;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .card .jogar-btn {
            display: block;
            background-color: #1abc9c;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
        }

        .card .jogar-btn:hover {
            background-color: #16a085;
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .jogos-page {
                padding: 15px;
            }

            .card {
                padding: 15px;
            }
        }

        /* Estilos para Paginação */
        .pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
            gap: 10px;
            overflow-x: auto;
            max-width: 100%;
            padding-bottom: 20px;
        }

        .pagination a {
            margin: 0 5px;
            color: #1abc9c;
            text-decoration: none;
            font-weight: normal;
        }

        .pagination a:hover {
            color: #e67e22;
        }

        .pagination .active {
            font-weight: bold;
            color: #e67e22;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<br><br>

<div class="jogos-page">
    <h1>Lista de Jogos</h1>

    <!-- Filtro de Gênero -->
    <div class="filter-container">
        <label for="genero">Filtrar por Gênero:</label>
        <form action="jogos.php" method="GET">
            <select id="genero" name="genero" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="Aventura" <?php if ($genero_filtro == 'Aventura') echo 'selected'; ?>>Aventura</option>
                <option value="Estratégia" <?php if ($genero_filtro == 'Estratégia') echo 'selected'; ?>>Estratégia</option>
                <option value="Simulação" <?php if ($genero_filtro == 'Simulação') echo 'selected'; ?>>Simulação</option>
                <option value="RPG" <?php if ($genero_filtro == 'RPG') echo 'selected'; ?>>RPG</option>
                <option value="Ação" <?php if ($genero_filtro == 'Ação') echo 'selected'; ?>>Ação</option>
                <option value="Indie" <?php if ($genero_filtro == 'Indie') echo 'selected'; ?>>Indie</option>
            </select>
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="cards">
            <?php while ($jogo = $result->fetch_assoc()): ?>
                <div class="card">
                    <h2><?php echo htmlspecialchars($jogo['nome']); ?></h2>
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($jogo['genero']); ?></p>
                    <p><strong>Desenvolvedor:</strong> <?php echo htmlspecialchars($jogo['desenvolvedor']); ?></p>
                    <p><strong>Editor:</strong> <?php echo htmlspecialchars($jogo['editor']); ?></p>
                    <p><strong>Descrição:</strong><br>
                        <?php 
                            // Limita a descrição a 200 caracteres
                            $descricao = nl2br(htmlspecialchars($jogo['descricao'])); // Aplique nl2br para manter quebras de linha
                            if (strlen($descricao) > 200) {
                                $descricao = substr($descricao, 0, 200) . '...'; // Limita a 200 caracteres e adiciona '...'
                            }
                            echo $descricao;
                        ?>
                    </p>
                    <!-- Link para os detalhes -->
                    <a href="jogos_detalhes.php?id=<?php echo $jogo['id']; ?>">Ver Detalhes</a>

                    <!-- Botão Jogar -->
                    <?php if (filter_var($jogo['link_jogo'], FILTER_VALIDATE_URL)): ?>
                        <a href="<?php echo htmlspecialchars($jogo['link_jogo']); ?>" class="jogar-btn" target="_blank">Jogar</a>
                    <?php else: ?>
                        <p>Link inválido para o jogo</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Nenhum jogo encontrado.</p>
    <?php endif; ?>

    <!-- Paginação -->
    <div class="pagination">
        <?php
        $total_paginas = ceil($total_jogos / $limite_por_pagina);
        // Navegação de Paginação: links "Anterior" e "Próxima"
        if ($pagina_atual > 1) {
            $url_anterior = "jogos.php?pagina=" . ($pagina_atual - 1);
            if ($genero_filtro != '') {
                $url_anterior .= "&genero=" . urlencode($genero_filtro);
            }
            echo "<a href='$url_anterior' class='previous'>Anterior</a>";
        }

        // Links para as páginas
        for ($i = 1; $i <= $total_paginas; $i++) {
            $url = "jogos.php?pagina=$i";
            if ($genero_filtro != '') {
                $url .= "&genero=" . urlencode($genero_filtro);
            }
            $pagina_ativa = $i == $pagina_atual ? 'active' : '';
            echo "<a href='$url' class='$pagina_ativa'>$i</a>";
        }

        if ($pagina_atual < $total_paginas) {
            $url_proxima = "jogos.php?pagina=" . ($pagina_atual + 1);
            if ($genero_filtro != '') {
                $url_proxima .= "&genero=" . urlencode($genero_filtro);
            }
            echo "<a href='$url_proxima' class='next'>Próxima</a>";
        }
        ?>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>