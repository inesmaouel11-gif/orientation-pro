<?php
require_once "guard.php";
require_once "db.php";

if ($_SESSION["user_role"] !== "ADMIN") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    die("ID filière manquant.");
}

$id = (int) $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM filieres WHERE id_filiere = ?");
$stmt->execute([$id]);
$filiere = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filiere) {
    die("Filière introuvable.");
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code_filiere = trim($_POST["code_filiere"] ?? "");
    $nom = trim($_POST["nom"] ?? "");
    $domaine = trim($_POST["domaine"] ?? "");
    $duree = trim($_POST["duree"] ?? "");
    $diplome = trim($_POST["diplome"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $is_active = isset($_POST["is_active"]) ? 1 : 0;

    if (empty($nom)) {
        $error = "Le nom de la filière est obligatoire.";
    } else {
        $sql = "UPDATE filieres
                SET code_filiere = ?, nom = ?, domaine = ?, duree = ?, diplome = ?, description = ?, is_active = ?
                WHERE id_filiere = ?";
        $update = $pdo->prepare($sql);
        $update->execute([$code_filiere, $nom, $domaine, $duree, $diplome, $description, $is_active, $id]);

        $message = "✅ Filière modifiée avec succès.";

        $stmt = $pdo->prepare("SELECT * FROM filieres WHERE id_filiere = ?");
        $stmt->execute([$id]);
        $filiere = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une filière - Admin</title>
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
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 250px;
            background-color: #001529;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #1a2c3f;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            padding-top: 10px;
            flex: 1;
        }

        .nav-link {
            padding: 15px 25px;
            color: #a6adb4;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: #1890ff;
        }

        .nav-icon {
            margin-right: 15px;
        }

        /* --- MAIN WRAPPER --- */
        .main-wrapper {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
        }

        /* --- TOP HEADER --- */
        .top-header {
            background-color: white;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 30px;
            box-shadow: 0 1px 4px rgba(0,21,41,0.08);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            font-weight: 500;
        }

        /* --- CONTENT AREA --- */
        .content-area {
            padding: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* --- FORM CARD --- */
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            max-width: 700px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group label .required {
            color: #ff4d4f;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: 0.3s;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #1890ff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
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
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }

        .btn-back {
            display: inline-block;
            background-color: #722ed1;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #9254de;
            transform: translateY(-2px);
        }

        .success-message {
            background-color: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #52c41a;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-message {
            background-color: #fff2f0;
            border: 1px solid #ffccc7;
            color: #ff4d4f;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-badge {
            background-color: #e6f4ff;
            padding: 8px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #1890ff;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-brand {
                font-size: 0;
                padding: 20px 0;
            }
            .sidebar-brand span {
                font-size: 24px;
            }
            .nav-link span:not(.nav-icon) {
                display: none;
            }
            .main-wrapper {
                margin-left: 70px;
            }
            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">⚙️ <span>Admin Panel</span></div>
        <div class="nav-menu">
            <a href="dashboard_admin.php" class="nav-link">
                <span class="nav-icon">⊞</span> Dashboard
            </a>
            <a href="liste_filieres.php" class="nav-link active">
                <span class="nav-icon">📋</span> Filières
            </a>
            <a href="liste_etablissements.php" class="nav-link">
                <span class="nav-icon">🏫</span> Établissements
            </a>
            <a href="liste_filiere_etablissement.php" class="nav-link">
                <span class="nav-icon">🔗</span> Associations
            </a>
            <a href="liste_seuils.php" class="nav-link">
                <span class="nav-icon">📊</span> Seuils
            </a>
            <a href="admin_messages.php" class="nav-link">
                <span class="nav-icon">📨</span> Messages
            </a>
            <br>
            <a href="a_propos.php" class="nav-link">
                <span class="nav-icon">ℹ️</span> À propos
            </a>
            <a href="faq.php" class="nav-link">
                <span class="nav-icon">❓</span> FAQ
            </a>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;">
                <span class="nav-icon">🚪</span> Déconnexion
            </a>
        </div>
    </div>

    <div class="main-wrapper">
        
        <div class="top-header">
            <div class="user-profile">
                🛡️ <?php echo htmlspecialchars($_SESSION["user_prenom"] . " " . $_SESSION["user_nom"]); ?> (Admin)
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">✏️ Modifier une filière</h1>

            <?php if (!empty($message)): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="info-badge">
                📌 Vous êtes en train de modifier : <strong><?php echo htmlspecialchars($filiere["nom"]); ?></strong>
            </div>

            <div class="form-card">
                <form method="POST">
                    <div class="form-group">
                        <label>Code de la filière <span class="required">*</span></label>
                        <input type="text" name="code_filiere" value="<?php echo htmlspecialchars($filiere["code_filiere"] ?? ''); ?>" placeholder="Ex: INF, MED, GC..." required>
                        <small style="color: #888; font-size: 12px;">Code unique (3 lettres majuscules)</small>
                    </div>

                    <div class="form-group">
                        <label>Nom de la filière <span class="required">*</span></label>
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($filiere["nom"]); ?>" placeholder="Ex: INFORMATIQUE" required>
                    </div>

                    <div class="form-group">
                        <label>Domaine</label>
                        <input type="text" name="domaine" value="<?php echo htmlspecialchars($filiere["domaine"] ?? ''); ?>" placeholder="Ex: Sciences et Technologies">
                    </div>

                    <div class="form-group">
                        <label>Durée</label>
                        <input type="text" name="duree" value="<?php echo htmlspecialchars($filiere["duree"] ?? ''); ?>" placeholder="Ex: 3 ans (Licence)">
                    </div>

                    <div class="form-group">
                        <label>Diplôme</label>
                        <input type="text" name="diplome" value="<?php echo htmlspecialchars($filiere["diplome"] ?? ''); ?>" placeholder="Ex: Licence / Master">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" placeholder="Description détaillée de la filière..."><?php echo htmlspecialchars($filiere["description"] ?? ''); ?></textarea>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="is_active" <?php echo ($filiere["is_active"] == 1) ? "checked" : ""; ?>>
                        <label for="is_active">✅ Filière active (visible par les utilisateurs)</label>
                    </div>

                    <button type="submit" class="btn-submit">💾 Enregistrer les modifications</button>
                </form>
            </div>

            <a href="liste_filieres.php" class="btn-back">← Retour à la liste des filières</a>
        </div>
    </div>

</body>
</html>