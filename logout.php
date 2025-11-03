<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Clear remember me cookies
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time()-3600, '/', '', true, true);
}
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time()-3600, '/', '', true, true);
}

// Redirect to login page with success message
header("Location: login.php?message=logged_out");
exit();
?> 