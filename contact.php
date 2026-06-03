<?php
require_once "db.php";
session_start();

// Traitement du formulaire de contact
$message_envoye = "";
$erreur = "";

// Récupérer l'utilisateur connecté (si existant)
$user_connecte = isset($_SESSION["user_id"]);
$user_nom = "";
$user_email = "";

if ($user_connecte) {
    $stmt = $pdo->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id_user = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user_nom = $user["prenom"] . " " . $user["nom"];
        $user_email = $user["email"];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $sujet = trim($_POST["sujet"] ?? "");
    $message = trim($_POST["message"] ?? "");
    
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $erreur = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        // Sauvegarder le message
        $id_user = $user_connecte ? $_SESSION["user_id"] : null;
        $stmt = $pdo->prepare("INSERT INTO messages_contact (id_user, nom, email, sujet, message, date_envoi, status) VALUES (?, ?, ?, ?, ?, NOW(), 'non_lu')");
        $stmt->execute([$id_user, $nom, $email, $sujet, $message]);
        
        $message_envoye = "✅ Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Orientation Pro</title>
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
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .contact-info {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .contact-info h3 {
            color: #001529;
            margin-bottom: 25px;
            font-size: 22px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eef2f6;
        }

        .contact-icon {
            font-size: 24px;
            width: 45px;
            height: 45px;
            background: #e6f4ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-text {
            flex: 1;
        }

        .contact-text strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
        }

        .contact-text a, .contact-text span {
            color: #666;
            text-decoration: none;
        }

        .contact-text a:hover {
            color: #1890ff;
        }

        .hours-box {
            margin-top: 25px;
            background: #f8f9fc;
            padding: 20px;
            border-radius: 12px;
        }

        .hours-box h4 {
            color: #001529;
            margin-bottom: 10px;
        }

        .hours-box p {
            color: #666;
            line-height: 1.8;
        }

        .contact-form {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .contact-form h3 {
            color: #001529;
            margin-bottom: 25px;
            font-size: 22px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }

        .user-info-badge {
            background: #e6f4ff;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #1890ff;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #1890ff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
        }

        .btn-submit {
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #40a9ff;
        }

        .success-msg {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #135200;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-msg {
            background: #fff2f0;
            border: 1px solid #ffccc7;
            color: #ff4d4f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
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

        @media (max-width: 768px) {
            .main-header {
                padding: 0 20px;
            }
            .nav-links {
                gap: 15px;
            }
            .contact-grid {
                grid-template-columns: 1fr;
            }
            .content-area {
                padding: 30px 20px;
            }
            .page-title {
                font-size: 28px;
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

    <div class="content-area">
        <h1 class="page-title">📞 Nous contacter</h1>
        <p class="page-subtitle">Une question ? Un problème technique ? Notre équipe est là pour vous répondre 24h/24 et 7j/7.</p>

        <?php if ($message_envoye): ?>
            <div class="success-msg"><?= $message_envoye ?></div>
        <?php endif; ?>

        <?php if ($erreur): ?>
            <div class="error-msg"><?= $erreur ?></div>
        <?php endif; ?>

        <div class="contact-grid">
            <div class="contact-info">
                <h3>📍 Nos coordonnées</h3>

                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <div class="contact-text">
                        <strong>Email</strong>
                        <a href="mailto:contact@orientation-pro.dz">contact@orientation-pro.dz</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div class="contact-text">
                        <strong>Téléphone</strong>
                        <a href="tel:+213557798669">05.57.79.86.69</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">💬</div>
                    <div class="contact-text">
                        <strong>WhatsApp</strong>
                        <a href="https://wa.me/213557798669">05.57.79.86.69</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">🌐</div>
                    <div class="contact-text">
                        <strong>Site web</strong>
                        <a href="#">www.orientation-pro.dz</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <div class="contact-text">
                        <strong>Adresse</strong>
                        <span>Sidi Bel Abbès, Algérie</span>
                    </div>
                </div>

                <div class="hours-box">
                    <h4>📅 Horaires d'assistance</h4>
                    <p>
                        🕐 <strong>24h/24 et 7j/7</strong><br>
                        Notre équipe est disponible à tout moment pour vous assister.<br>
                        (Centre d'appel - Assistance permanente)
                    </p>
                    <p style="margin-top: 10px; font-size: 13px; color: #1890ff;">
                        ⏱️ Réponse immédiate ou sous 30 minutes maximum
                    </p>
                </div>
            </div>

            <div class="contact-form">
                <h3>✉️ Envoyez-nous un message</h3>

                <?php if ($user_connecte): ?>
                    <div class="user-info-badge">
                        ✅ Connecté en tant que <strong><?= htmlspecialchars($user_nom); ?></strong> (<?= htmlspecialchars($user_email); ?>)
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($user_nom); ?>" <?= $user_connecte ? 'readonly' : ''; ?> required>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user_email); ?>" <?= $user_connecte ? 'readonly' : ''; ?> required>
                    </div>

                    <div class="form-group">
                        <label>Sujet *</label>
                        <input type="text" name="sujet" required>
                    </div>

                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">📨 Envoyer le message</button>
                </form>
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