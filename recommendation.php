<?php
// =============================================
// RECOMMANDATION.PHP - AVEC FILTRES (ZONE + TYPE ÉTABLISSEMENT)
// =============================================

require_once "guard.php";
require_once "db.php";

// 🔥 Vérification des données (POST ou GET)
if (!isset($_POST['moyenne']) && !isset($_GET['moyenne'])) {
    die("⚠️ Veuillez passer par le formulaire");
}

// 🔥 Récupération de la moyenne et de la série (POST ou GET)
if (isset($_POST['moyenne'])) {
    $moyenne = floatval($_POST['moyenne']);
    $serie = $_POST['serie_bac'];
} else {
    $moyenne = floatval($_GET['moyenne']);
    $serie = $_GET['serie_bac'];
}

// 🔥 Récupération des filtres (GET uniquement)
$zone_filter = isset($_GET['zone']) ? $_GET['zone'] : 'all';
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';

// 🔥 Définition des zones géographiques - 58 WILAYAS COMPLÈTES
$zones = [
    'nord' => [
        'Alger', 'Tipaza', 'Blida', 'Boumerdes', 'Tizi Ouzou', 'Bejaia',
        'Chlef', 'Ain Defla', 'Medea', 'Bouira', 'Skikda', 'Annaba'
    ],
    'est' => [
        'Constantine', 'Setif', 'Batna', 'Tebessa', 'Oum El Bouaghi', 'Guelma',
        'Souk Ahras', 'Khenchela', 'Mila', 'Bordj Bou Arreridj', 'Jijel', 'El Tarf'
    ],
    'ouest' => [
        'Oran', 'Tlemcen', 'Sidi Bel Abbès', 'Mostaganem', 'Mascara', 'Saida',
        'Relizane', 'Ain Temouchent', 'Tiaret', 'El Bayadh', 'Naâma', 'Tissemsilt'
    ],
    'sud' => [
        'Adrar', 'Laghouat', 'Biskra', 'Ouargla', 'Ghardaia', 'Bechar',
        'Tamanrasset', 'Illizi', 'El Oued', 'Tindouf', 'Touggourt', 'Djanet',
        'Timimoun', 'Bordj Badji Mokhtar', 'Béni Abbès', 'El Méniaa', 'Ouled Djellal'
    ]
];

// 🔥 Définition des types d'établissements
$types_etablissements = [
    'universite' => ['Université'],
    'ecole_superieure' => ['Ecole supérieure', 'École Supérieure', 'Ecole Superieure'],
    'ecole_nationale' => ['Ecole nationale', 'École Nationale', 'Ecole Nationale']
];

// 🔥 Stocker en session pour les redirections futures
$_SESSION['last_moyenne'] = $moyenne;
$_SESSION['last_serie_bac'] = $serie;

$wilaya_user = $_SESSION['user_wilaya'] ?? null;

// 🔥 Construction de la condition WHERE pour la zone
if ($zone_filter !== 'all' && isset($zones[$zone_filter])) {
    $wilayas_zone = $zones[$zone_filter];
    $placeholders = implode(',', array_fill(0, count($wilayas_zone), '?'));
    $zone_condition = " AND e.wilaya IN ($placeholders) ";
    $zone_params = $wilayas_zone;
} else {
    $zone_condition = " AND (e.wilaya = ? OR e.wilaya IS NULL OR e.wilaya = '') ";
    $zone_params = [$wilaya_user];
}

// 🔥 Construction de la condition WHERE pour le type d'établissement
if ($type_filter !== 'all') {
    switch($type_filter) {
        case 'universite':
            $type_condition = " AND e.type = 'Université' ";
            break;
        case 'ecole_superieure':
            $type_condition = " AND e.type = 'Ecole supérieure' ";
            break;
        case 'ecole_nationale':
            $type_condition = " AND e.type = 'Ecole nationale' ";
            break;
        default:
            $type_condition = "";
    }
} else {
    $type_condition = "";
}

// 🔥 Vérifier si un message de confirmation existe dans la session
$success_message = '';
$error_message = '';

if (isset($_SESSION['favori_success'])) {
    $success_message = $_SESSION['favori_success'];
    unset($_SESSION['favori_success']);
}

if (isset($_SESSION['favori_error'])) {
    $error_message = $_SESSION['favori_error'];
    unset($_SESSION['favori_error']);
}

