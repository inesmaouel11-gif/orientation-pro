<?php
require_once "guard.php";
require_once "db.php";

if ($_SESSION["user_role"] !== "ADMIN") {
    header("Location: login.php");
    exit();
}

// =============================================
// STATISTIQUES POUR LE DASHBOARD ADMIN
// =============================================

// 1. Nombre total d'utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs");
$total_users = $stmt->fetch()['total'];

// 2. Nombre d'étudiants (USER)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs WHERE role = 'USER'");
$total_etudiants = $stmt->fetch()['total'];

// 3. Nombre d'administrateurs
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs WHERE role = 'ADMIN'");
$total_admins = $stmt->fetch()['total'];

// 4. Nombre total de simulations (notes saisies)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM historique_notes");
$total_simulations = $stmt->fetch()['total'];

// 5. Nombre de filières
$stmt = $pdo->query("SELECT COUNT(*) as total FROM filieres");
$total_filieres = $stmt->fetch()['total'];

// 6. Nombre d'établissements
$stmt = $pdo->query("SELECT COUNT(*) as total FROM etablissements");
$total_etablissements = $stmt->fetch()['total'];

// 7. Nombre de seuils d'admission
$stmt = $pdo->query("SELECT COUNT(*) as total FROM seuils_admission");
$total_seuils = $stmt->fetch()['total'];

// 8. Répartition des utilisateurs par série BAC
$stmt = $pdo->query("SELECT serie, COUNT(*) as total FROM historique_notes GROUP BY serie ORDER BY total DESC LIMIT 10");
$series_stats = $stmt->fetchAll();

// 9. Évolution des inscriptions par mois (6 derniers mois)
$sql = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as mois,
            COUNT(*) as total
        FROM utilisateurs
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY mois ASC";
$stmt = $pdo->query($sql);
$inscriptions_stats = $stmt->fetchAll();

// 10. Top 10 des filières les plus consultées
$sql = "SELECT 
            f.nom as filiere,
            COUNT(*) as total
        FROM favoris fa
        JOIN filieres f ON fa.id_filiere = f.id_filiere
        GROUP BY fa.id_filiere
        ORDER BY total DESC
        LIMIT 10";
$stmt = $pdo->query($sql);
$favoris_stats = $stmt->fetchAll();

