<?php
require_once "guard.php";
require_once "db.php";

$id_user = $_SESSION["user_id"];

// Récupérer les filières BAC
$stmt = $pdo->query("SELECT * FROM filieres_bac");
$filieres = $stmt->fetchAll();

// Filière sélectionnée
$id_filiere = $_POST["id_filiere_bac"] ?? null;

// Récupérer le nom de la filière
$serie_bac_nom = "";
if ($id_filiere) {
    foreach ($filieres as $f) {
        if ($f["id_filiere_bac"] == $id_filiere) {
            $serie_bac_nom = $f["nom_filiere"];
            break;
        }
    }
}

// Récupérer les matières (sans spécialité)
$matieres = [];

if ($id_filiere) {
    $sql = "SELECT DISTINCT m.id_matiere, m.nom_matiere, c.coefficient
            FROM coefficients_bac c
            JOIN matieres m ON m.id_matiere = c.id_matiere
            WHERE c.id_filiere_bac = ?
            AND c.id_specialite IS NULL
            ORDER BY m.nom_matiere";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_filiere]);
    $matieres = $stmt->fetchAll();
}

// Récupérer les notes existantes
$notes_existantes = [];
if ($id_filiere) {
    $sql_notes = "SELECT id_matiere, note FROM notes_bac 
                  WHERE id_user = ? AND id_filiere_bac = ?
                  AND id_specialite IS NULL";
    
    $stmt_notes = $pdo->prepare($sql_notes);
    $stmt_notes->execute([$id_user, $id_filiere]);
    $notes_existantes = $stmt_notes->fetchAll(PDO::FETCH_KEY_PAIR);
}

// Calcul de la moyenne
$moyenne = null;
$message_success = "";

