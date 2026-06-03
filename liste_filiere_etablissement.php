<?php
require "db.php";

$sql = "SELECT 
        filieres.nom AS filiere,
        etablissements.nom AS etablissement,
        filiere_etablissement.places
        FROM filiere_etablissement
        JOIN filieres ON filiere_etablissement.id_filiere = filieres.id_filiere
        JOIN etablissements ON filiere_etablissement.id_etab = etablissements.id_etab";

$stmt = $pdo->query($sql);
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Liste Filières / Établissements</h2>

<table border="1">

<tr>
<th>Filière</th>
<th>Établissement</th>
<th>Places</th>
</tr>

<?php foreach($associations as $a): ?>

<tr>

<td><?= $a["filiere"] ?></td>
<td><?= $a["etablissement"] ?></td>
<td><?= $a["places"] ?></td>

</tr>

<?php endforeach; ?>

</table>