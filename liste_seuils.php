<?php
session_start(); // 🔥 IMPORTANT : Démarrer la session au tout début

require_once "db.php";

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "ADMIN") {
    header("Location: login.php");
    exit();
}

// Requête SQL corrigée (utilisation de min1 au lieu de moyenne_min)
$sql = "SELECT 
    seuils_admission.id_seuil,
    filieres.nom AS filiere,
    etablissements.nom AS etablissement,
    seuils_admission.annee,
    seuils_admission.serie_bac,
    seuils_admission.wilaya,
    seuils_admission.min1 as seuil
FROM seuils_admission
JOIN filieres ON seuils_admission.id_filiere = filieres.id_filiere
JOIN etablissements ON seuils_admission.id_etab = etablissements.id_etab
ORDER BY seuils_admission.annee DESC, seuils_admission.min1 DESC";

$stmt = $pdo->query($sql);
$seuils = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des seuils d'admission - Admin</title>
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
        }

        .content-area {
            padding: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 20px;
        }

        .btn-add {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .btn-add:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }

        .table-container {
            background: white;
            border-radius: 16px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #001529;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eef2f6;
            color: #333;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f8f9fc;
        }

        .seuil-value {
            font-weight: 600;
            color: #1890ff;
        }

        .actions a {
            text-decoration: none;
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        .edit-link {
            background-color: #1890ff;
            color: white;
        }

        .delete-link {
            background-color: #ff4d4f;
            color: white;
        }

        .edit-link:hover, .delete-link:hover {
            opacity: 0.8;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1890ff;
            text-decoration: none;
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
            <a href="liste_filieres.php" class="nav-link">
                <span class="nav-icon">📋</span> Filières
            </a>
            <a href="liste_etablissements.php" class="nav-link">
                <span class="nav-icon">🏫</span> Établissements
            </a>
            <a href="liste_filiere_etablissement.php" class="nav-link">
                <span class="nav-icon">🔗</span> Associations
            </a>
            <a href="liste_seuils.php" class="nav-link active">
                <span class="nav-icon">📊</span> Seuils
            </a>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;">
                <span class="nav-icon">🚪</span> Déconnexion
            </a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-header">
            <div class="user-profile">
                🛡️ <?php 
                // Vérifier que la variable existe avant de l'afficher
                $admin_nom = isset($_SESSION["user_prenom"]) ? $_SESSION["user_prenom"] : "Admin";
                $admin_prenom = isset($_SESSION["user_nom"]) ? $_SESSION["user_nom"] : "";
                echo htmlspecialchars($admin_nom . " " . $admin_prenom); 
                ?> (Admin)
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">📊 Liste des seuils d'admission</h1>

            <a href="ajouter_seuil.php" class="btn-add">➕ Ajouter un seuil</a>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Filière</th>
                            <th>Établissement</th>
                            <th>Année</th>
                            <th>Série BAC</th>
                            <th>Wilaya</th>
                            <th>Seuil (min1)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($seuils) > 0): ?>
                            <?php foreach($seuils as $s): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($s["filiere"] ?? 'N/A'); ?></strong></td>
                                    <td><?= htmlspecialchars($s["etablissement"] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($s["annee"] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($s["serie_bac"] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($s["wilaya"] ?: 'Toutes'); ?></td>
                                    <td class="seuil-value"><?= number_format($s["seuil"] ?? 0, 2); ?></td>
                                    <td class="actions">
                                        <a href="modifier_seuil.php?id=<?= $s["id_seuil"] ?>" class="edit-link">✏️ Modifier</a>
                                        <a href="supprimer_seuil.php?id=<?= $s["id_seuil"] ?>" class="delete-link" onclick="return confirm('Supprimer ce seuil ?')">🗑️ Supprimer</a>
                                    </span
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">📭 Aucun seuil d'admission trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                <a href="dashboard_admin.php" class="back-link">← Retour au tableau de bord</a>
            </div>
        </div>
    </div>

</body>
</html>