<?php
require_once "guard.php";
require_once "db.php";
require_once "dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$id_user = $_SESSION["user_id"];
$moyenne = $_POST["moyenne"] ?? null;
$serie = $_POST["serie_bac"] ?? null;

if (!$moyenne || !$serie) {
    die("Données manquantes pour l'export.");
}

// Récupérer les recommandations
$sql = "SELECT 
            f.id_filiere,
            f.code_filiere,
            f.nom AS filiere,
            e.id_etab,
            e.code_etab,
            e.nom AS etablissement,
            s.min1,
            s.min2,
            s.min3
        FROM seuils_admission s
        JOIN filieres f ON f.id_filiere = s.id_filiere
        JOIN etablissements e ON e.id_etab = s.id_etab
        WHERE LOWER(s.serie_bac) = LOWER(:serie)
        AND s.min1 IS NOT NULL
        AND s.min1 > 0
        AND s.min1 <= :moyenne
        ORDER BY s.min1 DESC
        LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute([':serie' => $serie, ':moyenne' => $moyenne]);
$resultats = $stmt->fetchAll();

// Récupérer les infos utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id_user = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch();

// Générer le HTML du PDF (version corrigée)
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recommandations - Orientation Pro</title>
    <style>
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1890ff;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #001529;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 12px;
        }
        .user-info {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #eef2f6;
        }
        .user-info h3 {
            margin: 0 0 10px;
            color: #001529;
        }
        .stats-badge {
            background: #e6f4ff;
            color: #1890ff;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #001529;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eef2f6;
            font-size: 11px;
        }
        .status-accepted {
            color: #52c41a;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #eef2f6;
            padding-top: 15px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>🎓 Orientation Pro</h1>
    <p>Plateforme d\'orientation post-bac Algérie</p>
</div>

<div class="user-info">
    <h3>📋 Rapport d\'orientation</h3>
    <p><strong>Étudiant :</strong> ' . htmlspecialchars($user["prenom"] . " " . $user["nom"]) . '</p>
    <p><strong>Email :</strong> ' . htmlspecialchars($user["email"]) . '</p>
    <p><strong>Série BAC :</strong> ' . htmlspecialchars($serie) . '</p>
    <p><span class="stats-badge">📊 Moyenne : ' . number_format($moyenne, 2) . ' / 20</span></p>
</div>

<h3>🎯 Recommandations personnalisées</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Code</th>
            <th>Filière</th>
            <th>Code Étab.</th>
            <th>Établissement</th>
            <th>Seuil</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>';

$compteur = 0;
if (count($resultats) > 0) {
    foreach ($resultats as $row) {
        $compteur++;
        $html .= '
        <tr>
            <td>' . $compteur . '</td>
            <td>' . htmlspecialchars($row['code_filiere'] ?? '---') . '</td>
            <td><strong>' . htmlspecialchars($row['filiere']) . '</strong></td>
            <td>' . htmlspecialchars($row['code_etab'] ?? '---') . '</td>
            <td>' . htmlspecialchars($row['etablissement']) . '</td>
            <td>' . number_format($row['min1'], 2) . '</td>
            <td class="status-accepted">✅ Accepté</td>
        </tr>';
    }
} else {
    $html .= '
        <tr>
            <td colspan="7" style="text-align:center; padding:40px;">😕 Aucune recommandation trouvée pour votre moyenne.</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<div class="footer">
    <p>Document généré le ' . date("d/m/Y à H:i") . '</p>
    <p>Orientation Pro - © 2025 - Plateforme d\'orientation académique et professionnelle (Algérie)</p>
    <p>🇩🇿 Universités LMD, Grandes écoles, Instituts paramédicaux</p>
</div>

</body>
</html>';

// Configuration de Dompdf
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);
$options->set('chroot', realpath(__DIR__));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Télécharger le PDF
$dompdf->stream("recommandations_" . date("Y-m-d") . ".pdf", array("Attachment" => true));
?>