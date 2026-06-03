<?php
require_once "guard.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
</head>

<body>

<h1>Bienvenue <?php echo $_SESSION["user_prenom"]; ?></h1>

<p>Role : <?php echo $_SESSION["user_role"]; ?></p>

<a href="logout.php">Se déconnecter</a>

</body>
</html>