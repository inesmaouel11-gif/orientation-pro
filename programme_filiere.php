<?php
// programme_filiere.php - Afficher le programme d'une filière
require_once "guard.php";
require_once "db.php";

if (!isset($_GET['id'])) {
    die("Filière non spécifiée.");
}

$id_filiere = intval($_GET['id']);

// Récupérer les infos de la filière
$stmt = $pdo->prepare("SELECT * FROM filieres WHERE id_filiere = ?");
$stmt->execute([$id_filiere]);
$filiere = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filiere) {
    die("Filière introuvable.");
}

// Récupérer le programme
$stmt = $pdo->prepare("SELECT * FROM programmes WHERE id_filiere = ? ORDER BY annee_version DESC LIMIT 1");
$stmt->execute([$id_filiere]);
$programme = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les modules du programme
$modules = [];
if ($programme) {
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE id_programme = ? ORDER BY semestre, nom");
    $stmt->execute([$programme['id_programme']]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Regrouper les modules par semestre
$modules_par_semestre = [];
foreach ($modules as $module) {
    $semestre = $module['semestre'] ?? 'Non défini';
    if (!isset($modules_par_semestre[$semestre])) {
        $modules_par_semestre[$semestre] = [];
    }
    $modules_par_semestre[$semestre][] = $module;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programme - <?= htmlspecialchars($filiere['nom']); ?> - Orientation Pro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Segoe UI", sans-serif;
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
        .nav-icon { margin-right: 15px; }
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
        .user-profile { display: flex; align-items: center; gap: 10px; }
        .content-area { padding: 30px; }
        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 10px;
        }
        .filiere-info {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .filiere-info h2 {
            color: #001529;
            margin-bottom: 10px;
        }
        .filiere-info p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 5px;
        }
        .programme-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .programme-card h3 {
            color: #001529;
            margin-bottom: 20px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }
        .semestre-section {
            margin-bottom: 30px;
        }
        .semestre-title {
            background: #e6f4ff;
            padding: 10px 15px;
            border-radius: 8px;
            color: #1890ff;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .module-item {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #eef2f6;
            transition: 0.3s;
        }
        .module-item:hover {
            border-color: #1890ff;
            box-shadow: 0 2px 8px rgba(24,144,255,0.1);
        }
        .module-nom {
            font-weight: 600;
            color: #001529;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .module-info {
            display: flex;
            gap: 20px;
            font-size: 12px;
            color: #888;
            margin-top: 8px;
        }
        .empty-message {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 16px;
            color: #888;
        }
        .empty-message .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .btn-back {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn-back:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-brand { font-size: 0; padding: 20px 0; }
            .sidebar-brand span { font-size: 24px; }
            .nav-link span:not(.nav-icon) { display: none; }
            .main-wrapper { margin-left: 70px; }
            .modules-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🎓 <span>Orientation Pro</span></div>
        <div class="nav-menu">
            <a href="dashboard_user.php" class="nav-link"><span class="nav-icon">⊞</span> Dashboard</a>
            <a href="releve_notes.php" class="nav-link"><span class="nav-icon">📄</span> Mon Relevé</a>
            <a href="filieres.php" class="nav-link"><span class="nav-icon">📚</span> Filières</a>
            <a href="releve_notes.php" class="nav-link"><span class="nav-icon">📊</span> Recommandations</a>
            <a href="mes_favoris.php" class="nav-link"><span class="nav-icon">❤️</span> Favoris</a>
            <a href="historique.php" class="nav-link"><span class="nav-icon">🕒</span> Historique</a>
            <a href="profil.php" class="nav-link"><span class="nav-icon">👤</span> Mon profil</a>
            <br>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;"><span class="nav-icon">🚪</span> Déconnexion</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-header">
            <div class="user-profile">
                👤 <?= htmlspecialchars($_SESSION["user_prenom"] . " " . $_SESSION["user_nom"]); ?>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">📖 Programme de formation</h1>
            
            <div class="filiere-info">
                <h2><?= htmlspecialchars($filiere['nom']); ?></h2>
                <p><strong>Domaine :</strong> <?= htmlspecialchars($filiere['domaine'] ?? 'Non spécifié'); ?></p>
                <p><strong>Durée :</strong> <?= htmlspecialchars($filiere['duree'] ?? 'Non spécifiée'); ?></p>
                <p><strong>Diplôme :</strong> <?= htmlspecialchars($filiere['diplome'] ?? 'Non spécifié'); ?></p>
            </div>

            <div class="programme-card">
                <h3>📚 Programme pédagogique</h3>
                
                <?php if ($programme && count($modules) > 0): ?>
                    <?php if ($programme['description']): ?>
                        <p style="margin-bottom: 20px; color: #666;"><?= htmlspecialchars($programme['description']); ?></p>
                    <?php endif; ?>
                    
                    <?php foreach ($modules_par_semestre as $semestre => $mods): ?>
                        <div class="semestre-section">
                            <div class="semestre-title">📌 <?= htmlspecialchars($semestre); ?></div>
                            <div class="modules-grid">
                                <?php foreach ($mods as $module): ?>
                                    <div class="module-item">
                                        <div class="module-nom"><?= htmlspecialchars($module['nom']); ?></div>
                                        <div class="module-info">
                                            <?php if ($module['credits']): ?>
                                                <span>🎓 Crédits: <?= $module['credits']; ?></span>
                                            <?php endif; ?>
                                            <?php if ($module['volume_horaire']): ?>
                                                <span>⏱️ Volume: <?= $module['volume_horaire']; ?>h</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <div class="icon">📚</div>
                        <p>Le programme pédagogique de cette filière n'est pas encore disponible.</p>
                        <p>Revenez bientôt pour plus d'informations !</p>
                    </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center;">
                <a href="filieres.php" class="btn-back">← Retour à la liste des filières</a>
            </div>
        </div>
    </div>

</body>
</html>