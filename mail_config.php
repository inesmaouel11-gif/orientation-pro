<?php
// Configuration pour l'envoi d'emails avec Gmail
// À placer dans C:\xampp\htdocs\OrientationPro\

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

function sendResetEmail($to_email, $reset_link) {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // ⚠️ À MODIFIER AVEC VOS IDENTIFIANTS GMAIL ⚠️
        $mail->Username   = 'inesmaouel11@gmail.com';  // Votre email Gmail
        $mail->Password   = 'plyl wbvw okao jxpi';     // Votre mot de passe Gmail
        // ⚠️ FIN DES MODIFICATIONS ⚠️
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Expéditeur et destinataire
        $mail->setFrom('VOTRE_EMAIL@gmail.com', 'Orientation Pro');
        $mail->addAddress($to_email);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Réinitialisation de votre mot de passe - Orientation Pro';
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { padding: 20px; background: #f4f6f9; }
                    .card { background: white; padding: 25px; border-radius: 10px; }
                    .btn { 
                        background: #1890ff; 
                        color: white; 
                        padding: 12px 25px; 
                        text-decoration: none; 
                        border-radius: 5px;
                        display: inline-block;
                    }
                    .footer { margin-top: 20px; font-size: 12px; color: #888; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='card'>
                        <h2>🎓 Orientation Pro</h2>
                        <p>Bonjour,</p>
                        <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
                        <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                        <p style='text-align: center;'>
                            <a href='$reset_link' class='btn'>Réinitialiser mon mot de passe</a>
                        </p>
                        <p>Ou copiez ce lien dans votre navigateur :</p>
                        <p><a href='$reset_link'>$reset_link</a></p>
                        <p>Ce lien est valable pendant <strong>1 heure</strong>.</p>
                        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
                        <div class='footer'>
                            <hr>
                            <p>Orientation Pro - Plateforme d'orientation post-bac Algérie</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Bonjour,\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nCopiez ce lien dans votre navigateur : $reset_link\n\nCe lien est valable pendant 1 heure.\n\nOrientation Pro";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>