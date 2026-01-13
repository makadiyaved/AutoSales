<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: profile.php');
    exit();
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$errors = [];
if ($new !== $confirm) {
    $errors[] = 'New passwords do not match.';
}
if (strlen($new) < 6) {
    $errors[] = 'New password must be at least 6 characters.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: profile.php');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || !password_verify($current, $row['password'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        header('Location: profile.php');
        exit();
    }

    $hash = password_hash($new, PASSWORD_BCRYPT);
    $upd = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
    $upd->execute([$hash, $_SESSION['user_id']]);

    $_SESSION['success'] = 'Password updated successfully.';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to update password. ' . htmlspecialchars($e->getMessage());
}

header('Location: profile.php');
exit();
?>


