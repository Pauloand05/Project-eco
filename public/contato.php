<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1b1b1b;
            color: #ffffff;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #ff6347;
        }

        .contact-info {
            margin: 50px auto;
            background: #2b2b2b;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .contact-info p {
            margin: 10px 0;
        }

        .contact-description {
            font-size: 16px;
            color: #ddd;
            text-align: center;
            width: 60%;
            margin: auto;
        }

        .contact-button {
            background-color: #ff6347;
            color: white;
            margin-bottom: 40px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .contact-button:hover {
            background-color: #e55347;
        }
        /* Media Queries para telas pequenas */
        @media (max-width: 768px) {
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <h1>Contato</h1>

        <div class="contact-info">
            <h2>Informações de Contato</h2>
            <p><strong>Telefone:</strong> (11) 1234-5678</p>
            <p><strong>Email:</strong> contato@empresa.com.br</p>
            <p><strong>Endereço:</strong> Rua Exemplo, 123, São Paulo, SP</p>
        </div>

        <div class="contact-description">
            <p>Se você tem alguma sugestão, gostaria de relatar um bug ou fornecer algum feedback sobre o nosso site ou serviços, por favor, envie-nos um e-mail. Ficaremos felizes em ouvir sua opinião!</p>
        </div>

        <!-- Botão para abrir o cliente de e-mail -->
        <a href="mailto:eco@gmail.com?subject=Contato%20via%20site&body=Olá,%20gostaria%20de%20falar%20sobre..." class="contact-button">
            Enviar E-mail
        </a>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Scripts -->
    <script>
        // Função para alternar o menu hambúrguer
        function toggleMenu() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('active');
        }

        // Efeito para navbar ao rolar a página
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>