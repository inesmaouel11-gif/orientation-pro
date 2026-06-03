<?php
require_once "guard.php";
require_once "db.php";

if ($_SESSION["user_role"] !== "ADMIN") {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM filieres ORDER BY id_filiere DESC");
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Filières - Admin</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR (Identique au Dashboard Admin) --- */
        .sidebar {
            width: 250px;
            background-color: #001529;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed; /* Fixe le menu pour le scroll */
            height: 100vh;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            color: white;
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
            font-size: 15px;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: #1890ff;
        }
        .nav-icon { margin-right: 15px; font-size: 18px; }

        /* --- MAIN CONTENT --- */
        .main-wrapper {
            flex: 1;
            margin-left: 250px; /* Espace pour la sidebar fixe */
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

        .content-area { padding: 30px; }
        
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .content-title { margin: 0; color: #333; font-size: 24px; }

        /* --- BOUTON AJOUTER --- */
        .btn-add {
            background-color: #1890ff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-add:hover { background-color: #40a9ff; }

        /* --- STYLISATION DU TABLEAU --- */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #333;
        }
        th {
            background-color: #fafafa;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        tr:hover { background-color: #fcfcfc; }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active { background: #e6f7ff; color: #1890ff; }
        .status-inactive { background: #fff1f0; color: #f5222d; }

        .actions a {
            text-decoration: none;
            margin-right: 10px;
            font-weight: 500;
        }
        .edit-link { color: #1890ff; }
        .delete-link { color: #ff4d4f; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">⚙️ Admin Panel</div>
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
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;">
                <span class="nav-icon">🚪</span> Déconnexion
            </a>
        </div>
    </div>

    <div class="main-wrapper">
        
        <div class="top-header">
            <div style="font-weight: 500;">🛡️ Admin : <?= htmlspecialchars($_SESSION["user_prenom"]); ?></div>
        </div>

        <div class="content-area">
            <div class="header-flex">
                <h1 class="content-title">Liste des filières</h1>
                <a href="ajouter_filiere.php" class="btn-add">+ Ajouter une filière</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Domaine</th>
                            <th>Diplôme</th>
                            <th>Durée</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filieres as $filiere): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($filiere["nom"]); ?></strong></td>
                                <td><?= htmlspecialchars($filiere["domaine"] ?? "-"); ?></td>
                                <td><?= htmlspecialchars($filiere["diplome"] ?? "-"); ?></td>
                                <td><?= htmlspecialchars($filiere["duree"] ?? "-"); ?> ans</td>
                                <td>
                                    <span class="status-badge <?= $filiere["is_active"] ? 'status-active' : 'status-inactive'; ?>">
                                        <?= $filiere["is_active"] ? "Actif" : "Inactif"; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="modifier_filiere.php?id=<?= $filiere["id_filiere"]; ?>" class="edit-link">Modifier</a>
                                    <a href="supprimer_filiere.php?id=<?= $filiere["id_filiere"]; ?>" class="delete-link" onclick="return confirm('Supprimer cette filière ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>