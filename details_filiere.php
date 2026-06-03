<?php
$conn = new mysqli("127.0.0.1", "root", "", "orientation_pro");

if ($conn->connect_error) {
    die("Erreur connexion");
}

if (!isset($_GET['id'])) {
    die("Filière introuvable");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM filieres WHERE id_filiere = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$f = $result->fetch_assoc();

if (!$f) {
    die("Filière non trouvée");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Détails filière</title>

<style>
body {
    font-family: Arial;
    background:#f4f6f9;
}

.container {
    width:70%;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

h2 {
    color:#2c3e50;
}

.info {
    margin:10px 0;
    font-size:16px;
}

.btn {
    display:inline-block;
    margin-top:20px;
    padding:10px 15px;
    background:#3498db;
    color:white;
    text-decoration:none;
    border-radius:6px;
}
</style>

</head>

<body>

<div class="container">

<h2><?= htmlspecialchars($f['nom']); ?></h2>

<div class="info"><b>Domaine :</b> <?= $f['domaine']; ?></div>
<div class="info"><b>Durée :</b> <?= $f['duree']; ?></div>
<div class="info"><b>Diplôme :</b> <?= $f['diplome']; ?></div>

<div class="info">
<b>Description :</b><br>
<?= $f['description'] ?: "Aucune description disponible"; ?>
</div>

<a href="filieres.php" class="btn">⬅️ Retour</a>

</div>

</body>
</html>