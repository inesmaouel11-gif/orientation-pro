<?php
require "db.php";

$id = $_GET["id"];

$sql = "DELETE FROM seuils_admission WHERE id_seuil=:id";

$stmt = $pdo->prepare($sql);

$stmt->execute(["id"=>$id]);

header("Location: liste_seuils.php");
exit();
?>