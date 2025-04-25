<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-image: url('img/flora.jpg'); /* Mantém a imagem de fundo */
            background-size: cover;
            background-position: center;
            color: #fff; /* Texto claro para contraste */
            overflow-x: hidden;
            height: 100vh; /* Garantir que a altura cubra toda a tela */
        }

        /* Conteúdo principal */
        .content {
            background-color: rgba(0, 0, 0, 0.75); /* Fundo semitransparente escuro */
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            margin: 40px auto;
            max-width: 900px;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .content.visible {
            opacity: 1;
            transform: translateY(0);
        }

        h1 {
            font-size: 3rem;
            font-weight: bold;
            color: #ff5722; /* Laranja vibrante */
            text-transform: uppercase;
        }

        h2 {
            color: #fff;
            font-size: 2rem;
            margin-top: 20px;
        }

        .team {
            margin: 40px 0;
            background-color: rgba(33, 33, 33, 0.9); /* Fundo escuro para a seção de equipe */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .team-members {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            /* Removemos as animações de transição */
        }

        .member {
            background: rgba(255, 255, 255, 0.1); /* Cartões com fundo escuro translúcido */
            border-radius: 12px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease-out, box-shadow 0.3s;
        }

        .member:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5);
        }

        .member h3 {
            margin-top: 0;
            color: #ff5722; /* Nome com cor vibrante */
            font-size: 1.5rem;
        }

        .objectives, .values {
            max-width: 600px;
            margin: 40px auto;
            background-color: rgba(33, 33, 33, 0.8); /* Fundo escuro para as seções */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .objectives h2, .values h2 {
            color: #ff5722;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        ul {
            text-align: left;
            padding-left: 20px;
        }

        li {
            margin: 10px 0;
            font-size: 1.1rem;
        }

        /* Efeito de feedback nos botões */
        button {
            background-color: #ff5722;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 25px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #e64a19;
        }

        button:active {
            transform: scale(0.98);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .team-members {
                flex-direction: column;
                align-items: center;
            }

            .team-members .member {
                width: 80%;
            }

            .objectives, .values {
                padding: 15px;
            }

            .content {
                padding: 30px;
            }

            h1 {
                font-size: 2.2rem;
            }

            h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content" id="content">
    <h1>Sobre Nós</h1>
    
    <section class="team">
        <h2>Nossa Equipe</h2>
        <p>Somos um grupo de profissionais apaixonados por jogos e tecnologia. Nossa equipe é composta por desenvolvedores, designers e especialistas em marketing que trabalham juntos para criar experiências de jogo excepcionais.</p>
        <div class="team-members" id="team-members">
            <div class="member">
                <h3>Marcos Wyllian</h3>
                <p>Desenvolvedor Principal</p>
                <p>Desenvolvedor back-end com 5 anos de experiência, especializado em PHP e MySQL. Ele é responsável por garantir a eficiência, segurança e escalabilidade do sistema no projeto Eco.</p>
            </div>
            <div class="member">
                <h3>Luis Ricardo</h3>
                <p>Game Designer</p>
                <p>Desenvolvedor de jogos com 4 anos de experiência, focada em criar experiências imersivas e dinâmicas. Ela é a responsável pelo design e desenvolvimento dos jogos no projeto Eco.</p>
            </div>
            <div class="member">
                <h3>José Edimilson</h3>
                <p>Designer Gráfico</p>
                <p>Desenvolvedor front-end e designer, apaixonado por criar interfaces intuitivas e atraentes. Com 5 anos de experiência, ele é responsável por garantir uma experiência visual fluida e envolvente no projeto Eco.</p>
            </div>
            <div class="member">
                <h3>Paulo André</h3>
                <p>Especialista em Marketing</p>
                <p>Especialista com 7 anos de experiência, focada em estratégias que ampliam a presença online e engajam usuários. É responsável por impulsionar a marca e conectar o público à experiência oferecida.</p>
            </div>
        </div>
    </section>

    <section class="objectives">
        <h2>Objetivos</h2>
        <p>Nossos principais objetivos incluem:</p>
        <ul>
            <li>Criar jogos envolventes que proporcionem diversão e aprendizado.</li>
            <li>Fomentar uma comunidade de jogadores apaixonados e engajados.</li>
            <li>Inovar constantemente e incorporar feedback dos jogadores em nossos projetos.</li>
        </ul>
    </section>

    <section class="values">
        <h2>Valores</h2>
        <p>Nossos valores fundamentais são:</p>
        <ul>
            <li><strong>Inovação:</strong> Estamos sempre em busca de novas ideias e soluções criativas.</li>
            <li><strong>Qualidade:</strong> Comprometemo-nos a entregar produtos de alta qualidade.</li>
            <li><strong>Colaboração:</strong> Valorizamos o trabalho em equipe e a contribuição de todos.</li>
            <li><strong>Integridade:</strong> Operamos com transparência e ética em tudo o que fazemos.</li>
        </ul>
    </section>
</div>

<?php include 'footer.php'; ?>

<script>
    // Animação do conteúdo
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('content').classList.add('visible');
    });
</script>

</body>
</html>