// =============================================
// 🔥 REQUÊTE SQL
// =============================================
$sql = "
SELECT 
    f.id_filiere,
    f.code_filiere,
    f.nom AS filiere,
    e.id_etab,
    e.code_etab,
    e.nom AS etablissement,
    e.wilaya,
    e.type,
    s.min1,
    s.min2,
    s.min3,
    s.serie_bac,
    COALESCE(i.employabilite, 50) as employabilite,
    COALESCE(i.popularite, 50) as popularite,
    COALESCE(i.tendance, 50) as tendance
FROM seuils_admission s
JOIN filieres f ON f.id_filiere = s.id_filiere
JOIN etablissements e ON e.id_etab = s.id_etab
LEFT JOIN indicateurs_filieres i ON i.id_filiere = f.id_filiere
WHERE LOWER(s.serie_bac) = LOWER(?)
AND (s.min1 IS NULL OR s.min1 <= ?)
$zone_condition
$type_condition
ORDER BY COALESCE(s.min1, 0) DESC
";

// Préparation des paramètres
$params = array_merge([$serie, $moyenne], $zone_params);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$resultats = $stmt->fetchAll();

// 🔥 Fonction pour calculer le score de pertinence
function calculerScorePertinence($moyenne, $min1, $employabilite, $popularite, $tendance) {
    if ($min1 > 0) {
        $scoreMoyenne = min(100, ($moyenne / $min1) * 100);
    } else {
        $scoreMoyenne = 100;
    }
    $scoreIndicateurs = ($employabilite * 0.4) + ($popularite * 0.3) + ($tendance * 0.3);
    $scoreFinal = round(($scoreMoyenne * 0.6) + ($scoreIndicateurs * 0.4), 1);
    return min(100, max(0, $scoreFinal));
}

// 🔥 Fonction pour calculer le pourcentage de chances d'admission
function calculerPourcentageChances($moyenne, $min1) {
    if ($min1 <= 0) return 95;
    if ($moyenne >= $min1) {
        $bonus = min(30, ($moyenne - $min1) * 10);
        return min(100, 70 + $bonus);
    } else {
        return round(($moyenne / $min1) * 65, 1);
    }
}

// 🔥 Fonction pour obtenir la couleur du score
function getScoreColor($score) {
    if ($score >= 80) return '#52c41a';
    if ($score >= 60) return '#1890ff';
    if ($score >= 40) return '#faad14';
    return '#ff4d4f';
}

// 🔥 Fonction pour obtenir la classe CSS du statut
function getStatutClasse($moyenne, $min1) {
    if ($min1 === null || $min1 <= 0) return 'status-accepted';
    if ($moyenne >= $min1) return 'status-accepted';
    if ($moyenne >= $min1 * 0.85) return 'status-borderline';
    return 'status-refused';
}

function getStatutTexte($moyenne, $min1) {
    if ($min1 === null || $min1 <= 0) return 'Accepté';
    if ($moyenne >= $min1) return 'Accepté';
    if ($moyenne >= $min1 * 0.85) return 'Limite';
    return 'Refusé';
}

