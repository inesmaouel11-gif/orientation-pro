<?php
// mes_messages_reponses.php - Page où l'utilisateur voit ses messages et réponses
require_once "guard.php";
require_once "db.php";

$id_user = $_SESSION["user_id"];

// Récupérer les messages de l'utilisateur connecté
$stmt = $pdo->prepare("
    SELECT * FROM messages_contact 
    WHERE id_user = ? OR email = ? 
    ORDER BY date_envoi DESC
");
$stmt->execute([$id_user, $_SESSION["user_email"]]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes messages - Orientation Pro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .nav-icon { margin-right: 15px; }
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
        .user-profile { display: flex; align-items: center; gap: 10px; }
        .content-area { padding: 30px; }
        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 20px;
        }
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
        .reponse-box {
            margin-top: 15px;
            padding: 15px;
            background: #f6ffed;
            border-radius: 8px;
            border-left: 3px solid #52c41a;
        }
        .reponse-box strong {
            color: #52c41a;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-repondu { background: #f6ffed; color: #52c41a; }
        .status-envoyé { background: #e6f4ff; color: #1890ff; }
        .btn-back {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            margin-top: 20px;
            transition: 0.3s;
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
        <div class="sidebar-brand">🎓 <span>Orientation Pro</span></div>
        <div class="nav-menu">
            <a href="dashboard_user.php" class="nav-link"><span class="nav-icon">⊞</span> Dashboard</a>
            <a href="releve_notes.php" class="nav-link"><span class="nav-icon">📄</span> Mon Relevé</a>
            <a href="filieres.php" class="nav-link"><span class="nav-icon">📚</span> Filières</a>
            <a href="releve_notes.php" class="nav-link"><span class="nav-icon">📊</span> Recommandations</a>
            <a href="mes_favoris.php" class="nav-link"><span class="nav-icon">❤️</span> Favoris</a>
            <a href="historique.php" class="nav-link"><span class="nav-icon">🕒</span> Historique</a>
            <a href="profil.php" class="nav-link"><span class="nav-icon">👤</span> Mon profil</a>
            <a href="mes_messages_reponses.php" class="nav-link active"><span class="nav-icon">📨</span> Mes messages</a>
            <br>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;"><span class="nav-icon">🚪</span> Déconnexion</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-header">
            <div class="user-profile">
                👤 <?= htmlspecialchars($_SESSION["user_prenom"] . " " . $_SESSION["user_nom"]); ?>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">📨 Mes messages</h1>

            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <div class="message-info">
                                <div><span class="message-label">Sujet :</span> <span class="message-value"><?= htmlspecialchars($msg['sujet']); ?></span></div>
                                <div><span class="message-label">Date :</span> <span class="message-value"><?= date('d/m/Y H:i', strtotime($msg['date_envoi'])); ?></span></div>
                                <div>
                                    <span class="status-badge <?= $msg['status'] == 'repondu' ? 'status-repondu' : 'status-envoyé'; ?>">
                                        <?= $msg['status'] == 'repondu' ? '✅ Répondu' : '📤 Envoyé'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="message-content">
                            <strong>📝 Votre message :</strong><br>
                            <?= nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>

                        <?php if ($msg['reponse']): ?>
                            <div class="reponse-box">
                                <strong>📎 Réponse de l'équipe :</strong><br>
                                <?= nl2br(htmlspecialchars($msg['reponse'])); ?>
                                <div style="font-size: 12px; color: #888; margin-top: 8px;">
                                    Répondu le <?= date('d/m/Y H:i', strtotime($msg['date_reponse'])); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 10px; font-size: 13px; color: #888; background: #f8f9fc; padding: 10px; border-radius: 8px;">
                                ⏳ En attente de réponse...
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px; background: white; border-radius: 16px; color: #888;">
                    <div style="font-size: 48px; margin-bottom: 15px;">📭</div>
                    <p>Vous n'avez envoyé aucun message.</p>
                    <p>Vous pouvez nous contacter depuis la page <a href="contact.php" style="color: #1890ff;">Contact</a>.</p>
                </div>
            <?php endif; ?>

            <div style="text-align: center;">
                <a href="dashboard_user.php" class="btn-back">← Retour au tableau de bord</a>
            </div>
        </div>
    </div>

</body>
</html>