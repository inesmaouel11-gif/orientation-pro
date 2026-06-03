<?php
require "db.php";

$message="";

$filieres = $pdo->query("SELECT * FROM filieres")->fetchAll();
$etablissements = $pdo->query("SELECT * FROM etablissements")->fetchAll();

if(isset($_POST["ajouter"])){

$id_filiere = $_POST["id_filiere"];
$id_etab = $_POST["id_etab"];
$annee = $_POST["annee"];
$serie = $_POST["serie_bac"];
$wilaya = $_POST["wilaya"];
$moyenne = $_POST["moyenne"];

$sql = "INSERT INTO seuils_admission
(id_filiere,id_etab,annee,serie_bac,wilaya,moyenne_min)
VALUES(:id_filiere,:id_etab,:annee,:serie,:wilaya,:moyenne)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
"id_filiere"=>$id_filiere,
"id_etab"=>$id_etab,
"annee"=>$annee,
"serie"=>$serie,
"wilaya"=>$wilaya,
"moyenne"=>$moyenne
]);

$message="Seuil ajouté avec succès";
}
?>

<h2>Ajouter un seuil d'admission</h2>

<?php if($message) echo "<p style='color:green'>$message</p>"; ?>

<form method="POST">

Filière<br>
<select name="id_filiere">

<?php foreach($filieres as $f){ ?>

<option value="<?= $f["id_filiere"] ?>">
<?= $f["nom"] ?>
</option>

<?php } ?>

</select>

<br><br>

Établissement<br>
<select name="id_etab">

<?php foreach($etablissements as $e){ ?>

<option value="<?= $e["id_etab"] ?>">
<?= $e["nom"] ?>
</option>

<?php } ?>

</select>

<br><br>

Année<br>
<input type="number" name="annee" value="2024">

<br><br>

Série Bac<br>
<input type="text" name="serie_bac">

<br><br>

Wilaya<br>
<input type="text" name="wilaya">

<br><br>

Seuil (moyenne minimum)<br>
<input type="number" step="0.01" name="moyenne">

<br><br>

<button type="submit" name="ajouter">Ajouter</button>

</form>