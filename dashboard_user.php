<?php
require_once "guard.php";

if ($_SESSION["user_role"] !== "USER") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilisateur - Orientation Pro</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR (Menu de gauche) --- */
        .sidebar {
            width: 250px;
            background-color: #001529;
            color: white;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-align: center;
            border-bottom: 1px solid #1a2c3f;
            letter-spacing: 1px;
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
            transition: background 0.3s, color 0.3s;
            font-size: 15px;
        }
        .nav-link:hover {
            color: white;
        }
        .nav-link.active {
            background-color: #1890ff;
            color: white;
        }
        .nav-icon {
            margin-right: 15px;
            font-size: 18px;
        }

        /* --- CONTENU PRINCIPAL --- */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* --- TOP HEADER (Barre du haut) --- */
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
            color: #333;
            font-weight: 500;
        }

        /* --- ZONE DE CONTENU --- */
        .content-area {
            padding: 30px;
        }
        .content-title {
            margin-top: 0;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* --- CARTES DE RACCOURCIS --- */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            padding: 25px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card .icon {
            font-size: 35px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            🎓 Orientation Pro
        </div>
        <div class="nav-menu">
            <a href="dashboard_user.php" class="nav-link active">
                <span class="nav-icon">⊞</span> Dashboard
            </a>
            <a href="releve_notes.php" class="nav-link">
                <span class="nav-icon">📄</span> Mon Relevé
            </a>
            <a href="filieres.php" class="nav-link">
                <span class="nav-icon">📚</span> Filières
            </a>
            <a href="releve_notes.php" class="nav-link">
                <span class="nav-icon">📊</span> Recommandations
            </a>
            <a href="mes_favoris.php" class="nav-link">
                <span class="nav-icon">❤️</span> Favoris
            </a>
            <a href="historique.php" class="nav-link">
                <span class="nav-icon">🕒</span> Historique
            </a>
            <a href="profil.php" class="nav-link">
                <span class="nav-icon">👤</span> Mon profil
            </a>
            <!-- 🔥 NOUVEAU LIEN MES MESSAGES -->
            <a href="mes_messages_reponses.php" class="nav-link">
                <span class="nav-icon">📨</span> Mes messages
            </a>
            <br>
            <a href="a_propos.php" class="nav-link">
                <span class="nav-icon">ℹ️</span> À propos
            </a>
            <a href="faq.php" class="nav-link">
                <span class="nav-icon">❓</span> FAQ
            </a>
            <a href="logout.php" class="nav-link" style="color: #ff4d4f; margin-top: auto;">
                <span class="nav-icon">🚪</span> Déconnexion
            </a>
        </div>
    </div>

    <div class="main-wrapper">
        
        <div class="top-header">
            <div class="user-profile">
                👤 <?= htmlspecialchars($_SESSION["user_prenom"] . " " . $_SESSION["user_nom"]); ?> (Étudiant)
            </div>
        </div>

        <div class="content-area">
            <h1 class="content-title">Vue d'ensemble</h1>

            <div class="card-container">
                <a href="releve_notes.php" class="card">
                    <div class="icon">📄</div>
                    <div>Saisir mon relevé de notes</div>
                </a>
                <a href="filieres.php" class="card">
                    <div class="icon">📚</div>
                    <div>Consulter les filières</div>
                </a>
                <a href="releve_notes.php" class="card">
                    <div class="icon">📊</div>
                    <div>Voir les recommandations</div>
                </a>
            </div>
        </div>

    </div>

</body>
</html>