<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function logEmailError($message) {
    $logDir = __DIR__ . '/../logs';
    $logFile = $logDir . '/email_errors.log';
    
    try {
        // Create logs directory if it doesn't exist
        if (!file_exists($logDir)) {
            if (!@mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $logDir));
            }
        }
        
        // Ensure the directory is writable
        if (!is_writable($logDir)) {
            @chmod($logDir, 0755);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Log to file
        if (@file_put_contents($logFile, $logMessage, FILE_APPEND) === false) {
            throw new RuntimeException("Failed to write to log file: $logFile");
        }
        
        // Also log to PHP error log
        error_log($message);
    } catch (Exception $e) {
        // If we can't log to file, at least log to PHP error log
        error_log('Logging error: ' . $e->getMessage());
        error_log('Original message: ' . $message);
    }
    
    return false;
}

function sendWelcomeEmail($to, $username) {
    // Validate email
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return logEmailError("Invalid email address: $to");
    }

    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                    // Disable debug output to prevent header issues
        $mail->Debugoutput = function($str, $level) {
            // Log debug output to file instead of outputting it
            file_put_contents(
                __DIR__ . '/../logs/mail_debug.log',
                date('Y-m-d H:i:s') . " [$level] $str\n",
                FILE_APPEND
            );
        };
        $mail->isSMTP();                                        // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                   // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = 'makadiyaved07@gmail.com';             // SMTP username (your Gmail)
        $mail->Password   = 'urro gpem dczp tpof';       // SMTP password (App Password for Gmail)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
        $mail->Port       = 587;                                // TCP port to connect to
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Recipients
        $mail->setFrom('noreply@autosales.com', 'AutoSales');
        $mail->addAddress($to, $username);     // Add a recipient
        $mail->addReplyTo('support@autosales.com', 'AutoSales Support');

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Welcome to AutoSales - Your Account Has Been Created';
        
        // HTML Message with car-themed design
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Welcome to AutoSales</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
                
                body { 
                    font-family: 'Poppins', Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333333;
                    background-color: #f5f7fa;
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
                    color: white; 
                    padding: 30px 20px;
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                }
                .header:before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('https://img.icons8.com/ios-filled/100/000000/car.png') no-repeat 90% 50%/100px auto;
                    opacity: 0.1;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 700;
                    position: relative;
                    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
                }
                .header p {
                    margin: 10px 0 0;
                    opacity: 0.9;
                    font-weight: 300;
                }
                .content { 
                    padding: 30px;
                    color: #444444;
                    line-height: 1.7;
                }
                .welcome-text {
                    font-size: 18px;
                    margin-bottom: 25px;
                    color: #1a237e;
                    font-weight: 500;
                }
                .cta-button {
                    display: inline-block;
                    background: #ff5722;
                    color: white !important;
                    text-decoration: none;
                    padding: 12px 30px;
                    border-radius: 50px;
                    font-weight: 600;
                    margin: 20px 0;
                    text-align: center;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 15px rgba(255,87,34,0.3);
                }
                .cta-button:hover {
                    background: #e64a19;
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(255,87,34,0.4);
                }
                .car-icon {
                    font-size: 24px;
                    vertical-align: middle;
                    margin: 0 5px;
                }
                .features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin: 30px 0;
    padding: 0 10px;
}
                .feature {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid rgba(0, 0, 0, 0.05);
}
                .feature::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: linear-gradient(90deg, #ff5722, #ff9800);
                    transition: all 0.4s ease;
                }
                .feature:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: #ffccbc;
}
                .feature:hover::before {
                    height: 6px;
                }
                .feature i {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff5f5;
    border-radius: 50%;
    color: #ff5722;
    font-size: 20px;
    box-shadow: 0 4px 10px rgba(255, 87, 34, 0.15);
    margin-right: 20px;
}
    .feature-content {
    text-align: left;
}
                .feature:hover i {
                    transform: rotateY(180deg);
                    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
                }
                .feature h3 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-size: 16px;
}
                .feature h3::after {
                    content: '';
                    position: absolute;
                    bottom: -8px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 40px;
                    height: 2px;
                    background: #ff9800;
                    transition: all 0.3s ease;
                }
                .feature:hover h3::after {
                    width: 60px;
                    background: #ff5722;
                }
                .feature p {
    margin: 0;
    color: #666;
    font-size: 13px;
    line-height: 1.5;
    padding-right: 10px;
}
                @media (max-width: 768px) {
    .features {
        grid-template-columns: 1fr;
    }

                    .feature {
                        padding: 25px 20px;
                    }
                }
                .feature i {
                    color: #1a237e;
                    font-size: 20px;
                }
                .footer { 
                    background: #f5f7fa;
                    padding: 20px;
                    text-align: center; 
                    font-size: 12px; 
                    color: #777777;
                    border-top: 1px solid #e0e0e0;
                }
                .social-links {
                    margin: 20px 0;
                }
                .social-links a {
                    display: inline-block;
                    margin: 0 10px;
                    color: #1a237e;
                    font-size: 20px;
                    transition: all 0.3s ease;
                }
                .social-links a:hover {
                    color: #ff5722;
                    transform: translateY(-3px);
                }
                    .icon {
    font-size: 24px;
    margin-right: 15px;
    min-width: 40px;
    text-align: center;
}
            </style>
        </head>
        <body style='margin: 0; padding: 0;'>
            <div class='container'>
                <div class='header'>
                    <h1>🚗 Welcome to AutoSales!</h1>
                    <p>Your journey to finding the perfect car starts here</p>
                </div>
                <div class='content'>
                    <div class='welcome-text'>
                        Hello $username,<br>
                        We're thrilled to have you on board!
                    </div>
                    
                    <p>Thank you for registering with AutoSales. Your account has been successfully created and you're now part of our growing community of car enthusiasts.</p>

                    <div class='features'>
                        <div class='feature'>
                            <span class='icon'>🚗</span>
                            <div class='feature-content'>
                                <h3>Wide Selection</h3>
                                <p>Browse thousands of vehicles from trusted dealers</p>
                            </div>
                        </div>
                        <div class='feature'>
                            <span class='icon'>💰</span>
                            <div class='feature-content'>
                                <h3>Best Deals</h3>
                                <p>Get the best deals and offers</p>
                            </div>
                        </div>
                        <div class='feature'>
                            <span class='icon'>🛡️</span>
                            <div class='feature-content'>
                                <h3>Secure Transactions</h3>
                                <p>Secure and trusted transactions</p>
                            </div>
                        </div>
                        <div class='feature'>
                            <span class='icon'>🎧</span>
                            <div class='feature-content'>
                                <h3>24/7 Support</h3>
                                <p>24/7 Customer support</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='http://autosales.test/' class='cta-button'>
                            Start Browsing Cars 🚀
                        </a>
                    </div>
                    
                    <p>If you have any questions or need assistance, our support team is here to help. Just reply to this email or contact us at support@autosales.com</p>
                    
                    <p>Happy car hunting! <span class='car-icon'>🚘</span></p>
                    
                    <p>Best regards,<br><strong>The AutoSales Team</strong></p>
                </div>
                
                <div class='social-links'>
                    <a href='#'><i class='fab fa-facebook'></i></a>
                    <a href='#'><i class='fab fa-twitter'></i></a>
                    <a href='#'><i class='fab fa-instagram'></i></a>
                    <a href='#'><i class='fab fa-linkedin'></i></a>
                </div>
                
                <div class='footer'>
                    <p>© " . date('Y') . " AutoSales. All rights reserved.<br>
                    <small>1234 Auto Drive, Motor City, MC 12345</small></p>
                    <p><small><a href='#' style='color: #666;'>Unsubscribe</a> | <a href='#' style='color: #666;'>Privacy Policy</a> | <a href='#' style='color: #666;'>Terms of Service</a></small></p>
                </div>
            </div>
        </body>
        </html>";
        
        // Plain text version for non-HTML mail clients
        $mail->AltBody = "Welcome to AutoSales!\n\n" .
                        "Hello $username,\n\n" .
                        "Thank you for registering with AutoSales. Your account has been successfully created.\n" .
                        "You can now log in to your account using your email address and the password you provided during registration.\n\n" .
                        "If you have any questions, feel free to contact our support team.\n\n" .
                        "Happy car shopping!\n\n" .
                        "Best regards,\nThe AutoSales Team\n\n" .
                        "© " . date('Y') . " AutoSales. All rights reserved.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        logEmailError("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
