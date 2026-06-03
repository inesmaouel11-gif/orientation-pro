<?php
require_once "db.php";
// Page publique, pas de guard
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Orientation Pro</title>
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

        /* Header */
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
            margin-bottom: 15px;
            text-align: center;
        }

        .page-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 50px;
            font-size: 18px;
        }

        .faq-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .faq-category {
            margin-bottom: 30px;
        }

        .faq-category h2 {
            color: #001529;
            margin-bottom: 20px;
            font-size: 22px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }

        .faq-item {
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 15px;
        }

        .faq-question {
            padding: 15px 0;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            transition: color 0.3s;
        }

        .faq-question:hover {
            color: #1890ff;
        }

        .faq-question span:first-child {
            font-size: 16px;
        }

        .faq-icon {
            font-size: 20px;
            transition: transform 0.3s;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0;
            color: #666;
            line-height: 1.6;
        }

        .faq-answer.active {
            max-height: 500px;
            padding-bottom: 15px;
        }

        .faq-answer p {
            margin-bottom: 10px;
        }

        .faq-answer ul, .faq-answer ol {
            margin-left: 25px;
            margin-top: 10px;
        }

        .faq-answer li {
            margin-bottom: 5px;
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
            .faq-section {
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
        <!-- 🔥 5 BOUTONS : Accueil, À propos, FAQ, Contact, Connexion (PAS de modale) -->
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="a_propos.php">À propos</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Contact</a>
            <a href="login.php" class="btn-connexion-nav">🔐 Connexion</a>
        </div>
    </header>

    <div class="content-area">
        <h1 class="page-title">❓ Foire aux questions</h1>
        <p class="page-subtitle">Toutes les réponses à vos questions sur Orientation Pro</p>

        <div class="faq-section">
            <div class="faq-category">
                <h2>📝 Inscription et compte</h2>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Comment créer un compte sur Orientation Pro ?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Pour créer un compte :</p>
                        <ol>
                            <li>Cliquez sur "S'inscrire" dans le menu</li>
                            <li>Remplissez le formulaire avec vos informations (nom, prénom, email, mot de passe)</li>
                            <li>Validez votre inscription</li>
                            <li>Connectez-vous avec votre email et mot de passe</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>J'ai oublié mon mot de passe, comment faire ?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Cliquez sur "Mot de passe oublié" sur la page de connexion. Vous recevrez un email avec un lien pour réinitialiser votre mot de passe.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Puis-je modifier mes informations personnelles ?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Oui, une fois connecté, allez dans "Mon profil" pour modifier vos coordonnées, votre photo, votre wilaya ou votre série BAC.</p>
                    </div>
                </div>
            </div>

            <div class="faq-category">
                <h2>📊 Saisie des notes et calcul</h2>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Comment saisir mes notes ?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Deux méthodes sont disponibles :</p>
                        <ul>
                            <li><strong>Saisie manuelle :</strong> Sélectionnez votre série BAC, puis entrez vos notes pour chaque matière.</li>
                            <li><strong>Upload de relevé :</strong> Importez votre relevé de notes (PDF, JPG, PNG) et notre système OCR extrait automatiquement les notes.</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Les notes sont comprises entre quelles valeurs ?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Les notes doivent être comprises entre <strong>0 et 20</strong>. Vous pouvez utiliser des décimales (ex: 15.5).</p>
                    </div>
                </div>
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

    <script>
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('.faq-icon');
            
            if (answer.classList.contains('active')) {
                answer.classList.remove('active');
                icon.innerHTML = '▼';
            } else {
                answer.classList.add('active');
                icon.innerHTML = '▲';
            }
        }
    </script>

</body>
</html>