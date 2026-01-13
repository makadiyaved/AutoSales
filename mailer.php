<?php
// includes/mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendResetPasswordEmail($to, $username, $resetLink) {
    $config = require __DIR__ . '/mail_config.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = $config['smtp_auth'];
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to, $username);
        $mail->addReplyTo($config['reply_to'], $config['from_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - AutoSales';
        
        // Email template
        $message = file_get_contents(__DIR__ . '/templates/email/reset-password.html');
        $message = str_replace(
            ['{{username}}', '{{reset_link}}', '{{year}}'],
            [htmlspecialchars($username), $resetLink, date('Y')],
            $message
        );
        
        $mail->msgHTML($message);
        $mail->AltBody = "Hello $username,\n\nYou have requested to reset your password. Please use the following link to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}