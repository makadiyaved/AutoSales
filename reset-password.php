<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Reset Password - AutoSales";
$error = '';
$success = '';
$validToken = false;
$token = $_GET['token'] ?? '';
$user = null;

// Validate token
if (!empty($token)) {
    try {
        // Find valid, non-expired token
        $stmt = $pdo->prepare('SELECT * FROM password_reset_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()');
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tokenData) {
            // Get user data
            $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ?');
            $stmt->execute([$tokenData['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $validToken = true;
            }
        }
    } catch (PDOException $e) {
        error_log('Password reset error: ' . $e->getMessage());
        $error = 'An error occurred. Please try again.';
    }
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if (empty($password) || strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Update user's password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hashedPassword, $user['id']]);
            
            // Mark token as used
            $stmt = $pdo->prepare('UPDATE password_reset_tokens SET used = 1 WHERE token = ?');
            $stmt->execute([$token]);
            
            // Commit transaction
            $pdo->commit();
            
            // Set success message
            $success = 'Your password has been reset successfully. You can now <a href="login.php">log in</a> with your new password.';
            $validToken = false; // Prevent form from showing again
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log('Password reset error: ' . $e->getMessage());
            $error = 'An error occurred while resetting your password. Please try again.';
        }
    }
}

include 'header.php';
?>

<main class="auth-container">
    <div class="auth-card">
        <?php if ($validToken): ?>
            <div class="auth-header">
                <h1>Reset Your Password</h1>
                <p>Enter your new password below.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="post" class="auth-form">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" required 
                           minlength="8" placeholder="At least 8 characters">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-control" required minlength="8" placeholder="Re-enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
            
        <?php elseif ($success): ?>
            <div class="auth-header">
                <h1>Password Reset Successful</h1>
            </div>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
            
        <?php else: ?>
            <div class="auth-header">
                <h1>Invalid or Expired Link</h1>
            </div>
            <div class="alert alert-danger">
                The password reset link is invalid or has expired. 
                Please <a href="forgot-password.php">request a new one</a>.
            </div>
        <?php endif; ?>
        
        <div class="auth-footer">
            <p>Remember your password? <a href="login.php">Log in</a></p>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
