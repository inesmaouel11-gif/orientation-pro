<?php
require "db.php";

$id = $_GET["id"];

$sql = "DELETE FROM etablissements WHERE id_etab = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute(["id"=>$id]);

header("Location: liste_etablissements.php");
exit();
?>