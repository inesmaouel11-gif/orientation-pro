<?php
require "db.php";

if(isset($_POST["ajouter"])){

$nom = $_POST["nom"];
$wilaya = $_POST["wilaya"];
$adresse = $_POST["adresse"];
$site_web = $_POST["site_web"];

$sql = "INSERT INTO etablissements(nom,wilaya,adresse,site_web)
VALUES(:nom,:wilaya,:adresse,:site_web)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
"nom"=>$nom,
"wilaya"=>$wilaya,
"adresse"=>$adresse,
"site_web"=>$site_web
]);

header("Location: liste_etablissements.php");
exit();
}
?>

<h2>Ajouter établissement</h2>

<form method="POST">

Nom établissement<br>
<input type="text" name="nom" required>

<br><br>

Wilaya<br>
<input type="text" name="wilaya">

<br><br>

Adresse<br>
<input type="text" name="adresse">

<br><br>

Site web<br>
<input type="text" name="site_web">

<br><br>

<button type="submit" name="ajouter">Ajouter</button>

</form>