if (isset($_POST["enregistrer"])) {
    // Supprimer les anciennes notes
    $sql_delete = "DELETE FROM notes_bac WHERE id_user = ? AND id_filiere_bac = ? AND id_specialite IS NULL";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([$id_user, $id_filiere]);
    
    $somme = 0;
    $total_coef = 0;
    
    foreach ($matieres as $matiere) {
        $id_matiere = $matiere["id_matiere"];
        $coef = $matiere["coefficient"];
        
        // Remplacer la virgule par un point pour la note
        $note = str_replace(',', '.', $_POST["note_" . $id_matiere] ?? '');
        
        if ($note !== null && $note !== "" && is_numeric($note) && $note >= 0 && $note <= 20) {
            $note = floatval($note);
            
            $sql = "INSERT INTO notes_bac
                    (id_user,id_filiere_bac,id_specialite,id_matiere,note)
                    VALUES (?,?,?,?,?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_user,
                $id_filiere,
                null,
                $id_matiere,
                $note
            ]);
            
            $somme += ($note * $coef);
            $total_coef += $coef;
        }
    }
    
    if ($total_coef > 0) {
        $moyenne = round($somme / $total_coef, 2);
        $message_success = "✅ Notes enregistrées avec succès !";
        
        // Sauvegarder dans historique_notes
        $sql_hist = "INSERT INTO historique_notes (id_user, serie, moyenne, created_at) VALUES (?, ?, ?, NOW())";
        $stmt_hist = $pdo->prepare($sql_hist);
        $stmt_hist->execute([$id_user, $serie_bac_nom, $moyenne]);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon relevé de notes - Orientation Pro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #001529;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #1a2c3f;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            padding-top: 10px;
            flex: 1;
        }

        .nav-link {
            padding: 15px 25px;
            color: #a6adb4;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: #1890ff;
        }

        .nav-icon {
            margin-right: 15px;
        }

        .main-wrapper {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            background-color: white;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 30px;
            box-shadow: 0 1px 4px rgba(0,21,41,0.08);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-area {
            padding: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 25px;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 30px;
            max-width: 900px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
        }

        select:focus {
            outline: none;
            border-color: #1890ff;
            box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
        }

        .success-message {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #135200;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .matieres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .matiere-item {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }

        .matiere-item:hover {
            border-color: #1890ff;
            box-shadow: 0 2px 8px rgba(24,144,255,0.1);
        }

        .matiere-label {
            font-weight: 600;
            color: #001529;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .matiere-coef {
            background: #e6f4ff;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 12px;
            color: #1890ff;
        }

        .matiere-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #40a9ff;
            transform: translateY(-2px);
        }

        .btn-recommend {
            background-color: #52c41a;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
            width: 100%;
        }

        .btn-recommend:hover {
            background-color: #73d13d;
            transform: translateY(-2px);
        }

        .moyenne-box {
            background: linear-gradient(135deg, #001529 0%, #002140 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-top: 20px;
        }

        .moyenne-value {
            font-size: 42px;
            font-weight: bold;
            color: #1890ff;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #eef2f6;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-brand {
                font-size: 0;
                padding: 20px 0;
            }
            .sidebar-brand span {
                font-size: 24px;
            }
            .nav-link span:not(.nav-icon) {
                display: none;
            }
            .main-wrapper {
                margin-left: 70px;
            }
            .matieres-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🎓 <span>Orientation Pro</span></div>
        <div class="nav-menu">
            <a href="dashboard_user.php" class="nav-link"><span class="nav-icon">⊞</span> Dashboard</a>
            <a href="releve_notes.php" class="nav-link active"><span class="nav-icon">📄</span> Mon Relevé</a>
            <a href="filieres.php" class="nav-link"><span class="nav-icon">📚</span> Filières</a>
            <a href="releve_notes.php" class="nav-link"><span class="nav-icon">📊</span> Recommandations</a>
            <a href="mes_favoris.php" class="nav-link"><span class="nav-icon">❤️</span> Favoris</a>
            <a href="historique.php" class="nav-link"><span class="nav-icon">🕒</span> Historique</a>
            <a href="profil.php" class="nav-link"><span class="nav-icon">👤</span> Mon profil</a>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;"><span class="nav-icon">🚪</span> Déconnexion</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-header">
            <div class="user-profile">
                👤 <?= htmlspecialchars($_SESSION["user_prenom"] . " " . $_SESSION["user_nom"]); ?> (Étudiant)
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">📝 Saisir mon relevé de notes</h1>

            <?php if($message_success): ?>
                <div class="success-message"><?= $message_success ?></div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST" id="notesForm">
                    <div class="form-group">
                        <label>🎓 Filière BAC</label>
                        <select name="id_filiere_bac" onchange="this.form.submit()" required>
                            <option value="">-- Choisissez votre filière --</option>
                            <?php foreach($filieres as $f): ?>
                                <option value="<?= $f["id_filiere_bac"]; ?>" <?= ($id_filiere == $f["id_filiere_bac"]) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($f["nom_filiere"]); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if(!empty($matieres)): ?>
                        <hr>
                        <h3 style="margin-bottom: 20px; color: #001529;">📖 Saisissez vos notes</h3>
                        
                        <div class="matieres-grid">
                            <?php foreach($matieres as $m): 
                                $note_existante = $notes_existantes[$m["id_matiere"]] ?? '';
                                $note_existante_formatee = str_replace('.', ',', $note_existante);
                            ?>
                                <div class="matiere-item">
                                    <div class="matiere-label">
                                        <?= htmlspecialchars($m["nom_matiere"]); ?>
                                        <span class="matiere-coef">Coefficient <?= $m["coefficient"]; ?></span>
                                    </div>
                                    <input type="number" 
                                           step="0.25" 
                                           min="0" 
                                           max="20" 
                                           class="matiere-input"
                                           name="note_<?= $m["id_matiere"]; ?>" 
                                           placeholder="Note /20"
                                           value="<?= htmlspecialchars($note_existante_formatee); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" name="enregistrer" class="btn-primary">
                            💾 Enregistrer mes notes
                        </button>
                    <?php elseif($id_filiere): ?>
                        <div class="info-message" style="background:#e6f7ff; border:1px solid #91d5ff; color:#0050b3; padding:12px 16px; border-radius:8px;">
                            ⚠️ Aucune matière trouvée pour cette filière. Veuillez contacter l'administrateur.
                        </div>
                    <?php endif; ?>
                </form>

                <?php if($moyenne !== null): ?>
                    <div class="moyenne-box">
                        <h3>🎯 Votre moyenne générale</h3>
                        <div class="moyenne-value"><?= number_format($moyenne, 2) ?> / 20</div>
                        
                        <form method="POST" action="recommendation.php" style="margin-top: 20px;">
                            <input type="hidden" name="moyenne" value="<?= $moyenne ?>">
                            <input type="hidden" name="serie_bac" value="<?= htmlspecialchars($serie_bac_nom) ?>">
                            <button type="submit" class="btn-recommend">
                                🎓 Voir mes recommandations
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let formSubmitted = false;
        const form = document.getElementById('notesForm');
        if(form) {
            form.addEventListener('submit', function() {
                if(formSubmitted) {
                    event.preventDefault();
                } else {
                    formSubmitted = true;
                }
            });
        }
    </script>
</body>
</html>