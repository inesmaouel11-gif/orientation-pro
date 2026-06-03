<?php
require_once "guard.php";
require_once "db.php";

$id_user = $_SESSION["user_id"];

$sql = "SELECT * FROM historique_notes WHERE id_user = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$historique = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Historique</title>

<style>
body {
    font-family: Arial;
    background:#f4f6f9;
}

.container {
    width:80%;
    margin:30px auto;
}

table {
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

th {
    background:#2c3e50;
    color:white;
    padding:10px;
}

td {
    padding:10px;
    border-bottom:1px solid #ddd;
}

tr:hover {
    background:#f1f1f1;
}

.back {
    display:inline-block;
    margin-top:20px;
}
</style>

</head>

<body>

<div class="container">

<h2>📊 Mon historique</h2>

<table>
<tr>
    <th>Date</th>
    <th>Filière BAC</th>
    <th>Moyenne</th>
</tr>

<?php foreach($historique as $h){ ?>

<tr>
    <td><?= $h['created_at']; ?></td>
    <td><?= $h['serie']; ?></td>
    <td><?= $h['moyenne']; ?></td>
</tr>

<?php } ?>

</table>

<a class="back" href="dashboard_user.php">⬅️ Retour</a>

</div>

</body>
</html>