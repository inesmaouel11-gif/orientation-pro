<?php
// favori.php - Version adaptée à votre structure de table
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
        $_SESSION['favori_error'] = "⚠️ Vous avez atteint la limite de 20 favoris !";
    } else {
        // Vérifier si la filière existe déjà dans les favoris
        $check_sql = "SELECT * FROM favoris WHERE id_user = ? AND id_filiere = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$user_id, $id_filiere]);
        
        if ($check_stmt->rowCount() > 0) {
            $_SESSION['favori_error'] = "Cette filière est déjà dans vos favoris !";
        } else {
            // Insertion dans la table favoris (sans colonne id)
            $sql = "INSERT INTO favoris (id_user, id_filiere, created_at) VALUES (?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $id_filiere]);
            
            $restant = 20 - ($favoris_count + 1);
            $_SESSION['favori_success'] = "✓ Filière ajoutée à vos favoris ! (Plus que $restant places sur 20)";
        }
    }
} catch (PDOException $e) {
    // Vérifier si c'est une erreur de doublon
    if ($e->getCode() == 23000) {
        $_SESSION['favori_error'] = "Cette filière est déjà dans vos favoris !";
    } else {
        $_SESSION['favori_error'] = "Erreur lors de l'ajout aux favoris : " . $e->getMessage();
    }
}

// Redirection avec conservation des données pour recommendation.php
if ($return_url == 'recommendation.php' && isset($_POST['moyenne']) && isset($_POST['serie_bac'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Redirection...</title>
    </head>
    <body>
        <form method="POST" action="recommendation.php">
            <input type="hidden" name="moyenne" value="<?= htmlspecialchars($_POST['moyenne']) ?>">
            <input type="hidden" name="serie_bac" value="<?= htmlspecialchars($_POST['serie_bac']) ?>">
        </form>
        <script>document.forms[0].submit();</script>
    </body>
    </html>
    <?php
    exit();
} else {
    header("Location: " . $return_url);
    exit();
}
?>