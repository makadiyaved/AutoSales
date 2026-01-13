<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug information
echo '<pre>';
echo 'Session ID: ' . session_id() . "\n";
echo 'Session Data: ';
print_r($_SESSION);

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    echo "\nUser is logged in.\n";
    echo 'User ID: ' . $_SESSION['user_id'] . "\n";
    echo 'Username: ' . ($_SESSION['username'] ?? 'Not set') . "\n";
    echo 'Profile Image: ' . ($_SESSION['profile_image'] ?? 'Not set') . "\n";
    
    // Check if profile image file exists
    $profileImage = 'uploads/profile/' . ($_SESSION['profile_image'] ?? 'default-avatar.png');
    echo 'Profile image path: ' . $profileImage . "\n";
    echo 'File exists: ' . (file_exists($profileImage) ? 'Yes' : 'No') . "\n";
} else {
    echo "\nUser is not logged in.\n";
}

echo '</pre>';

// Add a link to go back
if (basename($_SERVER['HTTP_REFERER'] ?? '') !== 'debug_session.php') {
    echo '<p><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'index.php') . '">Go Back</a></p>';
}
?>
