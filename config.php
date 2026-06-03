<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$db = "orientation_pro";
$port = 3307; // 🔥 IMPORTANT

$conn = new mysqli($host, $user, $password, $db, $port);

if ($conn->connect_error) {
    die("❌ Erreur connexion : " . $conn->connect_error);
}
?>