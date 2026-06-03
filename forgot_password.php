<?php
require_once "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");

    if (empty($email)) {
        $error = "Veuillez saisir votre adresse email.";
    } else {
        // Vérifier si l'email existe
        $stmt = $pdo->prepare("SELECT id_user, email FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Supprimer les anciens tokens pour cet utilisateur
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE id_user = ?");
            $stmt->execute([$user["id_user"]]);

            // Insérer le nouveau token
            $stmt = $pdo->prepare("INSERT INTO password_resets (id_user, code_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user["id_user"], $token, $expires_at]);

            // Lien de réinitialisation (à adapter selon votre domaine)
            $reset_link = "http://" . $_SERVER["HTTP_HOST"] . "/OrientationPro/reset_password.php?token=" . $token;

            // Simulation d'envoi d'email (affichage du lien)
            $message = "Un lien de réinitialisation a été généré. <br>
                        <strong>⚠️ Mode démo :</strong> Voici votre lien : <br>
                        <a href='$reset_link' target='_blank'>$reset_link</a><br><br>
                        <small>Dans un environnement réel, ce lien serait envoyé par email.</small>";
        } else {
            $error = "Aucun compte trouvé avec cette adresse email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Orientation Pro</title>
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
<?php
require_once "db.php";
require_once "mail_config.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");

    if (empty($email)) {
        $error = "Veuillez saisir votre adresse email.";
    } else {
        // Vérifier si l'email existe
        $stmt = $pdo->prepare("SELECT id_user, email FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Supprimer les anciens tokens pour cet utilisateur
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE id_user = ?");
            $stmt->execute([$user["id_user"]]);

            // Insérer le nouveau token
            $stmt = $pdo->prepare("INSERT INTO password_resets (id_user, code_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user["id_user"], $token, $expires_at]);

            // Lien de réinitialisation
            $reset_link = "http://" . $_SERVER["HTTP_HOST"] . "/OrientationPro/reset_password.php?token=" . $token;

            // Envoyer l'email
            if (sendResetEmail($email, $reset_link)) {
                $message = "Un email de réinitialisation a été envoyé à <strong>" . htmlspecialchars($email) . "</strong>. Veuillez vérifier votre boîte de réception (et vos spams).";
            } else {
                $error = "Une erreur est survenue lors de l'envoi de l'email. Veuillez réessayer.";
            }
        } else {
            $error = "Aucun compte trouvé avec cette adresse email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Orientation Pro</title>
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

        .success-msg {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #135200;
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

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="logo-area">🎓 Orientation Pro</a>

    <div class="card">
        <h2>Mot de passe oublié ?</h2>
        <p class="subtitle">Saisissez votre email pour recevoir un lien de réinitialisation</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success-msg"><?= $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Adresse Email</label>
                <input type="email" name="email" placeholder="exemple@mail.com" required>
            </div>

            <button type="submit" class="btn-submit">Envoyer le lien</button>
        </form>

        <div class="footer-links">
            <a href="login.php">← Retour à la connexion</a>
        </div>
    </div>
</div>

</body>
</html>
        </div>
    </div>
</div>

</body>
</html>