<?php
session_start();
include 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

$message = ""; // Para mensagens de sucesso ou erro

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $cpf = $_POST['cpf'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $nickname = $_POST['nickname'] ?? '';

    // Limpa os dados para garantir que estamos com o formato correto
    $cpf = preg_replace('/\D/', '', $cpf); // Remove tudo que não for número
    $telefone = preg_replace('/\D/', '', $telefone); // Remove tudo que não for número

    // Validação de campos obrigatórios
    if (empty($cpf) || empty($nome) || empty($email) || empty($senha) || empty($nickname)) {
        $message = "Todos os campos são obrigatórios!";
    } else {
        // Valida o formato do CPF
        if (strlen($cpf) != 11) {
            $message = "CPF deve ter 11 dígitos.";
        } else {
            // Verifica se o CPF já existe
            $checkCpfSql = "SELECT COUNT(*) FROM usuario WHERE cpf = ?";
            $checkCpfStmt = $conn->prepare($checkCpfSql);
            $checkCpfStmt->bind_param("s", $cpf);
            $checkCpfStmt->execute();
            $checkCpfStmt->bind_result($countCpf);
            $checkCpfStmt->fetch();
            $checkCpfStmt->close();

            if ($countCpf > 0) {
                $message = "O CPF já está cadastrado. Tente outro.";
            } else {
                // Valida o formato do e-mail
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = "E-mail inválido!";
                } else {
                    // Verifica se o e-mail já está em uso
                    $checkEmailSql = "SELECT COUNT(*) FROM usuario WHERE email = ?";
                    $checkEmailStmt = $conn->prepare($checkEmailSql);
                    $checkEmailStmt->bind_param("s", $email);
                    $checkEmailStmt->execute();
                    $checkEmailStmt->bind_result($countEmail);
                    $checkEmailStmt->fetch();
                    $checkEmailStmt->close();

                    if ($countEmail > 0) {
                        $message = "O E-mail já está em uso. Tente outro.";
                    } else {
                        // Verifica se o nickname já está em uso
                        $checkNicknameSql = "SELECT COUNT(*) FROM usuario WHERE nickname = ?";
                        $checkNicknameStmt = $conn->prepare($checkNicknameSql);
                        $checkNicknameStmt->bind_param("s", $nickname);
                        $checkNicknameStmt->execute();
                        $checkNicknameStmt->bind_result($countNickname);
                        $checkNicknameStmt->fetch();
                        $checkNicknameStmt->close();

                        if ($countNickname > 0) {
                            $message = "O Nickname já está em uso. Tente outro.";
                        } else {
                            // Validação de senha (mínimo de 8 caracteres, pelo menos uma letra maiúscula, um número e um caractere especial)
                            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $senha)) {
                                $message = "A senha deve ter pelo menos 8 caracteres, com uma letra maiúscula, um número e um caractere especial.";
                            } else {
                                // Hash a senha
                                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                                // Prepara a inserção do novo usuário no banco de dados
                                $sql = "INSERT INTO usuario (cpf, nome, email, telefone, senha, nickname) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ssssss", $cpf, $nome, $email, $telefone, $senhaHash, $nickname);

                                if ($stmt->execute()) {
                                    $message = "Usuário cadastrado com sucesso!";
                                    // Redireciona para a página de login
                                    header("Location: login.php");
                                    exit();
                                } else {
                                    $message = "Erro ao cadastrar usuário: " . $stmt->error;
                                }

                                // Fecha a declaração
                                $stmt->close();
                            }
                        }
                    }
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <style>
        /* Estilo para a página de cadastro */
        body {
            margin: 0;
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
            background-image: url('img/lua.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.5); /* Menos brilho na imagem para maior contraste */
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-form {
            background-color: rgba(0, 0, 0, 0.7); /* Fundo mais escuro para o formulário */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 450px;
            text-align: center;
            color: #ffffff;
            animation: slideIn 0.6s ease forwards;
        }

        .register-form h1 {
            margin-bottom: 20px;
            font-size: 2.5em; /* Tamanho maior para o título */
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
            position: relative;
            font-weight: bold;
            color: #ffffff; /* Garantir que as labels sejam visíveis */
        }

        .form-control {
            width: 80%;
            padding: 15px 40px;
            border: 2px solid #fff; /* Borda branca para contraste */
            border-radius: 30px;
            font-size: 18px; /* Tamanho maior da fonte nos campos */
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            outline: none;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.3); /* Alteração no fundo ao focar */
            border-color: #007bff; /* Borda azul ao focar */
        }

        .message {
            margin-bottom: 20px;
            color: yellow;
            font-weight: bold;
            font-size: 14px;
        }

        .btn {
            background-color: #007BFF; /* Cor vibrante para os botões */
            color: #ffffff;
            padding: 15px 0;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 18px; /* Aumentar o tamanho da fonte do botão */
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #0056b3; /* Cor mais escura ao passar o mouse */
        }

        .error-message {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-weight: bold;
        }

        p {
            margin-top: 20px;
            color: #ffffff;
            font-size: 1.1em; /* Aumentar o tamanho da fonte do texto */
        }

        p a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }

        .policy-text {
            color: white;
            font-size: 1em; /* Aumentar o tamanho da fonte */
            margin-top: 10px;
            font-weight: bold;
        }

        .policy-text a {
            color: white;
        }

        .policy-text a:hover {
            text-decoration: underline;
        }
        /* Responsividade para dispositivos móveis (máximo de 600px) */
        @media (max-width: 600px) {
            .register-form {
                padding: 20px;
                max-width: 600px;
                margin: 50px auto;
            }

            .btn {
                font-size: 16px;
                padding: 12px 0;
            }

            p {
                font-size: 1em;
            }

            .message {
                font-size: 12px;
            }

            .policy-text {
                font-size: 0.9em;
            }
        }

        /* Responsividade para tablets (máximo de 768px) */
        @media (max-width: 768px) {

            .register-form {
                padding: 20px;
                max-width: 600px;
                margin: 50px auto;
            }

            .form-control {
                font-size: 17px;
            }

            .btn {
                font-size: 17px;
                padding: 14px 0;
            }}
    </style>
</head>
<body>
<div class="register-form">
  <h1> Cadastro de Usuário</h1>    
  <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'sucesso') !== false ? 'success' : ''; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-user"></i> CPF:</label>
            <input type="text" name="cpf" class="form-control" required placeholder="000.000.000-00">
        </div>
        <div class="form-group">
            <label><i class="fas fa-user-tag"></i> Nickname:</label>
            <input type="text" name="nickname" class="form-control" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-phone"></i> Telefone:</label>
            <input type="text" name="telefone" class="form-control" required placeholder="(00) 00000-0000">
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> Senha:</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="policy-text">
            <input type="checkbox" id="policy" required>
            <label for="policy">
                Eu aceito os 
                <a href="Terms&Conditions" class="option">Terms & Conditions</a>
            </label>
        </div>
        <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Cadastrar</button>
    </form>
    <p><a href="login.php">Já possui uma conta? Faça login</a></p>
</div>
<script>
$(document).ready(function(){
    // Aplica as máscaras nos campos durante a digitação
    $('input[name="cpf"]').mask('000.000.000-00');
    $('input[name="telefone"]').mask('(00) 00000-0000');
    
    // Remover as máscaras ao submeter o formulário
    $('form').submit(function() {
        // Remover a máscara do CPF
        var cpf = $('input[name="cpf"]').val().replace(/\D/g, ''); // Remove qualquer caractere não numérico
        $('input[name="cpf"]').val(cpf); // Atualiza o valor do campo sem a máscara

        // Remover a máscara do telefone
        var telefone = $('input[name="telefone"]').val().replace(/\D/g, ''); // Remove qualquer caractere não numérico
        $('input[name="telefone"]').val(telefone); // Atualiza o valor do campo sem a máscara
    });
});
</script>
</body>
</html>