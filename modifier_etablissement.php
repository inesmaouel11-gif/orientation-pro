<?php
require "db.php";

$id = $_GET["id"];

$sql = "SELECT * FROM etablissements WHERE id_etab = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(["id"=>$id]);
$etab = $stmt->fetch();

if(isset($_POST["modifier"])){

$nom = $_POST["nom"];
$wilaya = $_POST["wilaya"];
$adresse = $_POST["adresse"];
$site_web = $_POST["site_web"];

$sql = "UPDATE etablissements
SET nom=:nom,wilaya=:wilaya,adresse=:adresse,site_web=:site_web
WHERE id_etab=:id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
"nom"=>$nom,
"wilaya"=>$wilaya,
"adresse"=>$adresse,
"site_web"=>$site_web,
"id"=>$id
]);

header("Location: liste_etablissements.php");
}
?>

<h2>Modifier établissement</h2>

<form method="POST">

Nom<br>
<input type="text" name="nom" value="<?= $etab["nom"] ?>">

<br><br>

Wilaya<br>
<input type="text" name="wilaya" value="<?= $etab["wilaya"] ?>">

<br><br>

Adresse<br>
<input type="text" name="adresse" value="<?= $etab["adresse"] ?>">

<br><br>

Site web<br>
<input type="text" name="site_web" value="<?= $etab["site_web"] ?>">

<br><br>

<button type="submit" name="modifier">Modifier</button>

</form>