// 🔥 Fonction pour obtenir le badge du type d'établissement
function getTypeBadge($type) {
    switch($type) {
        case 'Université':
            return '<span style="background-color:#e6f4ff; color:#1890ff; padding:3px 10px; border-radius:20px; font-size:12px;">🏛️ Université</span>';
        case 'Ecole supérieure':
            return '<span style="background-color:#f6ffed; color:#52c41a; padding:3px 10px; border-radius:20px; font-size:12px;">🎓 École supérieure</span>';
        case 'Ecole nationale':
            return '<span style="background-color:#fff7e6; color:#fa8c16; padding:3px 10px; border-radius:20px; font-size:12px;">⭐ École nationale</span>';
        default:
            return '<span style="background-color:#f0f2f5; color:#666; padding:3px 10px; border-radius:20px; font-size:12px;">🏫 Établissement</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommandations - Orientation Pro</title>
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
        .stats-badge {
            background: #e6f4ff;
            color: #1890ff;
            padding: 8px 20px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 25px;
            font-weight: 500;
        }
        
        /* 🔥 STYLES POUR LES FILTRES */
        .filters-container {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-group label {
            font-weight: 600;
            color: #001529;
        }
        .filter-group select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #d9d9d9;
            background: white;
            font-size: 14px;
            cursor: pointer;
        }
        .filter-group select:focus {
            border-color: #1890ff;
            outline: none;
        }
        .btn-apply-filter {
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-apply-filter:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }
        .btn-reset-filters {
            background-color: #ff4d4f;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-reset-filters:hover {
            background-color: #ff7875;
            transform: translateY(-2px);
        }
        
        .alert-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success {
            background-color: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #52c41a;
        }
        .alert-error {
            background-color: #fff2f0;
            border: 1px solid #ffccc7;
            color: #ff4d4f;
        }
        .alert-content { display: flex; align-items: center; gap: 10px; }
        .close-alert {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: inherit;
        }
        
        .export-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .btn-export-csv {
            background-color: #52c41a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-export-csv:hover {
            background-color: #73d13d;
            transform: translateY(-2px);
        }
        .btn-export-excel {
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-export-excel:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }
        .btn-print {
            background-color: #722ed1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-print:hover {
            background-color: #9254de;
            transform: translateY(-2px);
        }
        
        .results-container {
            background: white;
            border-radius: 16px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1400px;
        }
        .results-table th {
            background-color: #001529;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-size: 14px;
        }
        .results-table td {
            padding: 12px;
            border-bottom: 1px solid #eef2f6;
            color: #333;
            font-size: 14px;
        }
        .results-table tr:hover { background-color: #f8f9fc; }
        .status-accepted {
            background-color: #f6ffed;
            color: #52c41a;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            font-size: 13px;
        }
        .status-borderline {
            background-color: #fff7e6;
            color: #fa8c16;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            font-size: 13px;
        }
        .status-refused {
            background-color: #fff2f0;
            color: #ff4d4f;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            font-size: 13px;
        }
        .seuil-value { font-weight: 600; color: #1890ff; }
        .score-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            color: white;
            min-width: 60px;
            text-align: center;
        }
        .code-badge {
            background-color: #f0f2f5;
            padding: 5px 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            font-weight: 600;
            color: #001529;
            display: inline-block;
        }
        .btn-favori {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.2s;
        }
        .btn-favori:hover { transform: scale(1.2); }
        .empty-results {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }
        .empty-results-icon { font-size: 48px; margin-bottom: 15px; }
        .btn-back {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            margin-top: 25px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .btn-back:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }
        .progress-bar-container {
            width: 100%;
            background-color: #f0f2f5;
            border-radius: 10px;
            overflow: hidden;
            height: 8px;
            margin-top: 5px;
        }
        .progress-bar {
            height: 8px;
            border-radius: 10px;
            transition: width 0.3s;
        }
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-brand { font-size: 0; padding: 20px 0; }
            .sidebar-brand span { font-size: 24px; }
            .nav-link span:not(.nav-icon) { display: none; }
            .main-wrapper { margin-left: 70px; }
            .results-table th, .results-table td { padding: 8px 10px; font-size: 12px; }
            .export-buttons { justify-content: center; flex-wrap: wrap; }
            .filters-container { flex-direction: column; align-items: stretch; }
            .filter-group { justify-content: space-between; }
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
            <a href="releve_notes.php" class="nav-link active"><span class="nav-icon">📊</span> Recommandations</a>
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
                <?php if($wilaya_user): ?> | 📍 <?= htmlspecialchars($wilaya_user); ?><?php endif; ?>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">🎓 Recommandations d'orientation</h1>
            
            <div class="stats-badge">
                📊 Moyenne : <strong><?= number_format($moyenne, 2); ?>/20</strong> 
                &nbsp;|&nbsp; 🎓 Série : <strong><?= htmlspecialchars($serie); ?></strong>
                <?php if($wilaya_user): ?> &nbsp;|&nbsp; 📍 Wilaya : <strong><?= htmlspecialchars($wilaya_user); ?></strong><?php endif; ?>
            </div>

            <?php if ($success_message): ?>
                <div class="alert-message alert-success" id="alertMessage">
                    <div class="alert-content"><span>✅</span><span><?= htmlspecialchars($success_message); ?></span></div>
                    <button class="close-alert" onclick="closeAlert()">&times;</button>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert-message alert-error" id="alertMessage">
                    <div class="alert-content"><span>❌</span><span><?= htmlspecialchars($error_message); ?></span></div>
                    <button class="close-alert" onclick="closeAlert()">&times;</button>
                </div>
            <?php endif; ?>

            <!-- 🔥 FILTRES : ZONE + TYPE ÉTABLISSEMENT -->
            <form method="GET" action="recommendation.php" class="filters-container">
                <div class="filter-group">
                    <label>📍 Zone géographique :</label>
                    <select name="zone">
                        <option value="all" <?= $zone_filter == 'all' ? 'selected' : '' ?>>🌍 Toutes les zones (58 wilayas)</option>
                        <option value="nord" <?= $zone_filter == 'nord' ? 'selected' : '' ?>>⬆️ Nord (12 wilayas)</option>
                        <option value="est" <?= $zone_filter == 'est' ? 'selected' : '' ?>>➡️ Est (12 wilayas)</option>
                        <option value="ouest" <?= $zone_filter == 'ouest' ? 'selected' : '' ?>>⬅️ Ouest (12 wilayas)</option>
                        <option value="sud" <?= $zone_filter == 'sud' ? 'selected' : '' ?>>⬇️ Sud (17 wilayas)</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>🏛️ Type d'établissement :</label>
                    <select name="type">
                        <option value="all" <?= $type_filter == 'all' ? 'selected' : '' ?>>📚 Tous les types</option>
                        <option value="universite" <?= $type_filter == 'universite' ? 'selected' : '' ?>>🏛️ Universités</option>
                        <option value="ecole_superieure" <?= $type_filter == 'ecole_superieure' ? 'selected' : '' ?>>🎓 Écoles supérieures</option>
                        <option value="ecole_nationale" <?= $type_filter == 'ecole_nationale' ? 'selected' : '' ?>>⭐ Écoles nationales</option>
                    </select>
                </div>

                <!-- 🔥 Champs cachés pour conserver moyenne et série -->
                <input type="hidden" name="moyenne" value="<?= $moyenne ?>">
                <input type="hidden" name="serie_bac" value="<?= htmlspecialchars($serie) ?>">
                
                <button type="submit" class="btn-apply-filter">🔍 Appliquer les filtres</button>
                <a href="recommendation.php?moyenne=<?= $moyenne ?>&serie_bac=<?= urlencode($serie) ?>" class="btn-reset-filters">🗑️ Réinitialiser</a>
            </form>

            <!-- 🔥 BOUTONS D'EXPORT -->
            <div class="export-buttons">
                <button onclick="exportToCSV()" class="btn-export-csv">📊 Exporter CSV</button>
                <button onclick="exportToExcel()" class="btn-export-excel">📑 Exporter Excel</button>
                <button onclick="window.print()" class="btn-print">🖨️ Imprimer / PDF</button>
            </div>

            <div class="results-container">
                <?php if(count($resultats) > 0): ?>
                    <table class="results-table" id="resultsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Filière</th>
                                <th>Code Étab.</th>
                                <th>Établissement</th>
                                <th>Type</th>
                                <th>Wilaya</th>
                                <th>Min1</th>
                                <th>Min2</th>
                                <th>Min3</th>
                                <th>Score</th>
                                <th>Chances</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $compteur = 0;
                            $resultats_avec_scores = [];
                            foreach($resultats as $row) {
                                $score = calculerScorePertinence($moyenne, $row['min1'], $row['employabilite'], $row['popularite'], $row['tendance']);
                                $chances = calculerPourcentageChances($moyenne, $row['min1']);
                                $resultats_avec_scores[] = ['row' => $row, 'score' => $score, 'chances' => $chances];
                            }
                            usort($resultats_avec_scores, function($a, $b) { return $b['score'] <=> $a['score']; });
                            
                            foreach($resultats_avec_scores as $item): 
                                $compteur++;
                                $row = $item['row'];
                                $score = $item['score'];
                                $chances = $item['chances'];
                                $scoreColor = getScoreColor($score);
                                $statutClasse = getStatutClasse($moyenne, $row['min1']);
                                $statutTexte = getStatutTexte($moyenne, $row['min1']);
                                $typeBadge = getTypeBadge($row['type']);
                                
                                $min1_affiche = ($row['min1'] === null || $row['min1'] == 0) ? 'N/A' : number_format($row['min1'], 2);
                                $min2_affiche = ($row['min2'] === null || $row['min2'] == 0) ? 'N/A' : number_format($row['min2'], 2);
                                $min3_affiche = ($row['min3'] === null || $row['min3'] == 0) ? 'N/A' : number_format($row['min3'], 2);
                            ?>
                                <tr>
                                    <td style="text-align: center;"><?= $compteur; ?></td>
                                    <td><span class="code-badge"><?= htmlspecialchars($row['code_filiere'] ?? '---'); ?></span></td>
                                    <td><strong><?= htmlspecialchars($row['filiere']); ?></strong></td>
                                    <td><span class="code-badge"><?= htmlspecialchars($row['code_etab'] ?? '---'); ?></span></td>
                                    <td><?= htmlspecialchars($row['etablissement']); ?></td>
                                    <td><?= $typeBadge ?></td>
                                    <td><?= htmlspecialchars($row['wilaya'] ?? '---'); ?></td>
                                    <td class="seuil-value"><?= $min1_affiche; ?></td>
                                    <td class="seuil-value"><?= $min2_affiche; ?></td>
                                    <td class="seuil-value"><?= $min3_affiche; ?></td>
                                    <td style="text-align: center;">
                                        <span class="score-badge" style="background-color: <?= $scoreColor; ?>;"><?= $score; ?>%</span>
                                        <div class="progress-bar-container"><div class="progress-bar" style="width: <?= $score; ?>%; background-color: <?= $scoreColor; ?>;"></div></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong><?= $chances; ?>%</strong>
                                        <div class="progress-bar-container"><div class="progress-bar" style="width: <?= $chances; ?>%; background-color: <?= $chances >= 70 ? '#52c41a' : ($chances >= 40 ? '#faad14' : '#ff4d4f'); ?>;"></div></div>
                                    </td>
                                    <td><span class="<?= $statutClasse; ?>"><?= $statutTexte; ?></span></td>
                                    <td style="text-align: center;">
                                        <form method="POST" action="favori.php" style="margin:0;">
                                            <input type="hidden" name="id_filiere" value="<?= $row['id_filiere'] ?>">
                                            <input type="hidden" name="return_url" value="recommendation.php">
                                            <input type="hidden" name="moyenne" value="<?= $moyenne ?>">
                                            <input type="hidden" name="serie_bac" value="<?= htmlspecialchars($serie) ?>">
                                            <input type="hidden" name="zone" value="<?= $zone_filter ?>">
                                            <input type="hidden" name="type" value="<?= $type_filter ?>">
                                            <button type="submit" class="btn-favori" title="Ajouter aux favoris">⭐</button>
                                        </form>
                                    </span
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-results">
                        <div class="empty-results-icon">😕</div>
                        <p>Aucune recommandation trouvée pour votre moyenne avec ces filtres.</p>
                        <p>Essayez de modifier les filtres ou vérifiez votre série BAC.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="releve_notes.php" class="btn-back">← Retour au relevé de notes</a>
            </div>
        </div>
    </div>

    <script>
        function closeAlert() {
            const alert = document.getElementById('alertMessage');
            if (alert) {
                alert.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => { alert.remove(); }, 300);
            }
        }
        setTimeout(function() { const alert = document.getElementById('alertMessage'); if (alert) closeAlert(); }, 5000);
        
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `@keyframes slideOut { from { transform: translateY(0); opacity: 1; } to { transform: translateY(-20px); opacity: 0; } }`;
        document.head.appendChild(styleSheet);
        
        // 🔥 EXPORT CSV
        function exportToCSV() {
            let table = document.getElementById('resultsTable');
            let rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let row of rows) {
                let cells = row.querySelectorAll('th, td');
                let rowData = [];
                for (let cell of cells) {
                    let text = cell.innerText.replace(/,/g, ';');
                    rowData.push('"' + text + '"');
                }
                csv.push(rowData.join(','));
            }
            
            let blob = new Blob(["\uFEFF" + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');
            let url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', 'recommandations_orientation_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        
        // 🔥 EXPORT EXCEL
        function exportToExcel() {
            let table = document.getElementById('resultsTable');
            let html = table.outerHTML;
            let blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            let link = document.createElement('a');
            let url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', 'recommandations_orientation_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.xls');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>

</body>
</html>