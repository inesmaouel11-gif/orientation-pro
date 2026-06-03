<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orientation Pro - Plateforme d'orientation post-bac Algérie</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            background-color: #001529;
            color: white;
            padding: 0 40px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            font-size: 28px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: #a6adb4;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: white;
        }

        .btn-connexion-nav {
            background-color: #1890ff;
            color: white !important;
            padding: 8px 20px;
            border-radius: 4px;
        }

        /* 🔥 HERO AVEC CARROUSEL D'IMAGES */
        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .hero-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            max-width: 1200px;
            width: 100%;
            overflow: hidden;
            text-align: center;
        }

        /* 🔥 CARROUSEL */
        .carousel-container {
            position: relative;
            width: 100%;
            height: 500px;
            overflow: hidden;
        }

        .carousel-slides {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        /* Overlay sombre pour lire le texte */
        .carousel-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,21,41,0.75) 0%, rgba(0,21,41,0.6) 100%);
            z-index: 1;
        }

        .carousel-slide-content {
            position: relative;
            z-index: 2;
            max-width: 70%;
            text-align: center;
            color: white;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .carousel-slide-content h2 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .carousel-slide-content p {
            font-size: 20px;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .carousel-slide-content ul {
            list-style: none;
            margin-bottom: 30px;
        }

        .carousel-slide-content li {
            display: inline-block;
            margin: 8px 12px;
            padding: 8px 18px;
            background: rgba(255,255,255,0.2);
            border-radius: 30px;
            font-size: 14px;
            backdrop-filter: blur(5px);
        }

        .btn-slide {
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn-slide:hover {
            background-color: #40a9ff;
            transform: scale(1.05);
        }

        /* Boutons navigation */
        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255,255,255,0.3);
            color: white;
            border: none;
            font-size: 30px;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 50%;
            transition: 0.3s;
            z-index: 10;
            backdrop-filter: blur(5px);
        }

        .carousel-btn:hover {
            background-color: #1890ff;
        }

        .carousel-btn-prev {
            left: 20px;
        }

        .carousel-btn-next {
            right: 20px;
        }

        /* Dots */
        .carousel-dots {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin: 0 5px;
            background-color: rgba(255,255,255,0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }

        .dot.active {
            background-color: #1890ff;
            transform: scale(1.2);
        }

        /* Statistiques */
        .stats-row {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
            margin: 30px 20px;
            padding: 25px;
            background: linear-gradient(135deg, #001529 0%, #002140 100%);
            border-radius: 12px;
            color: white;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1890ff;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.85;
        }

        .main-footer {
            background-color: #001529;
            color: #a6adb4;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            margin-top: auto;
        }

        .main-footer a {
            color: #a6adb4;
            text-decoration: none;
            margin: 0 10px;
        }

        .main-footer a:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .main-header {
                padding: 0 20px;
            }
            .nav-links {
                gap: 15px;
            }
            .carousel-container {
                height: 550px;
            }
            .carousel-slide-content h2 {
                font-size: 24px;
            }
            .carousel-slide-content p {
                font-size: 14px;
            }
            .carousel-slide-content li {
                display: block;
                margin: 8px 0;
            }
            .stats-row {
                gap: 25px;
            }
            .carousel-btn {
                font-size: 20px;
                padding: 5px 12px;
            }
            .carousel-slide-content {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="logo-area">
            <span class="logo-icon">🎓</span>
            <span class="logo-text">Orientation Pro</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="a_propos.php">À propos</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Contact</a>
            <a href="login.php" class="btn-connexion-nav">🔐 Connexion</a>
        </div>
    </header>

    <div class="hero-section">
        <div class="hero-card">
            <!-- 🔥 CARROUSEL AVEC 4 PHOTOS -->
            <div class="carousel-container">
                <div class="carousel-slides" id="carouselSlides">
                    
                    <!-- Slide 1 : Problématique - Étudiant perdu -->
                    <div class="carousel-slide" data-index="0" style="background-image: url('https://images.pexels.com/photos/4145191/pexels-photo-4145191.jpeg?w=1200&q=80');">
                        <div class="carousel-slide-content">
                            <h2>📌 Vous ne savez pas quoi choisir ?</h2>
                            <p>Chaque année, des milliers de bacheliers algériens sont confrontés à cette difficulté :<br>
                            <strong>Quelle spécialité correspond vraiment à mon profil ?</strong></p>
                            <ul>
                                <li>❌ Choix sans analyse des notes</li>
                                <li>❌ Absence d'accompagnement</li>
                                <li>❌ Risque de réorientation</li>
                            </ul>
                            <a href="login.php" class="btn-slide">🔍 Trouver ma voie</a>
                        </div>
                    </div>

                    <!-- Slide 2 : Solution - Ordinateur / Technologie -->
                    <div class="carousel-slide" data-index="1" style="background-image: url('https://images.pexels.com/photos/1181675/pexels-photo-1181675.jpeg?w=1200&q=80');">
                        <div class="carousel-slide-content">
                            <h2>🎯 La solution intelligente</h2>
                            <p>Orientation Pro analyse votre profil scolaire et vous propose<br>
                            les filières qui vous correspondent vraiment.</p>
                            <ul>
                                <li>✅ Algorithme multicritère</li>
                                <li>✅ Score de compatibilité personnalisé</li>
                                <li>✅ Basé sur vos notes et votre série BAC</li>
                            </ul>
                            <a href="login.php" class="btn-slide">✨ Découvrir</a>
                        </div>
                    </div>

                    <!-- Slide 3 : Fonctionnalités - Étudiant avec ordinateur -->
                    <div class="carousel-slide" data-index="2" style="background-image: url('https://images.pexels.com/photos/6238044/pexels-photo-6238044.jpeg?w=1200&q=80');">
                        <div class="carousel-slide-content">
                            <h2>⚙️ Des fonctionnalités complètes</h2>
                            <p>Un outil tout-en-un pour réussir votre orientation post-bac</p>
                            <ul>
                                <li>📝 Saisie simplifiée des notes</li>
                                <li>📊 Calcul des chances d'admission</li>
                                <li>🏛️ Catalogue de +50 filières</li>
                                <li>❤️ Favoris et export PDF/Excel</li>
                                <li>📚 Programmes et débouchés</li>
                            </ul>
                            <a href="login.php" class="btn-slide">🎓 C'est parti</a>
                        </div>
                    </div>

                    <!-- Slide 4 : Réussite - Diplômés / Cérémonie -->
                    <div class="carousel-slide" data-index="3" style="background-image: url('https://images.pexels.com/photos/267885/pexels-photo-267885.jpeg?w=1200&q=80');">
                        <div class="carousel-slide-content">
                            <h2>🎓 Votre avenir commence ici</h2>
                            <p>Rejoignez des milliers d'étudiants qui ont trouvé leur voie<br>
                            grâce à Orientation Pro.</p>
                            <ul>
                                <li>🌟 Gratuit et accessible à tous</li>
                                <li>🌟 Accompagnement personnalisé</li>
                                <li>🌟 Décisions éclairées pour votre futur</li>
                            </ul>
                            <a href="login.php" class="btn-slide">🚀 Commencer maintenant</a>
                        </div>
                    </div>

                </div>

                <button class="carousel-btn carousel-btn-prev" id="prevBtn">❮</button>
                <button class="carousel-btn carousel-btn-next" id="nextBtn">❯</button>
                <div class="carousel-dots" id="carouselDots"></div>
            </div>

            <!-- Statistiques -->
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number">+50</div>
                    <div class="stat-label">Spécialités référencées</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">30+</div>
                    <div class="stat-label">Établissements partenaires</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Gratuit pour les bacheliers</div>
                </div>
            </div>

            <p style="margin: 0 20px 25px 20px; color: #888; font-size: 13px; border-top: 1px solid #eef2f6; padding-top: 20px;">
                🇩🇿 Plateforme dédiée aux bacheliers algériens — Universités LMD, Grandes écoles, Instituts paramédicaux
            </p>
        </div>
    </div>

    <footer class="main-footer">
        <p>© 2025 Orientation Pro — Plateforme d'orientation académique et professionnelle (Algérie)</p>
        <p>
            <a href="a_propos.php">À propos</a> |
            <a href="faq.php">FAQ</a> |
            <a href="contact.php">Contact</a> |
            <a href="cgu.php">Mentions légales</a>
        </p>
    </footer>

    <script>
        // 🔥 SCRIPT DU CARROUSEL
        const slides = document.querySelectorAll('.carousel-slide');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dotsContainer = document.getElementById('carouselDots');
        
        let currentIndex = 0;
        const totalSlides = slides.length;
        let autoPlayInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });
            updateDots();
        }

        function createDots() {
            dotsContainer.innerHTML = '';
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === currentIndex) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(i));
                dotsContainer.appendChild(dot);
            }
        }

        function updateDots() {
            const dots = document.querySelectorAll('.dot');
            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function goToSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;
            showSlide(currentIndex);
            resetAutoPlay();
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        function resetAutoPlay() {
            if (autoPlayInterval) clearInterval(autoPlayInterval);
            autoPlayInterval = setInterval(() => {
                nextSlide();
            }, 6000);
        }

        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);

        createDots();
        showSlide(0);
        resetAutoPlay();

        const carouselContainer = document.querySelector('.carousel-container');
        carouselContainer.addEventListener('mouseenter', () => {
            if (autoPlayInterval) clearInterval(autoPlayInterval);
        });
        carouselContainer.addEventListener('mouseleave', resetAutoPlay);
    </script>

</body>
</html>