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

// Vérifier que la filière existe
$stmt = $pdo->prepare("SELECT * FROM filieres WHERE id_filiere = ?");
$stmt->execute([$id]);
$filiere = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filiere) {
    die("Filière introuvable.");
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["confirm_delete"])) {
        // Supprimer la filière
        $sql = "DELETE FROM filieres WHERE id_filiere = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        header("Location: liste_filieres.php?success=suppression");
        exit();
    } else {
        header("Location: liste_filieres.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer une filière - Admin</title>
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

        .main-wrapper {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
        }

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

        .content-area {
            padding: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .confirmation-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            max-width: 500px;
            text-align: center;
        }

        .warning-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .confirmation-card h3 {
            color: #ff4d4f;
            margin-bottom: 15px;
        }

        .confirmation-card p {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .filiere-name {
            background-color: #fff2f0;
            padding: 10px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 600;
            color: #ff4d4f;
        }

        .btn-danger {
            background-color: #ff4d4f;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-right: 10px;
        }

        .btn-danger:hover {
            background-color: #ff7875;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
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
            <h1 class="page-title">🗑️ Supprimer une filière</h1>

            <div class="confirmation-card">
                <div class="warning-icon">⚠️</div>
                <h3>Confirmation de suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer cette filière ?</p>
                <p>Cette action est <strong>irréversible</strong> et supprimera également :</p>
                <ul style="text-align: left; margin: 15px 0 15px 25px; color: #666;">
                    <li>Les seuils d'admission associés</li>
                    <li>Les associations avec les établissements</li>
                    <li>Les favoris des utilisateurs</li>
                </ul>
                <div class="filiere-name">
                    🎓 <?php echo htmlspecialchars($filiere["nom"]); ?>
                </div>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="confirm_delete" class="btn-danger">🗑️ Oui, supprimer</button>
                </form>
                <a href="liste_filieres.php" class="btn-cancel">❌ Non, annuler</a>
            </div>
        </div>
    </div>

</body>
</html>