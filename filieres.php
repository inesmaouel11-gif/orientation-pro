<?php
require_once "db.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Filières - Orientation Pro</title>

<style>

body {
    margin:0;
    font-family: "Segoe UI", Arial;
    background:#f4f6f9;
}

/* HEADER */
.header {
    background:#2c3e50;
    color:white;
    padding:20px;
    font-size:20px;
}

/* CONTAINER */
.container {
    padding:30px;
}

/* SEARCH */
.search-box {
    margin-bottom:20px;
}

.search-box input {
    width:300px;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
}

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

th {
    background:#34495e;
    color:white;
    padding:12px;
}

td {
    padding:12px;
    border-bottom:1px solid #eee;
}

tr:hover {
    background:#f1f1f1;
}

/* BUTTONS */
.btn {
    padding:6px 10px;
    color:white;
    border:none;
    border-radius:5px;
    text-decoration:none;
    font-size:13px;
    display: inline-block;
    margin: 2px;
    transition: 0.3s;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-details {
    background:#3498db;
}

.btn-details:hover {
    background:#2980b9;
}

.btn-programme {
    background:#27ae60;
}

.btn-programme:hover {
    background:#2ecc71;
}

.btn-debouches {
    background:#e67e22;
}

.btn-debouches:hover {
    background:#f39c12;
}

/* ACTIONS CONTAINER */
.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* BACK */
.back {
    display:inline-block;
    margin-top:20px;
    text-decoration:none;
    color:#2c3e50;
    font-weight:bold;
}

</style>

<script>
function rechercher() {
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

</head>

<body>

<div class="header">
    📚 Liste des filières universitaires
</div>

<div class="container">

<div class="search-box">
    <input type="text" id="search" onkeyup="rechercher()" placeholder="🔍 Rechercher une filière...">
</div>

<table>
<thead>
<tr>
    <th>Filière</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php

$sql = "SELECT id_filiere, nom FROM filieres ORDER BY nom";
$result = $pdo->query($sql);

while ($row = $result->fetch()) {
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($row['nom']) . "</strong></td>";
    
    echo "<td class='actions'>
            <a class='btn btn-details' href='details_filiere.php?id=" . $row['id_filiere'] . "'>
                📖 Détails
            </a>
            <a class='btn btn-programme' href='programme_filiere.php?id=" . $row['id_filiere'] . "'>
                📚 Programme
            </a>
            <a class='btn btn-debouches' href='debouches_filiere.php?id=" . $row['id_filiere'] . "'>
                💼 Débouchés
            </a>
           </td>";
    
    echo "</tr>";
}

?>

</tbody>
</table>

<a class="back" href="dashboard_user.php">⬅️ Retour au dashboard</a>

</div>

</body>
</html>