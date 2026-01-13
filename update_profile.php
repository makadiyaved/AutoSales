<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

function ensureUserColumns(PDO $pdo): void {
    $ensure = function(string $column, string $def) use ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = ?");
            $stmt->execute([$column]);
            if ((int)$stmt->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `{$column}` {$def}");
            }
        } catch (PDOException $e) {
            try { $pdo->query("DESCRIBE `users` `{$column}`"); }
            catch (PDOException $e2) { $pdo->exec("ALTER TABLE `users` ADD COLUMN `{$column}` {$def}"); }
        }
    };
    $ensure('phone', 'VARCHAR(20) NULL');
    $ensure('profile_image', 'VARCHAR(255) NULL');
    $ensure('profile_photo', 'VARCHAR(255) NULL');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: profile.php');
    exit();
}

ensureUserColumns($pdo);

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$errors = [];

if ($username === '') {
    $errors[] = 'Username is required.';
} elseif (strlen($username) < 2) {
    $errors[] = 'Username must be at least 2 characters.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// Ensure email is unique for other users
try {
    // Username uniqueness
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');
    $stmt->execute([$username, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $errors[] = 'This username is already in use by another account.';
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $errors[] = 'This email is already in use by another account.';
    }
} catch (PDOException $e) {
    $errors[] = 'Error validating email. Please try again.';
}

// Normalize phone (optional)
if ($phone !== '') {
    $normalized = preg_replace('/[^0-9+\-\s()]/', '', $phone);
    if (strlen($normalized) > 20) {
        $errors[] = 'Phone number is too long.';
    } else {
        $phone = $normalized;
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: profile.php');
    exit();
}

// Start a transaction for atomic updates
try {
    $pdo->beginTransaction();
    
    // First, get the current user data
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $currentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentData) {
        throw new Exception('User not found');
    }
    
    // Update the profile
    $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?');
    $success = $stmt->execute([
        $username, 
        $email, 
        $phone !== '' ? $phone : null, 
        $_SESSION['user_id']
    ]);

    if (!$success) {
        throw new PDOException('Failed to update profile');
    }

    // Keep session values in sync
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    
    // Also update phone in session if it exists in the database
    if (isset($currentData['phone'])) {
        $_SESSION['phone'] = $phone;
    }

    $pdo->commit();
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['success'] = 'No changes detected.';
    } else {
        $_SESSION['success'] = 'Profile updated successfully.';
    }
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    if ($e instanceof PDOException) {
        error_log('Database error during profile update: ' . $e->getMessage());
        $_SESSION['error'] = 'A database error occurred. Please try again.';
    } else {
        error_log('Profile update error: ' . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: profile.php');
exit();
?>

