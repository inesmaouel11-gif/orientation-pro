<?php
// export_recommandations.php
require_once "guard.php";
require_once "db.php";

if (!isset($_GET['moyenne']) || !isset($_GET['serie_bac'])) {
    die("Paramètres manquants");
}

$moyenne = floatval($_GET['moyenne']);
$serie = $_GET['serie_bac'];
$wilaya_user = $_SESSION['user_wilaya'] ?? null;
$format = $_GET['format'] ?? 'csv';

// Requête SQL
$sql = "
SELECT 
    f.code_filiere,
    f.nom AS filiere,
    e.code_etab,
    e.nom AS etablissement,
    s.min1 as seuil,
    CASE WHEN s.min1 > 0 THEN ROUND((:moyenne / s.min1) * 100, 1) ELSE 0 END as chances
FROM seuils_admission s
JOIN filieres f ON f.id_filiere = s.id_filiere
JOIN etablissements e ON e.id_etab = s.id_etab
WHERE LOWER(s.serie_bac) = LOWER(:serie)
AND s.min1 IS NOT NULL AND s.min1 > 0
AND (s.wilaya = :wilaya OR s.wilaya IS NULL OR s.wilaya = '')
ORDER BY chances DESC
LIMIT 50
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':moyenne' => $moyenne,
    ':serie' => $serie,
    ':wilaya' => $wilaya_user
]);
$resultats = $stmt->fetchAll();

if ($format == 'csv') {
    // Export CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="recommandations_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    // En-têtes
    fputcsv($output, ['Code Filière', 'Filière', 'Code Établissement', 'Établissement', 'Seuil', 'Chances (%)'], ';');
    
    // Données
    foreach ($resultats as $row) {
        fputcsv($output, [
            $row['code_filiere'],
            $row['filiere'],
            $row['code_etab'],
            $row['etablissement'],
            $row['seuil'],
            $row['chances']
        ], ';');
    }
    
    fclose($output);
    exit();
    
} elseif ($format == 'excel') {
    // Export Excel (HTML)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="recommandations_' . date('Y-m-d') . '.xls"');
    
    echo '<table border="1">';
    echo '<tr style="background:#001529; color:white;">
            <th>Code Filière</th>
            <th>Filière</th>
            <th>Code Établissement</th>
            <th>Établissement</th>
            <th>Seuil</th>
            <th>Chances (%)</th>
          </tr>';
    
    foreach ($resultats as $row) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['code_filiere']) . '</td>';
        echo '<td>' . htmlspecialchars($row['filiere']) . '</td>';
        echo '<td>' . htmlspecialchars($row['code_etab']) . '</td>';
        echo '<td>' . htmlspecialchars($row['etablissement']) . '</td>';
        echo '<td>' . $row['seuil'] . '</td>';
        echo '<td>' . $row['chances'] . '%</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    exit();
}
?>