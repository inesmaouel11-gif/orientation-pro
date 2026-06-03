<?php
require "guard.php";

$message="";

if(isset($_POST["upload"])){

$target_dir = "releves/";

$filename = basename($_FILES["releve"]["name"]);

$target_file = $target_dir . $filename;

if(move_uploaded_file($_FILES["releve"]["tmp_name"], $target_file)){

$message="Relevé uploadé avec succès";

}else{

$message="Erreur upload";

}

}
?>

<h2>Importer votre relevé de notes</h2>

<?php if($message) echo "<p>$message</p>"; ?>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="releve" required>

<br><br>

<button type="submit" name="upload">Importer</button>

</form>