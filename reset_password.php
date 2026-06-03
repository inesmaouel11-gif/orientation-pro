<?php
require_once "db.php";

$message = "";
$error = "";
$token = $_GET["token"] ?? "";
$user_id = null;

// Vérifier le token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE code_hash = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset) {
        $user_id = $reset["id_user"];
    } else {
        $error = "Lien invalide ou expiré. Veuillez refaire une demande.";
    }
}

// Traitement du nouveau mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_id) {
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (empty($password) || empty($confirm_password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET password_hash = ? WHERE id_user = ?");
        $stmt->execute([$hash, $user_id]);

        // Supprimer le token utilisé
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE id_user = ?");
        $stmt->execute([$user_id]);

        header("Location: login.php?reset=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - Orientation Pro</title>
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
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .logo-area {
            font-size: 28px;
            font-weight: bold;
            color: #001529;
            margin-bottom: 20px;
            display: block;
            text-decoration: none;
        }

        h2 {
            color: #001529;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #595959;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #1890ff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #1890ff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #40a9ff;
        }

        .error-msg {
            background: #fff2f0;
            border: 1px solid #ffccc7;
            color: #ff4d4f;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .footer-links {
            margin-top: 25px;
            font-size: 14px;
            color: #8c8c8c;
        }

        .footer-links a {
            color: #1890ff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="logo-area">🎓 Orientation Pro</a>

    <div class="card">
        <?php if (empty($token) || !$user_id): ?>
            <h2>Lien invalide</h2>
            <p class="subtitle">Le lien de réinitialisation est invalide ou a expiré.</p>
            <div class="footer-links">
                <a href="forgot_password.php">→ Faire une nouvelle demande</a><br><br>
                <a href="login.php">← Retour à la connexion</a>
            </div>
        <?php else: ?>
            <h2>Nouveau mot de passe</h2>
            <p class="subtitle">Choisissez un mot de passe sécurisé (8 caractères minimum)</p>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Réinitialiser</button>
            </form>

            <div class="footer-links">
                <a href="login.php">← Retour à la connexion</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>