<?php
require_once "db.php";

/* 🔐 infos admin */
$nom = "Admin";
$prenom = "System";
$email = "admin2@example.com";
$password = "admin123";

/* 🔒 hash du mot de passe */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* 🔥 insertion */
$sql = "INSERT INTO utilisateurs 
(nom, prenom, email, password_hash, role)
VALUES (?, ?, ?, ?, 'ADMIN')";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nom, $prenom, $email, $password_hash]);

echo "✅ Nouvel admin créé !";
?>