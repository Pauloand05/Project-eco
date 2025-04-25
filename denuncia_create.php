<?php
session_start();
include 'conexao.php';
include_once "utils/api.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mensagem = ""; 

function enviaBancoDeDados($conn, $dados_usuario, $endereco_usuario) {
    $message = "";
    $endereco_id = $endereco_usuario['cep'];

    try {
        $sql_verifica_cep = "SELECT cep FROM endereco WHERE cep = ?";
        $stmt = $conn->prepare($sql_verifica_cep);
        $stmt->bind_param("s", $endereco_usuario['cep']);
        $stmt->execute();
        $stmt->store_result();
        
        // Caso o CEP já exista, recuperamos o ID do endereço
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($endereco_usuario['cep']);
            $stmt->fetch();
            $stmt->close();
        } else {
            // Se o CEP não existir, inserimos ele na tabela 'endereco'
            $sql_endereco_usuario = "INSERT INTO endereco (cep, estado, cidade, bairro, logradouro) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_endereco_usuario);
            $stmt->bind_param("sssss", 
                $endereco_usuario['cep'], 
                $endereco_usuario['estado'], 
                $endereco_usuario['localidade'], 
                $endereco_usuario['bairro'], 
                $endereco_usuario['logradouro']
            );

            if (!$stmt->execute()) {
                throw new Exception("Desculpe, houve um problema ao registrar o seu endereço. Parece que o CEP fornecido está comprometido ou é inválido. Verifique o CEP e tente novamente.");
            }

            $endereco_id = $endereco_usuario['cep'];
            $stmt->close();
        }

        $sql_usuario = "INSERT INTO denuncia (titulo, descricao, usuario_cpf, endereco_cep, anexo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_usuario);
        $stmt->bind_param("sssss",
            $dados_usuario['titulo'], 
            $dados_usuario['descricao'], 
            $dados_usuario['id'], 
            $endereco_id,  
            $dados_usuario['caminho_foto']
        );

        if (!$stmt->execute()) {
            throw new Exception("Desculpe, ocorreu um erro ao tentar registrar sua denúncia. Tente novamente mais tarde.");
        }
        $stmt->close();

        $message = "Sua denúncia foi enviada com sucesso!";
        echo "<script>
                alert('$message');
                window.location.href = 'denuncia_listar.php';
            </script>";
        exit;

    } catch (Exception $e) {
        
        if (!empty($dados_usuario['caminho_foto']) && file_exists($dados_usuario['caminho_foto'])) {
            deleteFile($dados_usuario['caminho_foto']);
        }
        $message = $e->getMessage();
        echo "<script>alert('$message');</script>";
    }

    return $message;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['user_id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $cep = $_POST['cep'];
    
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {

        $resultado_do_consulta_cep = consultaCep($cep);

        if (!$resultado_do_consulta_cep['success']){
            $mensagem = $resultado_do_consulta_cep['error'];
        }else {
            $resultado_do_upload = uploadImagem($_FILES['anexo']['tmp_name']);
            if (!$resultado_do_upload['success']) {
                $mensagem = $resultado_do_upload['error'];
            }else{
                $caminho_destinado = $resultado_do_upload['file_path'];
                $cep_sem_traco = str_replace('-', '', $cep);

                $resultado_do_consulta_cep['cep']['cep'] = $cep_sem_traco;
                $dados_usuario = [
                    "titulo" => $titulo,
                    "descricao" => $descricao,
                    "id" => $id_usuario,
                    "cep" => $cep_sem_traco,
                    "caminho_foto" => $caminho_destinado
                ];
                $resultado_do_envio_banco_de_dados = enviaBancoDeDados($conn, $dados_usuario, $resultado_do_consulta_cep['cep']);
                $mensagem = $resultado_do_envio_banco_de_dados;
               
            }
        }
        
    } else {
        $mensagem = "Erro: Nenhum arquivo enviado ou ocorreu um erro no upload.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Faça sua Denúncia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('img/denuncia_listar.jpg'); /* Imagem de fundo */
            background-size: cover; /* Cobrir toda a área */
            background-position: center; /* Centraliza a imagem */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 200px;
            color: #ecf0f1;
            display: flex; /* Usar flexbox para alinhar elementos */
            justify-content: center; /* Centraliza o conteúdo */
            align-items: center; /* Alinha verticalmente */
            height: 100vh; /* Altura total da tela */
        }

        .container {
            display: flex; /* Exibe o formulário e a imagem lado a lado */
            max-width: 1200px; /* Largura máxima da seção */
            width: 100%; /* Largura total */
            margin: 50px auto; /* Centraliza a seção na página */
            padding: 20px; /* Mais espaçamento */
            background-color: rgba(44, 62, 80, 0.9); /* Fundo da seção com transparência */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            color: #1abc9c; /* Verde claro */
            margin-bottom: 30px; /* Espaço abaixo do título */
        }
        .form-container {
            flex: 1; /* Faz o formulário ocupar o espaço disponível */
            padding: 30px; /* Mais espaçamento */
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: white;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            background-color: #fafafa;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #4caf50;
            outline: none;
            background-color: #fff;
        }
        .form-group  button[type="button"] {
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: auto; /* Define a largura como automática para ajustar ao conteúdo */
            max-width: 410px; /* Define uma largura máxima */
            margin: 0 auto; /* Centraliza o botão horizontalmente */
        }

        .form-group  button[type="button"] {
            background-color: #45a049;
        }

        label {
            display: block; /* Faz o label ocupar toda a linha */
            margin: 10px 0 5px; /* Espaçamento do label */
        }

        input[type="text"],
        textarea {
            resize: both; /* Permite redimensionar tanto horizontal quanto verticalmente */
        min-width: 100%; /* Largura mínima */
        max-width: 600px; /* Largura máxima */
        max-height: 200px; /* Altura máxima */
                padding: 10px; /* Espaçamento interno */
            margin-bottom: 15px; /* Espaço abaixo dos campos */
            border: 1px solid #34495e; /* Borda */
            border-radius: 5px; /* Bordas arredondadas */
            background-color: #ecf0f1; /* Fundo claro */
            font-size: 16px; /* Tamanho da fonte */
        }

        input[type="text"]::placeholder,
        textarea::placeholder {
            color: #7f8c8d; /* Cor do texto do placeholder */
            opacity: 1; /* Garante que o texto do placeholder seja visível */
        }

        button {
            width: 100%; /* Largura total do botão */
            padding: 10px; /* Espaçamento interno do botão */
            border: none; /* Remove a borda */
            border-radius: 5px; /* Bordas arredondadas */
            background-color: #2ecc71; /* Verde */
            color: #fff; /* Texto branco */
            font-size: 18px; /* Tamanho da fonte */
            cursor: pointer; /* Cursor de mão */
            transition: background-color 0.3s; /* Transição suave */
        }

        button:hover {
            background-color: #27ae60; /* Verde mais escuro ao passar o mouse */
        }

        .icon {
            margin-right: 10px; /* Espaçamento entre o ícone e o texto */
        }

        a {
            display: block; /* Faz o link ocupar toda a largura */
            text-align: center; /* Centraliza o texto do link */
            margin-top: 20px; /* Espaço acima do link */
            color: #fff; /* Cor do texto */
            text-decoration: none; /* Remove sublinhado */
        }

        a:hover {
            text-decoration: underline; /* Sublinhado no hover */
        }

        .image-container {
            position: relative; /* Para permitir posicionamento absoluto */
            display: flex; /* Usar flexbox */
            flex-direction: column; /* Organiza os elementos em coluna */
            align-items: center; /* Centraliza horizontalmente */
            justify-content: center; /* Centraliza verticalmente */
            flex: 1; /* Faz a imagem ocupar o espaço disponível */
            padding: 20px; /* Adiciona um espaço interno */
        }

        .image-container img {
            max-width: 100%; /* A imagem não deve ultrapassar a largura da seção */
            height: auto; /* Mantém a proporção da imagem */
            border-radius: 8px; /* Bordas arredondadas na imagem */
        }

        .image-container h3,
        .image-container p {
            position: absolute; /* Permite sobreposição do texto */
            color: white; /* Cor do texto */
            background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente para melhor legibilidade */
            padding: 10px; /* Espaço interno do texto */
            border-radius: 4px; /* Bordas arredondadas no fundo do texto */
        }

        .image-container h3 {
            top: 10px; /* Distância do topo da imagem */
            left: 0px; /* Distância da esquerda */
            font-size: 16px; /* Tamanho da fonte do título */
        }

        .image-container p {
            bottom: 10px; /* Distância do fundo da imagem */
            left: 0px; /* Distância da esquerda */
            font-size: 12px; /* Tamanho da fonte do parágrafo */
            line-height: 1.4; /* Espaçamento entre linhas */
            margin: 0; /* Remove margens do parágrafo */
        }

    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h1><i class="fas fa-exclamation-triangle icon"></i>Criar Nova Denúncia</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="titulo"><i class="fas fa-tag"></i>Título:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Título da denúncia..." required maxlength="45" />

            <label for="descricao"><i class="fas fa-pencil-alt"></i> Descrição:</label>
            <textarea id="descricao" name="descricao" placeholder="Descreva a situação..." required maxlength="2499"></textarea>

            <div class="form-group">
                <p>Para facilitar o preenchimento do seu endereço, você pode usar o botão abaixo para obter sua localização atual. Isso ajudará a preencher automaticamente o campo de CEP. Clique em "Obter Localização" (opcional) para começar!</p>
                <label for="cep">CEP:</label>
                <button id="get-location" type="button" class="btn-submit">Obter Localização (Opcional)</button>
                <input type="text" id="cep" name="cep" placeholder="Seu CEP..." required 
                    pattern="\d{5}-\d{3}" title="Digite um CEP no formato 12345-678">
            </div>


            <div class="form-group">
                <label for="anexo">Anexar Evidências (obrigatório):</label>
                <input type="file" id="anexo" name="anexo" accept="image/*" required>
            </div>
                
            <?php if ($mensagem): ?>
                <p class="mensagem" id="mensagem"><?php echo htmlspecialchars($mensagem); ?></p>
            <?php endif; ?>

            <button type="submit"><i class="fas fa-paper-plane icon"></i>Enviar Denúncia</button>
        </form>
        <a href="denuncia_listar.php"><i class="fas fa-arrow-left"></i> Voltar para Lista</a>
    </div>

    <div class="image-container">
        <img src="img/denuncia_create.jpg" alt="Imagem Ilustrativa"> 
    </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            const cepInput = document.getElementById('cep');
        
            cepInput.addEventListener('input', function () {
                const cepPattern = /^\d{5}-\d{3}$/; // Padrão para CEP: 12345-678
                if (!cepPattern.test(this.value)) {
                    this.setCustomValidity('Digite um CEP no formato XXXXX-XXX');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
                
        window.onload = function() {
            const mensagem = document.getElementById('mensagem');
            if (mensagem) {
                setTimeout(() => {
                    mensagem.style.opacity = 0;
                    setTimeout(() => {
                        mensagem.style.display = 'none';
                    }, 500); 
                }, 4000);
            }
        };

        document.getElementById('get-location').addEventListener('click', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        getAddress(latitude, longitude);
                    },
                    (error) => {
                        alert("Por favor, para sabermos sua localização é necessário permite que sabermos sua localização. ");
                    }
                );
            } else {
                alert('Geolocalização não é suportada neste navegador.');
            }
        });

        function getAddress(latitude, longitude) {
            const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address && data.address.postcode) { 
                        document.getElementById('cep').value = data.address.postcode; 
                    } else {
                        document.getElementById('cep').value = 'CEP não encontrado'; 
                    }
                })
                .catch(error => {
                    document.getElementById('cep').value = 'Erro ao obter CEP.'; 
                });
        }

    </script>
</body>
</html>