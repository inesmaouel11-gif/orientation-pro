<?php
// favori.php - Version avec redirection GET
require_once "db.php";

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['id_filiere'])) {
    $_SESSION['favori_error'] = "Aucune filière spécifiée.";
    header("Location: " . ($_POST['return_url'] ?? 'recommendation.php'));
    exit();
}

$user_id = $_SESSION["user_id"];
$id_filiere = intval($_POST['id_filiere']);
$return_url = $_POST['return_url'] ?? 'recommendation.php';

try {
    // Vérifier le nombre de favoris (limite 20)
    $check_count = $pdo->prepare("SELECT COUNT(*) FROM favoris WHERE id_user = ?");
    $check_count->execute([$user_id]);
    $favoris_count = $check_count->fetchColumn();
    
    if ($favoris_count >= 20) {
        $_SESSION['favori_error'] = "⚠️ Limite de 20 favoris atteinte !";
    } else {
        // Vérifier si la filière existe déjà
        $check_sql = "SELECT * FROM favoris WHERE id_user = ? AND id_filiere = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$user_id, $id_filiere]);
        
        if ($check_stmt->rowCount() > 0) {
            $_SESSION['favori_error'] = "Cette filière est déjà dans vos favoris !";
        } else {
            $sql = "INSERT INTO favoris (id_user, id_filiere, created_at) VALUES (?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $id_filiere]);
            
            $restant = 20 - ($favoris_count + 1);
            $_SESSION['favori_success'] = "✓ Filière ajoutée ! (Plus que $restant places)";
        }
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $_SESSION['favori_error'] = "Cette filière est déjà dans vos favoris !";
    } else {
        $_SESSION['favori_error'] = "Erreur lors de l'ajout aux favoris.";
    }
}

// Redirection VERS recommendation.php avec paramètres GET
if ($return_url == 'recommendation.php') {
    $moyenne = $_POST['moyenne'] ?? $_SESSION['last_moyenne'] ?? null;
    $serie_bac = $_POST['serie_bac'] ?? $_SESSION['last_serie_bac'] ?? null;
    
    if ($moyenne && $serie_bac) {
        $_SESSION['last_moyenne'] = $moyenne;
        $_SESSION['last_serie_bac'] = $serie_bac;
        header("Location: recommendation.php?moyenne=" . urlencode($moyenne) . "&serie_bac=" . urlencode($serie_bac));
        exit();
    }
}

header("Location: " . $return_url);
exit();
?>