<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Home</title>
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-image: url('img/flora.jpg');
            background-size: cover;
            background-position: center;
            color: #ffffff;
            background-repeat: no-repeat;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .carousel {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            padding: 20px;
            margin-bottom: 20px;
        }

        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            min-width: 100%;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .carousel img {
            width: 100%;
            height: auto;
            filter: brightness(0.6);
            object-fit: cover;
        }

        .slide-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .slide-text h3 {
            font-size: 28px;
            margin: 0 0 10px 0;
        }

        .slide-text p {
            font-size: 18px;
            line-height: 1.4;
        }

        .slide-button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #ff5722;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .slide-button:hover {
            background-color: #e64a19;
        }

        .carousel-dots {
            text-align: center;
            margin-top: 10px;
        }

        .dot {
            height: 10px;
            width: 10px;
            margin: 0 5px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
        }

        .dot.active {
            background-color: #ff5722;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
        }

        .section {
            flex: 1;
            padding: 25px 20px;
            background-color: rgba(27, 94, 32, 0.9);
            border: 2px solid #ff6347;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            max-width: 300px;
            margin: 20px auto;
            text-align: center;
        }

        .section h2 {
            margin-bottom: 15px;
            font-size: 24px;
            color: #ffffff;
        }

        .section p {
            font-size: 16px;
            line-height: 1.5;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .section {
                max-width: 100%;
                padding: 15px;
            }

            .slide-text h3 {
                font-size: 20px;
            }

            .slide-text p {
                font-size: 14px;
            }

            .carousel {
                padding: 1px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Carrossel -->
<div class="carousel">
    <div class="slides">
        <div class="slide">
            <a href="tabela.php"><img src="img/banner1.png" alt="Descubra o horário de Coleta"></a>
            <div class="slide-text">
                <h3>Descubra o horário de Coleta</h3>
                <p>Veja todos os horários disponíveis e encontre quando a coleta passará no seu bairro!</p>
                <a href="coleta_tabela.php" class="slide-button">Ver Tabela</a>
            </div>
        </div>
        <div class="slide">
            <a href="jogos.php"><img src="img/banner2.png" alt="Explore Novos Jogos"></a>
            <div class="slide-text">
                <h3>Explore Novos Jogos</h3>
                <p>Divirta-se com os jogos mais populares e descubra novos desafios!</p>
                <a href="jogos.php" class="slide-button">Ver Jogos</a>
            </div>
        </div>
        <div class="slide">
            <a href="denuncia_create.php"><img src="img/banner3.png" alt="Faça Sua Denúncia"></a>
            <div class="slide-text">
                <h3>Faça Sua Denúncia</h3>
                <p>Ajude a manter a cidade limpa e organizada reportando conduta e locais de descarte inapropriados.</p>
                <a href="denuncia_create.php" class="slide-button">Fazer Denúncia</a>
            </div>
        </div>
    </div>

    <div class="carousel-dots">
        <span class="dot active" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>
</div>

<!-- Seções -->
<div class="container">
    <section class="section about">
        <h2>Sobre a Empresa</h2>
        <p>Somos uma empresa dedicada a oferecer os melhores jogos e experiências para os nossos usuários. Nossa missão é proporcionar diversão e entretenimento para todas as idades.</p>
    </section>

    <section class="section news">
        <h2>Novidades de Jogos</h2>
        <p>Fique ligado nas últimas novidades! Novos jogos estão chegando em breve, trazendo aventuras emocionantes e desafios únicos. Não perca!</p>
    </section>
</div>

<?php include 'footer.php'; ?>

<script>
    let slideIndex = 1;
    showSlides(slideIndex);

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".dot");
        if (n > slides.length) { slideIndex = 1; }
        if (n < 1) { slideIndex = slides.length; }
        slides.forEach((slide, index) => {
            slide.style.display = (index + 1 === slideIndex) ? "block" : "none";
        });
        dots.forEach((dot, index) => {
            dot.classList.toggle("active", index + 1 === slideIndex);
        });
    }

    // Automatizar o carrossel
    setInterval(() => {
        currentSlide(slideIndex + 1);
    }, 10000);
</script>

</body>
</html>