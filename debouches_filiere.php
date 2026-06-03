<?php
// debouches_filiere.php - Afficher les débouchés d'une filière
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

// Récupérer les débouchés (métiers)
$sql = "
    SELECT dm.* 
    FROM debouches_metiers dm
    JOIN filiere_metier fm ON fm.id_metier = dm.id_metier
    WHERE fm.id_filiere = ?
    ORDER BY dm.intitule
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_filiere]);
$metiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Débouchés - <?= htmlspecialchars($filiere['nom']); ?> - Orientation Pro</title>
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
        .metiers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        .metier-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-left: 4px solid #1890ff;
        }
        .metier-card:hover {
            transform: translateY(-3px);
        }
        .metier-titre {
            font-size: 18px;
            font-weight: 600;
            color: #001529;
            margin-bottom: 8px;
        }
        .metier-secteur {
            display: inline-block;
            background: #e6f4ff;
            color: #1890ff;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .metier-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .empty-message {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 16px;
            color: #888;
        }
        .btn-back {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            margin-top: 25px;
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
            .metiers-grid { grid-template-columns: 1fr; }
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
            <h1 class="page-title">🎯 Débouchés professionnels</h1>
            
            <div class="filiere-info">
                <h2><?= htmlspecialchars($filiere['nom']); ?></h2>
                <p><strong>Domaine :</strong> <?= htmlspecialchars($filiere['domaine'] ?? 'Non spécifié'); ?></p>
                <p><strong>Durée :</strong> <?= htmlspecialchars($filiere['duree'] ?? 'Non spécifiée'); ?></p>
                <p><strong>Diplôme :</strong> <?= htmlspecialchars($filiere['diplome'] ?? 'Non spécifié'); ?></p>
                <?php if($filiere['description']): ?>
                    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($filiere['description'])); ?></p>
                <?php endif; ?>
            </div>

            <?php if (count($metiers) > 0): ?>
                <div class="metiers-grid">
                    <?php foreach ($metiers as $metier): ?>
                        <div class="metier-card">
                            <div class="metier-titre"><?= htmlspecialchars($metier['intitule']); ?></div>
                            <div class="metier-secteur"><?= htmlspecialchars($metier['secteur'] ?? 'Secteur non spécifié'); ?></div>
                            <div class="metier-description"><?= htmlspecialchars($metier['description'] ?? 'Aucune description disponible.'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-message">
                    <div style="font-size: 48px; margin-bottom: 15px;">🎯</div>
                    <p>Aucun débouché n'est encore référencé pour cette filière.</p>
                    <p>Les informations seront bientôt disponibles.</p>
                </div>
            <?php endif; ?>

            <div style="text-align: center;">
                <a href="filieres.php" class="btn-back">← Retour à la liste des filières</a>
            </div>
        </div>
    </div>

</body>
</html>