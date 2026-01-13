<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once 'mailer.php';

$pageTitle = "Forgot Password - AutoSales";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare('SELECT id, username FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate a secure token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens for this user
            $pdo->prepare('DELETE FROM password_reset_tokens WHERE user_id = ?')
                ->execute([$user['id']]);
            
            // Insert new token
            $stmt = $pdo->prepare('INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $token, $expires]);
            
            // Generate reset link with hardcoded localhost URL
            $baseUrl = 'http://localhost/AutoSales';
            $resetLink = $baseUrl . "/reset-password.php?token=" . $token;
            
            // Send the reset email using the mailer function
            if (sendResetPasswordEmail($email, $user['username'], $resetLink)) {
                $success = 'A password reset link has been sent to your email address. Please check your inbox and follow the instructions.';
            } else {
                error_log("Failed to send password reset email to: $email");
                $error = 'Failed to send reset email. Please try again later.';
            }
        } else {
            // Don't reveal if the email exists or not (security best practice)
            $success = 'If an account exists with this email, a password reset link has been sent.';
        }

        } catch (PDOException $e) {
            error_log('Password reset error: ' . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}

include 'header.php';
?>

<main class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo">
                <h1>AutoSales</h1>
            </div>
            
            <div class="auth-header">
                <h1>Forgot Password?</h1>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>
            
            <form action="forgot-password.php" method="post" class="auth-form" id="forgotPasswordForm">
                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="btn-text">Send Reset Link</span>
                    <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
                
                <div class="auth-footer">
                    <p>Remember your password? <a href="login.php" class="text-link">Back to Login</a></p>
                </div>
            </form>
        </div>
        
        <div class="auth-illustration">
            <img src="images/auth/forgot-password" alt="Forgot Password" onerror="this.src='images/auth/placeholder.svg';">
        </div>
    </div>
</main>

<style>
/* Auth Page Styles */
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    padding: 20px;
}

.auth-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.auth-card {
    padding: 50px;
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
}

.auth-logo {
    text-align: center;
    margin-bottom: 30px;
}

.auth-logo img {
    max-width: 180px;
    height: auto;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h1 {
    font-size: 28px;
    color: #2d3748;
    margin: 0 0 10px;
    font-weight: 700;
}

.auth-header p {
    color: #718096;
    margin: 0;
    font-size: 15px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 14px;
    line-height: 1.5;
}

.alert i {
    margin-top: 2px;
    font-size: 18px;
}

.alert-error {
    background-color: #fef2f2;
    color: #dc2626;
    border-left: 4px solid #dc2626;
}

.alert-success {
    background-color: #f0fdf4;
    color: #16a34a;
    border-left: 4px solid #16a34a;
}

.form-group {
    margin-bottom: 20px;
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 16px;
}

.input-with-icon input {
    width: 100%;
    padding: 14px 16px 14px 45px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 15px;
    color: #2d3748;
    transition: all 0.3s ease;
    background-color: #f8fafc;
}

.input-with-icon input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
    background-color: #fff;
}

.btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 14px;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.5;
    color: white;
    text-align: center;
    text-decoration: none;
    background-color: #3b82f6;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    overflow: hidden;
}

.btn:hover {
    background-color: #2563eb;
    transform: translateY(-1px);
}

.btn:active {
    transform: translateY(0);
}

.btn .btn-text {
    position: relative;
    z-index: 1;
}

.btn .btn-loader {
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.btn.loading .btn-text {
    visibility: hidden;
}

.btn.loading .btn-loader {
    display: block;
}

.auth-footer {
    text-align: center;
    margin-top: 24px;
    color: #64748b;
    font-size: 14px;
}

.text-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.text-link:hover {
    color: #2563eb;
    text-decoration: underline;
}

.auth-illustration {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
}

.auth-illustration img {
    max-width: 100%;
    height: auto;
    max-height: 500px;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .auth-container {
        grid-template-columns: 1fr;
        max-width: 500px;
    }
    
    .auth-illustration {
        display: none;
    }
}

@media (max-width: 576px) {
    .auth-card {
        padding: 30px 20px;
    }
    
    .auth-header h1 {
        font-size: 24px;
    }
    
    .auth-header p {
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        if (form.checkValidity()) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        }
    });
    
    // Auto-hide success message after 10 seconds
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 300);
        }, 10000);
    }
});
</script>

<?php include 'footer.php'; ?>