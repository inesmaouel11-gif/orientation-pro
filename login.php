<?php
session_start();
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember_me = isset($_POST["remember_me"]);

    if (empty($email) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {
            // Stockage des informations de base dans la session
            $_SESSION["user_id"] = $user["id_user"];
            $_SESSION["user_nom"] = $user["nom"];
            $_SESSION["user_prenom"] = $user["prenom"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];
            
            // 🔥 NOUVEAU : Stocker wilaya et série BAC dans la session
            $_SESSION["user_wilaya"] = $user["wilaya"] ?? null;
            $_SESSION["user_serie_bac"] = $user["serie_bac"] ?? null;

            // 🔥 "Se souvenir de moi" (optionnel)
            if ($remember_me) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + 30*24*3600, '/');
                $stmt = $pdo->prepare("UPDATE utilisateurs SET remember_token = ? WHERE id_user = ?");
                $stmt->execute([$token, $user["id_user"]]);
            }

            if ($user["role"] === "ADMIN") {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit();
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Orientation Pro</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 30px;
            color: #001529;
            font-size: 24px;
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
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #1890ff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .checkbox-group input {
            width: auto;
            margin: 0;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #1890ff;
            color: white;
            border: none;
            border-radius: 4px;
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
            color: #ff4d4f;
            background: #fff2f0;
            border: 1px solid #ffccc7;
            padding: 10px;
            border-radius: 4px;
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
            font-weight: 500;
        }

        .logo-area {
            font-size: 28px;
            font-weight: bold;
            color: #001529;
            margin-bottom: 20px;
            display: block;
            text-decoration: none;
        }

        .forgot-password {
            text-align: right;
            margin-top: 5px;
        }

        .forgot-password a {
            font-size: 12px;
            color: #8c8c8c;
            text-decoration: none;
        }

        .forgot-password a:hover {
            color: #1890ff;
        }
    </style>
</head>
<body>

<div class="login-container">
    <a href="index.php" class="logo-area">🎓 Orientation Pro</a>

    <div class="login-card">
        <h2>Connexion</h2>

        <?php if (!empty($message)): ?>
            <div class="error-msg"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Adresse Email</label>
                <input type="email" name="email" placeholder="exemple@mail.com" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="remember_me" id="remember_me">
                <label for="remember_me">Se souvenir de moi</label>
            </div>

            <div class="forgot-password">
                <a href="forgot_password.php">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <div class="footer-links">
            Pas encore de compte ? <a href="register.php">Créer un compte</a>
        </div>
    </div>
</div>

</body>
</html>