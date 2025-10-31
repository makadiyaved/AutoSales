<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Function to check and handle remember me cookies
function checkRememberMe() {
    global $pdo;
    
    try {
        // First check if the column exists
        $checkColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
        if ($checkColumn->rowCount() === 0) {
            return false;
        }

        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
            $token = $_COOKIE['remember_token'];
            $user_id = $_COOKIE['user_id'];
            
            // Verify token from database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND remember_token = ?");
            $stmt->execute([$user_id, $token]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Refresh token and cookies
                $new_token = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$new_token, $user['id']]);
                
                setcookie('remember_token', $new_token, time() + (86400 * 30), '/', '', true, true);
                setcookie('user_id', $user['id'], time() + (86400 * 30), '/', '', true, true);
                
                return true;
            }
        }
    } catch(PDOException $e) {
        error_log("Error in checkRememberMe: " . $e->getMessage());
    }
    return false;
}

// Check for remember me cookies on every page load
checkRememberMe();
?> 