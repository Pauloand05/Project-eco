<?php
include 'conexao.php';  // Conexão com o banco de dados

// Variável para o filtro de pesquisa (bairro ou cep)
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

// Validar pesquisa (permitir apenas caracteres alfanuméricos e espaços)
if (!empty($pesquisa) && !preg_match("/^[a-zA-Z0-9\s,]*$/", $pesquisa)) {
    echo "Pesquisa inválida!";
    exit;
}

// Inicializar a consulta SQL para buscar os endereços
$sql_endereco = "SELECT * FROM eco.endereco WHERE 1=1";

// Adicionar a condição de pesquisa com LIKE (permite busca por parte do valor)
if (!empty($pesquisa)) {
    // Adicionar pesquisa de bairro e cep usando LIKE, permitindo buscas parciais
    $sql_endereco .= " AND (bairro LIKE ? OR cep LIKE ?)";
}

// Preparar a consulta para os endereços
$stmt_endereco = $conn->prepare($sql_endereco);

// Vincular o parâmetro à consulta (usando '%' para busca parcial)
if (!empty($pesquisa)) {
    // Bind dos parâmetros para pesquisa de bairro ou cep (usando '%' para permitir a busca parcial)
    $param = "%" . $pesquisa . "%";  // Adiciona o '%' no início e fim para permitir a busca parcial
    $stmt_endereco->bind_param("ss", $param, $param);
}

// Executar a consulta
$stmt_endereco->execute();

// Obter os resultados da consulta de endereços
$result_endereco = $stmt_endereco->get_result();
?>

<!-- Estilos CSS para o layout -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #121212; /* Cor de fundo escura */
        color: #f0f0f0; /* Texto claro */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .container {
        width: 90%;
        max-width: 1200px;
        margin: 50px auto;
        background-color: #1e1e1e; /* Cor de fundo mais escura para a caixa */
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra suave */
        text-align: center;
    }

    h3 {
        font-size: 28px;
        color: #e0e0e0; /* Texto de cabeçalho mais claro */
        margin-bottom: 30px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Formulário de Pesquisa */
    .form-container {
        margin-bottom: 40px;
    }

    form {
        display: flex;
        justify-content: center;
        gap: 15px; /* Diminui o espaço entre os campos */
        margin-bottom: 40px;
    }

    label {
        font-size: 16px;
        color: #e0e0e0; /* Texto do rótulo em claro */
    }

    input[type="text"] {
        padding: 10px;
        font-size: 14px; /* Diminui o tamanho da fonte */
        border-radius: 8px; /* Borda mais suave */
        border: 2px solid #444; /* Borda em cinza escuro */
        background-color: #333; /* Fundo escuro para os campos de texto */
        color: #fff; /* Texto claro */
        width: 50%; /* Reduz a largura da barra de pesquisa */
        outline: none;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus {
        border-color: #4CAF50; /* Cor do foco */
    }

    button {
        background-color: #4CAF50;
        color: #fff;
        padding: 10px 20px; /* Diminui o padding do botão */
        border: none;
        border-radius: 8px; /* Borda mais suave */
        cursor: pointer;
        font-size: 14px; /* Diminui o tamanho da fonte */
        transition: background-color 0.3s ease, transform 0.2s ease-in-out;
    }

    button:hover {
        background-color: #45a049;
        transform: scale(1.05);
    }

    button[type="reset"] {
        background-color: #f44336;
    }

    button[type="reset"]:hover {
        background-color: #e53935;
    }

    /* Tabela de Resultados */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
        background-color: #1e1e1e; /* Fundo escuro para a tabela */
        border-radius: 10px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    }

    table th, table td {
        padding: 16px;
        text-align: left;
        font-size: 18px;
        color: #f0f0f0; /* Texto claro */
        border-bottom: 1px solid #444; /* Borda escura */
    }

    table th {
        background-color: #333; /* Fundo escuro para o cabeçalho da tabela */
        color: #e0e0e0; /* Texto claro para cabeçalho */
        text-transform: uppercase;
        font-weight: bold;
    }

    table tr:nth-child(even) {
        background-color: #2a2a2a; /* Fundo alternado em cinza escuro */
    }

    table tr:hover {
        background-color: #333; /* Fundo mais claro ao passar o mouse */
        cursor: pointer;
    }

    /* Tabela de Horários */
    .horarios-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .horarios-table th, .horarios-table td {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #444;
        text-align: left;
    }

    .horarios-table th {
        background-color: #333;
        color: #e0e0e0;
        font-weight: bold;
    }

    .horarios-table tr:nth-child(even) {
        background-color: #2a2a2a;
    }

    .horarios-table tr:hover {
        background-color: #333;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        form {
            flex-direction: column;
        }

        input[type="text"], button {
            width: 100%;
            margin-bottom: 10px;
        }

        .container {
            padding: 20px;
        }

        table th, table td {
            font-size: 16px;
        }

        .horarios-table th, .horarios-table td {
            font-size: 14px;
        }
    }
</style>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="form-container">
            <!-- Formulário de Pesquisa -->
            <form action="coleta_tabela.php" method="GET">
                <label for="pesquisa">Pesquisar Bairro ou CEP:</label>
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Digite o nome do bairro ou CEP" value="<?php echo htmlspecialchars($pesquisa); ?>" />
                <button type="submit">Buscar</button>
                <button type="reset" onclick="window.location.href='coleta_tabela.php'">Limpar</button>
            </form>
        </div>

        <?php
        // Exibir os resultados da pesquisa de endereços (apenas se houver pesquisa)
        if (!empty($pesquisa)) {
            if ($result_endereco->num_rows > 0) {
                echo "<h3>Endereços Encontrados:</h3>";
                echo "<table>
                        <tr>
                            <th>CEP</th>
                            <th>Bairro</th>
                            <th>Horários de Coleta</th>
                        </tr>";

                // Para cada endereço encontrado, buscar os horários de coleta
                while ($row_endereco = $result_endereco->fetch_assoc()) {
                    $cep_endereco = $row_endereco["cep"];
                    $bairro_endereco = $row_endereco["bairro"];

                    // Buscar os horários de coleta associados a esse CEP
                    $sql_horarios = "SELECT * FROM horarios_coleta WHERE endereco_cep = ?";
                    $stmt_horarios = $conn->prepare($sql_horarios);
                    $stmt_horarios->bind_param("s", $cep_endereco);
                    $stmt_horarios->execute();
                    $result_horarios = $stmt_horarios->get_result();

                    echo "<tr>
                            <td>" . $cep_endereco . "</td>
                            <td>" . $bairro_endereco . "</td>
                            <td>";

                    // Exibe os horários de coleta, agora com uma tabela de horários organizada
                    if ($result_horarios->num_rows > 0) {
                        echo "<table class='horarios-table'>
                                <tr>
                                    <th>Dia da Semana</th>
                                    <th>Turno</th>
                                </tr>";

                        while ($row_horarios = $result_horarios->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . ucfirst($row_horarios["dia_semana"]) . "</td>
                                    <td>" . ucfirst($row_horarios["turno"]) . "</td>
                                  </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Nenhum horário encontrado.";
                    }

                    echo "</td></tr>";
                }

                echo "</table>";
            } else {
                echo "<p class='no-results'>Nenhum endereço encontrado para a pesquisa informada.</p>";
            }
        }

        // Fechar a conexão
        $stmt_endereco->close();
        $conn->close();
        ?>
    </div>

    <?php include 'footer.php'; ?>
</body>