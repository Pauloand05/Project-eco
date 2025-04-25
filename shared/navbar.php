<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="ECO é um site sobre jogos e entretenimento com conteúdo exclusivo e muito mais!">
    <meta name="keywords" content="jogos, entretenimento, notícias, jogos online">
    <meta name="author" content="ECO Team">
    <title>Navegação</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <style>
        * {
            box-sizing: border-box;
        }

        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, #1b5e20, #4caf50);
            padding: 20px 30px;
            height: 90px;
            top: 0;
            left: 0;
            width: 100%; /* Garantir que a navbar ocupe toda a largura da tela */
            z-index: 1000;
            transition: background 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(27, 94, 32, 0.9);
        }

        .logo img {
            height: 75px;
            margin: auto;
        }

        .menu {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .menu li {
            margin-left: 30px;
            position: relative;
        }

        .menu a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: color 0.3s;
            font-size: 18px;
            padding: 10px 15px;
            display: inline-block;
        }

        .menu a:hover {
            color: #ff6347;
        }

        .active-link {
            color: #ff6347;
            font-weight: bold;
        }

        /* Efeito do Indicador */
        .menu li::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            width: 0;
            height: 3px;
            background-color: #ff6347;
            transition: width 0.3s ease, left 0.3s ease;
        }

        .menu li:hover::after,
        .menu li a.active-link::after {
            width: 100%;
            left: 0;
        }

        .user-icon img {
            height: 60px;
            margin-left: 10px;
            transition: transform 0.3s;
            border: 2px solid white;
            border-radius: 50%;
        }

        .user-icon img:hover {
            transform: scale(1.1);
        }

        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: white;
        }

        /* Estilos para a responsividade */
        @media (max-width: 768px) {
            .menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 90px;
                left: 0;
                width: 100%;
                background-color: #4caf50;
                z-index: 999;
            }

            .menu.active {
                display: flex;
            }

            .hamburger {
                display: block;
            }

            .menu li {
                margin: 10px 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="img/logo.png" alt="Logo do ECO, um site sobre jogos e entretenimento" />
            </div>
            <div class="hamburger" onclick="toggleMenu()">
                <i class="uil uil-bars"></i>
            </div>
            <ul class="menu" id="menu">
                <li><a href="index.php" class="active-link">Home</a></li>
                <li><a href="sobre.php">Sobre Nós</a></li>
                <li><a href="jogos.php">Jogos</a></li>
                <li><a href="contato.php">Contato</a></li>
            </ul>
            
            <div class="user-icon">
                <a href="perfil.php" aria-label="Perfil">
                    <img src="img/Perfil.jpg" alt="Imagem de Perfil do Usuário" loading="lazy" />
                </a>
            </div>
        </nav>
    </header>

    <script>
        // Efeito de scroll na Navbar
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Função para alternar o menu hamburguer
        function toggleMenu() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('active');
            const hamburger = document.querySelector('.hamburger');
            hamburger.setAttribute('aria-expanded', menu.classList.contains('active'));
        }
    </script>
</body>
</html>