<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Clear remember token from database if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        // First check if the column exists
        $checkColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
        if ($checkColumn->rowCount() > 0) {
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
    } catch(PDOException $e) {
        // If there's an error, just continue with logout
        error_log("Error clearing remember token: " . $e->getMessage());
    }
}

// Clear session
session_destroy();

// Clear cookies
setcookie('remember_token', '', time() - 3600, '/', '', true, true);
setcookie('user_id', '', time() - 3600, '/', '', true, true);

header("Location: login.php");
exit();
?> 