<?php
require_once "db.php";
// Page publique, pas de guard
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGU - Orientation Pro</title>
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
            flex-direction: column;
        }

        /* Header */
        .main-header {
            background-color: #001529;
            color: white;
            padding: 0 40px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            font-size: 28px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: #a6adb4;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: white;
        }

        .btn-connexion-nav {
            background-color: #1890ff;
            color: white !important;
            padding: 8px 20px;
            border-radius: 4px;
        }

        /* Contenu */
        .content-area {
            flex: 1;
            padding: 60px 10%;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-title {
            font-size: 36px;
            color: #001529;
            margin-bottom: 15px;
            text-align: center;
        }

        .last-update {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 40px;
        }

        .cgu-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .cgu-section h2 {
            color: #001529;
            margin-bottom: 15px;
            font-size: 22px;
            border-left: 4px solid #1890ff;
            padding-left: 15px;
        }

        .cgu-section p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .cgu-section ul {
            margin-left: 30px;
            margin-bottom: 15px;
            color: #555;
            line-height: 1.6;
        }

        .cgu-section li {
            margin-bottom: 8px;
        }

        /* Footer */
        .main-footer {
            background-color: #001529;
            color: #a6adb4;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            margin-top: auto;
        }

        .main-footer a {
            color: #a6adb4;
            text-decoration: none;
            margin: 0 10px;
        }

        .main-footer a:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .main-header {
                padding: 0 20px;
            }
            .nav-links {
                gap: 15px;
            }
            .content-area {
                padding: 30px 20px;
            }
            .page-title {
                font-size: 28px;
            }
            .cgu-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="logo-area">
            <span class="logo-icon">🎓</span>
            <span class="logo-text">Orientation Pro</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="a_propos.php">À propos</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Contact</a>
            <a href="login.php" class="btn-connexion-nav">🔐 Connexion</a>
        </div>
    </header>

    <div class="content-area">
        <h1 class="page-title">📜 Conditions Générales d'Utilisation</h1>
        <div class="last-update">Dernière mise à jour : 22 avril 2025</div>

        <div class="cgu-section">
            <h2>1. Objet</h2>
            <p>
                Les présentes Conditions Générales d'Utilisation (CGU) régissent l'accès et l'utilisation 
                de la plateforme <strong>Orientation Pro</strong>, accessible à l'adresse www.orientation-pro.dz.
            </p>
            <p>
                Orientation Pro est une plateforme d'aide à l'orientation post-bac destinée aux bacheliers 
                algériens, leur permettant de saisir leurs notes, d'obtenir des recommandations personnalisées 
                sur les filières universitaires et de consulter les établissements d'enseignement supérieur en Algérie.
            </p>
        </div>

        <div class="cgu-section">
            <h2>2. Acceptation des CGU</h2>
            <p>
                L'utilisation de la plateforme implique l'acceptation pleine et entière des présentes CGU. 
                Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.
            </p>
            <p>
                Orientation Pro se réserve le droit de modifier ces CGU à tout moment. Les utilisateurs 
                seront informés des modifications via la plateforme ou par email.
            </p>
        </div>

        <div class="cgu-section">
            <h2>3. Inscription et compte utilisateur</h2>
            <p>
                Pour accéder aux fonctionnalités principales (saisie des notes, recommandations, favoris), 
                l'utilisateur doit créer un compte en fournissant des informations exactes et complètes :
            </p>
            <ul>
                <li>Nom et prénom</li>
                <li>Adresse email valide</li>
                <li>Mot de passe (minimum 8 caractères)</li>
                <li>Wilaya (optionnel)</li>
                <li>Série du baccalauréat</li>
            </ul>
            <p>
                L'utilisateur est responsable de la confidentialité de son mot de passe et de toutes les 
                activités effectuées depuis son compte.
            </p>
        </div>

        <div class="cgu-section">
            <h2>4. Données personnelles et confidentialité</h2>
            <p>
                Les données personnelles collectées sont traitées conformément à notre 
                <strong>Politique de Confidentialité</strong>. Elles sont utilisées pour :
            </p>
            <ul>
                <li>Fournir les services de la plateforme</li>
                <li>Générer des recommandations personnalisées</li>
                <li>Améliorer l'expérience utilisateur</li>
                <li>Communiquer avec l'utilisateur (support, actualités)</li>
            </ul>
            <p>
                Conformément à la loi algérienne (Loi 18-07 relative à la protection des personnes physiques 
                dans le traitement des données à caractère personnel), vous disposez d'un droit d'accès, 
                de rectification et de suppression de vos données.
            </p>
        </div>

        <div class="cgu-section">
            <h2>5. Propriété intellectuelle</h2>
            <p>
                L'ensemble du contenu de la plateforme (textes, logos, graphismes, base de données, 
                algorithmes) est la propriété exclusive d'<strong>Orientation Pro</strong> ou de ses partenaires.
            </p>
            <p>
                Toute reproduction, représentation, modification ou exploitation non autorisée est interdite 
                et constitue une contrefaçon.
            </p>
        </div>

        <div class="cgu-section">
            <h2>6. Responsabilités</h2>
            <p>
                <strong>Orientation Pro</strong> s'efforce de fournir des informations exactes et à jour, 
                notamment les seuils d'admission et les données sur les filières. Toutefois :
            </p>
            <ul>
                <li>Les recommandations sont fournies à titre indicatif et ne constituent pas une garantie d'admission</li>
                <li>Les seuils d'admission sont basés sur les années précédentes et peuvent varier</li>
                <li>L'utilisateur reste seul responsable de ses choix d'orientation</li>
            </ul>
            <p>
                La plateforme ne saurait être tenue responsable des conséquences liées à l'utilisation 
                des informations fournies.
            </p>
        </div>

        <div class="cgu-section">
            <h2>7. Disponibilité du service</h2>
            <p>
                Orientation Pro s'engage à mettre tout en œuvre pour assurer un accès 24h/24 et 7j/7 à la plateforme. 
                Des interruptions temporaires peuvent survenir pour des raisons de maintenance technique.
            </p>
            <p>
                En cas de panne ou d'indisponibilité, nous nous engageons à rétablir le service dans les meilleurs délais.
            </p>
        </div>

        <div class="cgu-section">
            <h2>8. Modification et résiliation</h2>
            <p>
                L'utilisateur peut fermer son compte à tout moment en contactant le support. 
                Orientation Pro se réserve le droit de suspendre ou résilier un compte en cas de :
            </p>
            <ul>
                <li>Non-respect des présentes CGU</li>
                <li>Activité frauduleuse ou malveillante</li>
                <li>Inactivité prolongée du compte</li>
            </ul>
        </div>

        <div class="cgu-section">
            <h2>9. Loi applicable</h2>
            <p>
                Les présentes CGU sont régies par le droit algérien. Tout litige relatif à l'utilisation 
                de la plateforme sera soumis à la compétence des tribunaux d'Alger.
            </p>
        </div>

        <div class="cgu-section">
            <h2>10. Contact</h2>
            <p>
                Pour toute question relative aux présentes CGU, vous pouvez nous contacter :
            </p>
            <ul>
                <li>📧 Email : <a href="mailto:contact@orientation-pro.dz">contact@orientation-pro.dz</a></li>
                <li>📞 Téléphone : 05.57.79.86.69</li>
                <li>📍 Adresse : Sidi Bel Abbès, Algérie</li>
            </ul>
        </div>
    </div>

    <footer class="main-footer">
        <p>© 2025 Orientation Pro — Plateforme d'orientation académique et professionnelle (Algérie)</p>
        <p>
            <a href="a_propos.php">À propos</a> |
            <a href="faq.php">FAQ</a> |
            <a href="contact.php">Contact</a> |
            <a href="cgu.php">CGU</a>
        </p>
    </footer>

</body>
</html>