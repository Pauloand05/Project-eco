<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Navegação</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        *:focus,
        *:active {
            outline: none !important;
            -webkit-tap-highlight-color: transparent;
        }

        footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 15px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: auto; /* Isso empurra o footer para o final da página */
        }

        footer .social-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        p {
            padding: 10px 0;
        }

        div a {
            color: black;
            text-decoration: none;
        }

        div a:hover {
            text-decoration: underline;
        }

        .wrapper {
            display: inline-flex;
            list-style: none;
        }

        .wrapper .icon {
            position: relative;
            background: #ffffff;
            border-radius: 50%;
            padding: 15px;
            margin: 10px;
            width: 50px;
            height: 50px;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .tooltip {
            position: absolute;
            top: 0;
            font-size: 14px;
            background: #ffffff;
            color: #ffffff;
            padding: 5px 8px;
            border-radius: 5px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .tooltip::before {
            position: absolute;
            content: "";
            height: 8px;
            width: 8px;
            background: #ffffff;
            bottom: -3px;
            left: 50%;
            transform: translate(-50%) rotate(45deg);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .icon:hover .tooltip {
            top: -45px;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .wrapper .icon:hover {
            transform: scale(1.1); /* Pequeno efeito de zoom ao passar o mouse */
        }

        /* Cores dos ícones */
        .wrapper .facebook:hover,
        .wrapper .facebook:hover .tooltip,
        .wrapper .facebook:hover .tooltip::before {
            background: #1877f2;
            color: #ffffff;
        }

        .wrapper .twitter:hover,
        .wrapper .twitter:hover .tooltip,
        .wrapper .twitter:hover .tooltip::before {
            background: #222222;
            color: #ffffff;
        }

        .wrapper .instagram:hover,
        .wrapper .instagram:hover .tooltip,
        .wrapper .instagram:hover .tooltip::before {
            background: #e4405f;
            color: #ffffff;
        }

        .wrapper .github:hover,
        .wrapper .github:hover .tooltip,
        .wrapper .github:hover .tooltip::before {
            background: #833ab4;
            color: #ffffff;
        }

        .wrapper .youtube:hover,
        .wrapper .youtube:hover .tooltip,
        .wrapper .youtube:hover .tooltip::before {
            background: #cd201f;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .social-icons ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .social-icons .icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
                padding: 10px;
            }

            footer p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<footer>
    <p>&copy; 2024 Eco. Todos os direitos reservados.</p>
    <div class="social-icons">
        <ul class="wrapper">
            <li><a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                <span class="icon facebook" aria-label="Facebook">
                    <span class="tooltip">Facebook</span>
                    <span><i class="fab fa-facebook-f"></i></span>
                </span>
            </a></li>

            <li><a href="https://twitter.com" target="_blank" rel="noopener noreferrer">
                <span class="icon twitter" aria-label="Twitter">
                    <span class="tooltip">Twitter</span>
                    <span><i class="fab fa-twitter"></i></span>
                </span>
            </a></li>

            <li><a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                <span class="icon instagram" aria-label="Instagram">
                    <span class="tooltip">Instagram</span>
                    <span><i class="fab fa-instagram"></i></span>
                </span>
            </a></li>

            <li><a href="https://github.com" target="_blank" rel="noopener noreferrer">
                <span class="icon github" aria-label="Github">
                    <span class="tooltip">Github</span>
                    <span><i class="fab fa-github"></i></span>
                </span>
            </a></li>

            <li><a href="https://youtube.com" target="_blank" rel="noopener noreferrer">
                <span class="icon youtube" aria-label="Youtube">
                    <span class="tooltip">Youtube</span>
                    <span><i class="fab fa-youtube"></i></span>
                </span>
            </a></li>
        </ul>
    </div>
    <p><a href="PoliticadePrivacidade.html" style="color: #fff; text-decoration: underline;">Política de Privacidade</a> | <a href="TermosdeServico.html" style="color: #fff; text-decoration: underline;">Termos de Serviço</a></p>
</footer>
</body>
</html>