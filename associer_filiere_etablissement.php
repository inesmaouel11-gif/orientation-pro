<?php
require "db.php";

$message = "";

/* récupérer filières */
$sql = "SELECT * FROM filieres";
$stmt = $pdo->query($sql);
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* récupérer établissements */
$sql = "SELECT * FROM etablissements";
$stmt = $pdo->query($sql);
$etablissements = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* traitement formulaire */
if(isset($_POST["associer"])){

$id_filiere = $_POST["id_filiere"];
$id_etab = $_POST["id_etab"];
$places = $_POST["places"];

$sql = "INSERT INTO filiere_etablissement(id_filiere,id_etab,places)
VALUES(:id_filiere,:id_etab,:places)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
"id_filiere"=>$id_filiere,
"id_etab"=>$id_etab,
"places"=>$places
]);

$message = "Association enregistrée avec succès";
}
?>

<h2>Associer une filière à un établissement</h2>

<?php if($message){ echo "<p style='color:green'>$message</p>"; } ?>

<form method="POST">

<label>Filière</label><br>

<select name="id_filiere" required>

<?php foreach($filieres as $f){ ?>

<option value="<?= $f["id_filiere"] ?>">
<?= $f["nom"] ?>
</option>

<?php } ?>

</select>

<br><br>

<label>Etablissement</label><br>

<select name="id_etab" required>

<?php foreach($etablissements as $e){ ?>

<option value="<?= $e["id_etab"] ?>">
<?= $e["nom"] ?>
</option>

<?php } ?>

</select>

<br><br>

<label>Nombre de places</label><br>

<input type="number" name="places" min="0">

<br><br>

<button type="submit" name="associer">Associer</button>

</form>