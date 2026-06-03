<?php
session_start();
require_once "db.php";

$message = "";
$success = "";

// Récupérer la liste des wilayas pour le select
$wilayas = [
    "Adrar", "Chlef", "Laghouat", "Oum El Bouaghi", "Batna", "Béjaïa", "Biskra", "Béchar", "Blida", "Bouira",
    "Tamanrasset", "Tébessa", "Tlemcen", "Tiaret", "Tizi Ouzou", "Alger", "Djelfa", "Jijel", "Sétif", "Saïda",
    "Skikda", "Sidi Bel Abbès", "Annaba", "Guelma", "Constantine", "Médéa", "Mostaganem", "M'Sila", "Mascara",
    "Ouargla", "Oran", "El Bayadh", "Bordj Bou Arreridj", "Boumerdès", "El Tarf", "Tindouf", "Tissemsilt",
    "El Oued", "Khenchela", "Souk Ahras", "Tipaza", "Mila", "Aïn Defla", "Naâma", "Aïn Témouchent", "Ghardaïa",
    "Relizane", "Timimoun", "Bordj Badji Mokhtar", "Ouled Djellal", "Béni Abbès", "El Ménia", "Touggourt",
    "Djanet", "El Meghaier", "Sidi Okba", "Sétif"
];

// Récupérer les séries BAC
$stmt = $pdo->query("SELECT id_filiere_bac, nom_filiere FROM filieres_bac ORDER BY nom_filiere");
$series_bac = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"] ?? "");
    $prenom = trim($_POST["prenom"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $wilaya = trim($_POST["wilaya"] ?? "");
    $serie_bac = trim($_POST["serie_bac"] ?? "");

    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($wilaya) || empty($serie_bac)) {
        $message = "Veuillez remplir tous les champs.";
    } elseif (strlen($password) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id_user FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = "Cet email est déjà utilisé.";
        } else {
            // Hachage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, password_hash, wilaya, serie_bac, role) VALUES (?, ?, ?, ?, ?, ?, 'USER')");
            
            if ($stmt->execute([$nom, $prenom, $email, $hash, $wilaya, $serie_bac])) {
                // Redirection vers login avec message de succès
                header("Location: login.php?success=1");
                exit();
            } else {
                $message = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Orientation Pro</title>
    <style>
        /* --- STYLE IDENTIQUE À LOGIN.PHP --- */
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background-color: #f0f2f5; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .register-card {
            background: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .register-card h2 {
            margin-bottom: 25px;
            color: #001529;
            font-size: 24px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #595959;
            font-size: 14px;
        }

        .form-group label .required {
            color: #ff4d4f;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d9d9d9;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: 0.3s;
            font-family: inherit;
        }

        .form-group input:focus, .form-group select:focus {
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
            font-size: 13px;
            text-align: left;
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
            font-size: 26px;
            font-weight: bold;
            color: #001529;
            margin-bottom: 20px;
            display: block;
            text-decoration: none;
            text-align: center;
        }

        /* Info tooltip */
        .info-tip {
            font-size: 12px;
            color: #8c8c8c;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>

<div class="register-container">
    <a href="index.php" class="logo-area">🎓 Orientation Pro</a>

    <div class="register-card">
        <h2>Créer un compte</h2>

        <?php if (!empty($message)): ?>
            <div class="error-msg"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom <span class="required">*</span></label>
                    <input type="text" name="prenom" placeholder="Ex: Jean" required>
                </div>
                <div class="form-group">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="nom" placeholder="Ex: Dupont" required>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse Email <span class="required">*</span></label>
                <input type="email" name="email" placeholder="exemple@mail.com" required>
            </div>

            <div class="form-group">
                <label>Mot de passe <span class="required">*</span></label>
                <input type="password" name="password" placeholder="8 caractères minimum" required>
                <span class="info-tip">🔒 Minimum 8 caractères</span>
            </div>

            <div class="form-group">
                <label>Wilaya <span class="required">*</span></label>
                <select name="wilaya" required>
                    <option value="">-- Choisissez votre wilaya --</option>
                    <?php foreach($wilayas as $w): ?>
                        <option value="<?= htmlspecialchars($w); ?>"><?= htmlspecialchars($w); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="info-tip">📍 Facilite les recommandations localisées</span>
            </div>

            <div class="form-group">
                <label>Série BAC <span class="required">*</span></label>
                <select name="serie_bac" required>
                    <option value="">-- Choisissez votre série --</option>
                    <?php foreach($series_bac as $serie): ?>
                        <option value="<?= htmlspecialchars($serie['nom_filiere']); ?>">
                            <?= htmlspecialchars($serie['nom_filiere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="info-tip">🎓 Les matières et coefficients seront adaptés</span>
            </div>

            <button type="submit" class="btn-submit">S'inscrire</button>
        </form>

        <div class="footer-links">
            Déjà inscrit ? <a href="login.php">Se connecter</a>
        </div>
    </div>
</div>

</body>
</html>