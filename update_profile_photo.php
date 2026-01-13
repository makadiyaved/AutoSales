<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure columns exist even on older MySQL versions
function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        $stmt->execute([$table, $column]);
        $exists = (int)$stmt->fetchColumn() > 0;
        if (!$exists) {
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        }
    } catch (PDOException $e) {
        // As a fallback, try DESCRIBE
        try {
            $pdo->query("DESCRIBE `{$table}` `{$column}`");
        } catch (PDOException $e2) {
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        }
    }
}

ensureColumn($pdo, 'users', 'profile_image', 'VARCHAR(255) NULL');
ensureColumn($pdo, 'users', 'profile_photo', 'VARCHAR(255) NULL');

if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Please choose a valid image to upload.';
    header('Location: profile.php');
    exit();
}

$file = $_FILES['profile_photo'];

// Validate type and size
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowed[$mime])) {
    $_SESSION['error'] = 'Only JPG, PNG, or WEBP images are allowed.';
    header('Location: profile.php');
    exit();
}

if ($file['size'] > 2 * 1024 * 1024) { // 2MB
    $_SESSION['error'] = 'Image is too large. Max size is 2MB.';
    header('Location: profile.php');
    exit();
}

$ext = $allowed[$mime];
$dir = __DIR__ . '/uploads/profile/';
if (!is_dir($dir)) {
    @mkdir($dir, 0775, true);
}

$filename = uniqid('', true) . '.' . $ext;
$destPath = $dir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    $_SESSION['error'] = 'Failed to save uploaded image.';
    header('Location: profile.php');
    exit();
}

$relativePath = 'uploads/profile/' . $filename;

try {
    $stmt = $pdo->prepare('UPDATE users SET profile_image = ?, profile_photo = ? WHERE id = ?');
    $stmt->execute([$filename, $relativePath, $_SESSION['user_id']]);

    // Keep session avatar in sync for header
    // Header expects only the file name appended to uploads/profile/
    $_SESSION['profile_image'] = $filename;

    $_SESSION['success'] = 'Profile photo updated successfully.';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to update profile photo in database.';
}

header('Location: profile.php');
exit();
?>


