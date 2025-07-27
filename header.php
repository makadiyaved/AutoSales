<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoSales - Premium Car Dealership</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>AutoSales</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-menu">
                        <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="admin/dashboard.php" class="admin-link">Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php" class="logout-link">Logout</a>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="login-link">Login</a></li>
                    <li><a href="signup.php" class="signup-link">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
</html> 