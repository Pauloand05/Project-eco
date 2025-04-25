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

// Conectar ao banco de dados e buscar o nível de acesso do administrador
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT hierarquia FROM hierarquia WHERE admin_codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_codigo);
$stmt->execute();
$stmt->bind_result($hierarquia);
$stmt->fetch();
$stmt->close();
$conn->close();

// Caminho para a pasta principal de arquivos
$diretorio_arquivos = "C:/xampp/htdocs/eco/uploads/";

// Determinar quais pastas mostrar de acordo com o nível de acesso
$pastas_visiveis = [];
if ($hierarquia >= 1) {
    $pastas_visiveis[] = '1';  // Nível 1 pode ver até a pasta 1
}
if ($hierarquia >= 2) {
    $pastas_visiveis[] = '2';  // Nível 2 pode ver até a pasta 2
}
if ($hierarquia >= 3) {
    $pastas_visiveis[] = '3';  // Nível 3 pode ver até a pasta 3
}
if ($hierarquia >= 4) {
    $pastas_visiveis[] = '4';  // Nível 4 pode ver até a pasta 4
}
if ($hierarquia >= 5) {
    $pastas_visiveis[] = '5';  // Nível 5 pode ver até a pasta 5
}

// Exibir as pastas visíveis para o administrador
$arquivos = [];
foreach ($pastas_visiveis as $pasta) {
    // Caminho completo para a pasta
    $caminho_pasta = $diretorio_arquivos . $pasta;
    
    // Verificar se a pasta existe
    if (is_dir($caminho_pasta)) {
        $arquivos[$pasta] = scandir($caminho_pasta);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Relatórios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2em;
            color: #333;
        }

        .pasta {
            margin-bottom: 20px;
        }

        .pasta h2 {
            font-size: 1.5em;
            color: #007BFF;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #f8f8f8;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Relatórios Disponíveis</h1>
        <!-- Botão Voltar -->
        <a href="index.php" class="back-btn">
            <button type="button">Voltar</button>
        </a>
    <?php if (empty($arquivos)): ?>
        <p>Não há relatórios disponíveis para este nível de acesso.</p>
    <?php else: ?>
        <?php foreach ($arquivos as $pasta => $lista_arquivos): ?>
            <div class="pasta">
                <h2>Pasta <?php echo $pasta; ?></h2>
                <ul>
                    <?php foreach ($lista_arquivos as $arquivo): ?>
                        <?php if ($arquivo != '.' && $arquivo != '..'): ?>
                            <li>
                                <a href="<?php echo urlencode($diretorio_arquivos . $pasta . '/' . $arquivo); ?>" target="_blank">
                                    <?php echo $arquivo; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>