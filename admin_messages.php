<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "ADMIN") {
    header("Location: login.php");
    exit();
}

// Traitement de la réponse
if (isset($_POST['repondre']) && isset($_POST['id_message']) && isset($_POST['reponse'])) {
    $id_message = intval($_POST['id_message']);
    $reponse = trim($_POST['reponse']);
    
    $stmt = $pdo->prepare("UPDATE messages_contact SET reponse = ?, status = 'repondu', date_reponse = NOW() WHERE id_message = ?");
    $stmt->execute([$reponse, $id_message]);
    $success = "✅ Réponse envoyée avec succès !";
}

// Récupérer tous les messages
$stmt = $pdo->prepare("
    SELECT m.*, 
           CASE WHEN m.id_user IS NOT NULL THEN u.prenom ELSE NULL END as user_prenom,
           CASE WHEN m.id_user IS NOT NULL THEN u.nom ELSE NULL END as user_nom
    FROM messages_contact m
    LEFT JOIN utilisateurs u ON u.id_user = m.id_user
    ORDER BY m.date_envoi DESC
");
$stmt->execute();
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des messages - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f0f2f5;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #001529;
            color: white;
            height: 100vh;
            position: fixed;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #1a2c3f;
        }
        .nav-menu {
            padding-top: 10px;
        }
        .nav-link {
            padding: 15px 25px;
            color: #a6adb4;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: #1890ff;
        }
        .nav-icon { margin-right: 15px; }
        .main-wrapper {
            flex: 1;
            margin-left: 250px;
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
        .content-area { padding: 30px; }
        .page-title { font-size: 28px; color: #001529; margin-bottom: 20px; }
        .message-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid #1890ff;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eef2f6;
        }
        .message-info { display: flex; gap: 20px; flex-wrap: wrap; }
        .message-label { font-weight: 600; color: #001529; }
        .message-value { color: #666; }
        .message-content {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-non_lu { background: #fff2f0; color: #ff4d4f; }
        .status-lu { background: #e6f4ff; color: #1890ff; }
        .status-repondu { background: #f6ffed; color: #52c41a; }
        .btn-repondre {
            background-color: #1890ff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }
        .reponse-box {
            margin-top: 15px;
            padding: 15px;
            background: #f6ffed;
            border-radius: 8px;
            display: none;
        }
        .reponse-box textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .btn-envoyer {
            background-color: #52c41a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        .anonyme-badge {
            background: #f0f2f5;
            color: #888;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-brand { font-size: 0; padding: 20px 0; }
            .sidebar-brand span { font-size: 24px; }
            .nav-link span:not(.nav-icon) { display: none; }
            .main-wrapper { margin-left: 70px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">⚙️ <span>Admin Panel</span></div>
        <div class="nav-menu">
            <a href="dashboard_admin.php" class="nav-link"><span class="nav-icon">⊞</span> Dashboard</a>
            <a href="admin_messages.php" class="nav-link active"><span class="nav-icon">📨</span> Messages</a>
            <a href="liste_filieres.php" class="nav-link"><span class="nav-icon">📋</span> Filières</a>
            <a href="liste_etablissements.php" class="nav-link"><span class="nav-icon">🏫</span> Établissements</a>
            <a href="liste_seuils.php" class="nav-link"><span class="nav-icon">📊</span> Seuils</a>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;"><span class="nav-icon">🚪</span> Déconnexion</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-header">
            <div class="user-profile">🛡️ <?= htmlspecialchars($_SESSION["user_prenom"]); ?> (Admin)</div>
        </div>
        <div class="content-area">
            <h1 class="page-title">📨 Gestion des messages</h1>
            
            <?php if (isset($success)): ?>
                <div style="background:#f6ffed; border:1px solid #b7eb8f; color:#135200; padding:12px; border-radius:8px; margin-bottom:20px;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php foreach ($messages as $msg): 
                $is_inscrit = !is_null($msg['id_user']);
                $status_class = '';
                switch($msg['status']) {
                    case 'non_lu': $status_class = 'status-non_lu'; break;
                    case 'lu': $status_class = 'status-lu'; break;
                    case 'repondu': $status_class = 'status-repondu'; break;
                }
            ?>
                <div class="message-card" id="message-<?= $msg['id_message'] ?>">
                    <div class="message-header">
                        <div class="message-info">
                            <div><span class="message-label">De :</span> 
                                <?php if ($is_inscrit): ?>
                                    <span class="message-value"><?= htmlspecialchars($msg['user_prenom'] . ' ' . $msg['user_nom']); ?> (📱 Utilisateur inscrit)</span>
                                <?php else: ?>
                                    <span class="message-value"><?= htmlspecialchars($msg['nom']); ?></span>
                                    <span class="anonyme-badge">👤 Anonyme (non inscrit)</span>
                                <?php endif; ?>
                            </div>
                            <div><span class="message-label">Email :</span> <span class="message-value"><?= htmlspecialchars($msg['email']); ?></span></div>
                            <div><span class="message-label">Sujet :</span> <span class="message-value"><?= htmlspecialchars($msg['sujet']); ?></span></div>
                            <div><span class="message-label">Date :</span> <span class="message-value"><?= date('d/m/Y H:i', strtotime($msg['date_envoi'])); ?></span></div>
                        </div>
                        <div><span class="status-badge <?= $status_class; ?>"><?= strtoupper($msg['status']); ?></span></div>
                    </div>
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>
                    
                    <?php if ($msg['reponse']): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #e6f4ff; border-radius: 8px;">
                            <strong>📎 Votre réponse :</strong><br>
                            <?= nl2br(htmlspecialchars($msg['reponse'])); ?>
                            <div style="font-size: 12px; color: #888; margin-top: 5px;">
                                Répondu le <?= date('d/m/Y H:i', strtotime($msg['date_reponse'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($msg['status'] != 'repondu'): ?>
                        <button class="btn-repondre" onclick="toggleReponse(<?= $msg['id_message'] ?>)">✏️ Répondre</button>
                        <div id="reponse-form-<?= $msg['id_message'] ?>" class="reponse-box">
                            <form method="POST">
                                <textarea name="reponse" rows="3" placeholder="Écrire votre réponse..." required></textarea>
                                <input type="hidden" name="id_message" value="<?= $msg['id_message'] ?>">
                                <button type="submit" name="repondre" class="btn-envoyer">📤 Envoyer la réponse</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($messages) == 0): ?>
                <div style="text-align: center; padding: 60px; background: white; border-radius: 16px; color: #888;">
                    📭 Aucun message pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleReponse(id) {
            const form = document.getElementById('reponse-form-' + id);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>
</html>