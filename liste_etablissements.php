<?php
require_once "guard.php";
require_once "db.php";

if ($_SESSION["user_role"] !== "ADMIN") {
    header("Location: login.php");
    exit();
}

// Récupérer le message de succès éventuel
$success_message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'ajout') {
        $success_message = '✅ Établissement ajouté avec succès !';
    } elseif ($_GET['success'] == 'modification') {
        $success_message = '✅ Établissement modifié avec succès !';
    } elseif ($_GET['success'] == 'suppression') {
        $success_message = '✅ Établissement supprimé avec succès !';
    }
}

$sql = "SELECT * FROM etablissements ORDER BY nom ASC";
$stmt = $pdo->query($sql);
$etablissements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des établissements - Admin</title>
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

        /* --- MESSAGES --- */
        .success-message {
            background-color: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #52c41a;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* --- BOUTON AJOUTER --- */
        .btn-add {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: 0.3s;
            font-weight: 500;
        }

        .btn-add:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }

        /* --- TABLEAU --- */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
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

        /* --- BOUTONS ACTIONS --- */
        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            transition: 0.3s;
        }

        .btn-edit:hover {
            background-color: #40a9ff;
        }

        .btn-delete {
            background-color: #ff4d4f;
            color: white;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            transition: 0.3s;
        }

        .btn-delete:hover {
            background-color: #ff7875;
        }

        /* --- RECHERCHE --- */
        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s;
        }

        .search-box input:focus {
            border-color: #1890ff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
        }

        /* --- BOUTON RETOUR --- */
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
            .table-container {
                border-radius: 12px;
            }
            th, td {
                padding: 8px 10px;
                font-size: 12px;
            }
            .actions {
                flex-direction: column;
                gap: 5px;
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
            <a href="liste_etablissements.php" class="nav-link active">
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
            <h1 class="page-title">🏫 Liste des établissements</h1>

            <?php if ($success_message): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <a href="ajouter_etablissement.php" class="btn-add">➕ Ajouter un établissement</a>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Rechercher un établissement..." onkeyup="searchTable()">
            </div>

            <div class="table-container">
                <table id="etablissementsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Wilaya</th>
                            <th>Adresse</th>
                            <th>Site web</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($etablissements as $etab): ?>
                            <tr>
                                <td><?= $etab["id_etab"] ?></td>
                                <td><strong><?= htmlspecialchars($etab["nom"]) ?></strong></td>
                                <td><?= htmlspecialchars($etab["wilaya"] ?? '-') ?></td>
                                <td><?= htmlspecialchars($etab["adresse"] ?? '-') ?></td>
                                <td>
                                    <?php if(!empty($etab["site_web"])): ?>
                                        <a href="<?= htmlspecialchars($etab["site_web"]) ?>" target="_blank" style="color:#1890ff;"><?= htmlspecialchars($etab["site_web"]) ?></a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="modifier_etablissement.php?id=<?= $etab["id_etab"] ?>" class="btn-edit">✏️ Modifier</a>
                                    <a href="supprimer_etablissement.php?id=<?= $etab["id_etab"] ?>" class="btn-delete" onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet établissement ? Cette action est irréversible.')">🗑️ Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="dashboard_admin.php" class="btn-back">← Retour au tableau de bord</a>
            </div>
        </div>
    </div>

    <script>
        function searchTable() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let table = document.getElementById('etablissementsTable');
            let rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length - 1; j++) {
                    let cellText = cells[j].innerText.toLowerCase();
                    if (cellText.indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>

</body>
</html>