// 11. Nombre de messages non lus
$stmt = $pdo->query("SELECT COUNT(*) as total FROM messages_contact WHERE status = 'non_lu'");
$messages_non_lus = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Orientation Pro</title>
    <!-- Chart.js CDN fiable -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR --- */
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

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: #1890ff;
        }

        .nav-icon {
            margin-right: 15px;
            font-size: 18px;
        }

        /* --- MAIN WRAPPER --- */
        .main-wrapper {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- TOP HEADER --- */
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
            color: #333;
            font-weight: 500;
        }

        /* --- CONTENT AREA --- */
        .content-area {
            padding: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #001529;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .page-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        /* --- STATS CARDS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-top: 3px solid #1890ff;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1890ff;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* --- CHARTS GRID --- */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .chart-card h3 {
            color: #001529;
            margin-bottom: 15px;
            font-size: 18px;
            border-left: 3px solid #1890ff;
            padding-left: 10px;
        }

        canvas {
            max-height: 300px;
            width: 100%;
        }

        /* --- TABLEAU DES FAVORIS --- */
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table-card h3 {
            color: #001529;
            margin-bottom: 15px;
            font-size: 18px;
            border-left: 3px solid #1890ff;
            padding-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f8f9fc;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #eef2f6;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eef2f6;
        }

        .btn-action {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 15px;
            transition: 0.3s;
        }

        .btn-action:hover {
            background-color: #40a9ff;
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
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            ⚙️ <span>Admin Panel</span>
        </div>
        <div class="nav-menu">
            <a href="dashboard_admin.php" class="nav-link active">
                <span class="nav-icon">⊞</span> Dashboard
            </a>
            <a href="liste_filieres.php" class="nav-link">
                <span class="nav-icon">📋</span> Filières
            </a>
            <a href="liste_etablissements.php" class="nav-link">
                <span class="nav-icon">🏫</span> Établissements
            </a>
            <a href="liste_filiere_etablissement.php" class="nav-link">
                <span class="nav-icon">🔗</span> Associations
            </a>
            <a href="liste_seuils.php" class="nav-link">
                <span class="nav-icon">📊</span> Seuils
            </a>
            <!-- 🔥 NOUVEAU LIEN MESSAGES -->
            <a href="admin_messages.php" class="nav-link">
                <span class="nav-icon">📨</span> Messages
                <?php if ($messages_non_lus > 0): ?>
                    <span style="background-color: #ff4d4f; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; margin-left: 8px;"><?= $messages_non_lus; ?></span>
                <?php endif; ?>
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
                🛡️ <?php echo htmlspecialchars($_SESSION["user_prenom"] . " " . $_SESSION["user_nom"]); ?> (Admin)
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">Tableau de bord</h1>
            <p class="page-subtitle">Bienvenue dans votre espace d'administration. Voici les statistiques de la plateforme.</p>

            <!-- Statistiques en cartes -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $total_users ?></div>
                    <div class="stat-label">Utilisateurs totaux</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_etudiants ?></div>
                    <div class="stat-label">Étudiants</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_admins ?></div>
                    <div class="stat-label">Administrateurs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_simulations ?></div>
                    <div class="stat-label">Simulations effectuées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_filieres ?></div>
                    <div class="stat-label">Filières</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_etablissements ?></div>
                    <div class="stat-label">Établissements</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_seuils ?></div>
                    <div class="stat-label">Seuils d'admission</div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="charts-grid">
                <!-- Graphique 1 : Répartition par série BAC -->
                <div class="chart-card">
                    <h3>📊 Répartition par série BAC</h3>
                    <canvas id="serieChart"></canvas>
                </div>

                <!-- Graphique 2 : Évolution des inscriptions -->
                <div class="chart-card">
                    <h3>📈 Évolution des inscriptions (6 mois)</h3>
                    <canvas id="inscriptionsChart"></canvas>
                </div>
            </div>

            <!-- Top filières favorites -->
            <div class="table-card">
                <h3>⭐ Top 10 des filières les plus favorisées</h3>
                <?php if(count($favoris_stats) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Filière</th>
                                <th>Nombre de favoris</th>
                            </table>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach($favoris_stats as $row): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['filiere']) ?></td>
                                <td><?= $row['total'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #888; padding: 20px; text-align: center;">Aucune donnée de favoris disponible pour le moment.</p>
                <?php endif; ?>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <a href="ajouter_filiere.php" class="btn-action">➕ Ajouter une filière</a>
                <a href="ajouter_etablissement.php" class="btn-action" style="background-color: #52c41a;">🏢 Ajouter un établissement</a>
                <a href="ajouter_seuil.php" class="btn-action" style="background-color: #722ed1;">📈 Ajouter un seuil</a>
            </div>
        </div>
    </div>

    <script>
        // Graphique 1 : Répartition par série BAC
        const serieCtx = document.getElementById('serieChart').getContext('2d');
        const serieLabels = <?php 
            $labels = [];
            $data = [];
            foreach($series_stats as $s) {
                $labels[] = $s['serie'];
                $data[] = $s['total'];
            }
            echo json_encode($labels);
        ?>;
        const serieData = <?php echo json_encode($data); ?>;

        if(serieLabels.length > 0) {
            new Chart(serieCtx, {
                type: 'bar',
                data: {
                    labels: serieLabels,
                    datasets: [{
                        label: 'Nombre d\'étudiants',
                        data: serieData,
                        backgroundColor: '#1890ff',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        // Graphique 2 : Évolution des inscriptions
        const inscriptionsCtx = document.getElementById('inscriptionsChart').getContext('2d');
        const inscriptionsLabels = <?php 
            $labels = [];
            $data = [];
            foreach($inscriptions_stats as $s) {
                $labels[] = $s['mois'];
                $data[] = $s['total'];
            }
            echo json_encode($labels);
        ?>;
        const inscriptionsData = <?php echo json_encode($data); ?>;

        if(inscriptionsLabels.length > 0) {
            new Chart(inscriptionsCtx, {
                type: 'line',
                data: {
                    labels: inscriptionsLabels,
                    datasets: [{
                        label: 'Nouveaux inscrits',
                        data: inscriptionsData,
                        borderColor: '#1890ff',
                        backgroundColor: 'rgba(24,144,255,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#1890ff',
                        pointBorderColor: '#fff',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    </script>

</body>
</html>