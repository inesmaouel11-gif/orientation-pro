<?php
require_once "db.php";
// Page publique - pas de guard
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Orientation Pro</title>
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

        /* Header avec les 5 boutons */
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
            letter-spacing: 1px;
        }

        /* 🔥 5 BOUTONS DE NAVIGATION */
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: #a6adb4;
            text-decoration: none;
            transition: color 0.3s;
            font-size: 15px;
        }

        .nav-links a:hover {
            color: white;
        }

        .btn-connexion-nav {
            background-color: #1890ff;
            color: white !important;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: 500;
        }

        /* Contenu principal */
        .content-area {
            flex: 1;
            padding: 60px 10%;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-title {
            font-size: 36px;
            color: #001529;
            margin-bottom: 30px;
            text-align: center;
        }

        .about-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .about-section h2 {
            color: #001529;
            margin-bottom: 20px;
            font-size: 24px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }

        .about-section p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .mission-card {
            background: #f8f9fc;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s;
        }

        .mission-card:hover {
            transform: translateY(-5px);
        }

        .mission-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .mission-card h3 {
            color: #001529;
            margin-bottom: 10px;
        }

        .mission-card p {
            font-size: 14px;
            color: #666;
        }

        /* 🔥 SECTION NOTRE ÉQUIPE */
        .team-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .team-section h2 {
            color: #001529;
            margin-bottom: 20px;
            font-size: 24px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .team-card {
            background: #f8f9fc;
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }

        .team-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1890ff 0%, #001529 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 45px;
            color: white;
        }

        .team-name {
            font-size: 20px;
            font-weight: 600;
            color: #001529;
            margin-bottom: 8px;
        }

        .team-role {
            font-size: 14px;
            color: #1890ff;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .team-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #001529 0%, #002140 100%);
            border-radius: 16px;
            color: white;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #1890ff;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.85;
            margin-top: 5px;
        }

        /* Footer */
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
            .content-area {
                padding: 30px 20px;
            }
            .page-title {
                font-size: 28px;
            }
            .about-section, .team-section {
                padding: 25px;
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
        <!-- 🔥 LES 5 BOUTONS : Accueil, À propos, FAQ, Contact, Connexion -->
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="a_propos.php">À propos</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Contact</a>
            <a href="login.php" class="btn-connexion-nav">🔐 Connexion</a>
        </div>
    </header>

    <div class="content-area">
        <h1 class="page-title">À propos d'Orientation Pro</h1>

        <div class="about-section">
            <h2>📌 Notre mission</h2>
            <p>
                <strong>Orientation Pro</strong> est une plateforme web intelligente dédiée à l'aide à l'orientation post-bac en Algérie. 
                Chaque année, des milliers de bacheliers algériens sont confrontés à une difficulté majeure : le choix de la spécialité 
                universitaire ou professionnelle la plus adaptée à leur profil scolaire.
            </p>
            <p>
                Notre mission est de fournir un outil automatisé et intelligent capable d'analyser les notes du baccalauréat afin de 
                proposer des recommandations personnalisées sur les spécialités universitaires, les grandes écoles et les instituts paramédicaux.
            </p>
        </div>

        <div class="about-section">
            <h2>🎯 Nos objectifs</h2>
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">📝</div>
                    <h3>Saisie simplifiée</h3>
                    <p>Renseignez vos notes du Bac en quelques minutes</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">⚙️</div>
                    <h3>Analyse intelligente</h3>
                    <p>Algorithme de pondération dynamique par spécialité</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">📊</div>
                    <h3>Score de compatibilité</h3>
                    <p>Pour chaque filière, un score personnalisé</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">🎓</div>
                    <h3>Recommandations ciblées</h3>
                    <p>Universités, Grandes écoles, Instituts paramédicaux</p>
                </div>
            </div>
        </div>

        <!-- 🔥 NOUVELLE SECTION : NOTRE ÉQUIPE -->
        <div class="team-section">
            <h2>👥 Notre équipe</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <div class="team-name">Maouel Ines Naziha</div>
                    <div class="team-role">Développeuse Full Stack</div>
                    <div class="team-desc">Conception et développement de la plateforme, algorithme d'orientation et base de données.</div>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍🏫</div>
                    <div class="team-name">Fahsi Mahmoud</div>
                    <div class="team-role">Enseignant / Encadrant</div>
                    <div class="team-desc">Supervision du projet, validation des fonctionnalités et conseils pédagogiques.</div>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>🌍 Périmètre couvert</h2>
            <p>
                Notre plateforme couvre l'ensemble des établissements d'enseignement supérieur en Algérie :
            </p>
            <ul style="margin-top: 15px; margin-left: 25px; color: #555; line-height: 1.8;">
                <li>🏛️ Universités algériennes (système LMD, système d'ingéniorat)</li>
                <li>🏫 Grandes écoles nationales et écoles supérieures spécialisées</li>
                <li>🏥 Instituts paramédicaux</li>
                <li>📚 Instituts de formation spécialisée (commerce, création, etc.)</li>
            </ul>
        </div>

        <div class="stats-container">
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

</body>
</html>