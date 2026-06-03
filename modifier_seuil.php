<?php
require "db.php";

$id = $_GET["id"];

$sql = "SELECT * FROM seuils_admission WHERE id_seuil=:id";
$stmt = $pdo->prepare($sql);
$stmt->execute(["id"=>$id]);
$seuil = $stmt->fetch();

if(isset($_POST["modifier"])){

$annee = $_POST["annee"];
$serie = $_POST["serie_bac"];
$wilaya = $_POST["wilaya"];
$moyenne = $_POST["moyenne"];

$sql = "UPDATE seuils_admission
SET annee=:annee,
serie_bac=:serie,
wilaya=:wilaya,
moyenne_min=:moyenne
WHERE id_seuil=:id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
"annee"=>$annee,
"serie"=>$serie,
"wilaya"=>$wilaya,
"moyenne"=>$moyenne,
"id"=>$id
]);

header("Location: liste_seuils.php");
}
?>

<h2>Modifier seuil</h2>

<form method="POST">

Année<br>
<input type="number" name="annee" value="<?= $seuil["annee"] ?>">

<br><br>

Série Bac<br>
<input type="text" name="serie_bac" value="<?= $seuil["serie_bac"] ?>">

<br><br>

Wilaya<br>
<input type="text" name="wilaya" value="<?= $seuil["wilaya"] ?>">

<br><br>

Seuil<br>
<input type="number" step="0.01" name="moyenne" value="<?= $seuil["moyenne_min"] ?>">

<br><br>

<button type="submit" name="modifier">Modifier</button>

</form>