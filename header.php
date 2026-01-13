<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';

// Debug information
// echo '<pre>';
// echo 'Session Data: ';
// print_r($_SESSION);
// echo '</pre>';
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
                        <div class="profile-container">
                            <?php 
                            // Debug info - uncomment to see session data
                            // echo '<!-- Session Data: ' . print_r($_SESSION, true) . ' -->';
                            
                            $profileImage = 'uploads/profile/default-avatar.png';
                            if (isset($_SESSION['profile_image'])) {
                                $profileImage = 'uploads/profile/' . htmlspecialchars($_SESSION['profile_image']);
                                // Check if file exists, if not use default
                                if (!file_exists($profileImage)) {
                                    $profileImage = 'uploads/profile/default-avatar.png';
                                }
                            }
                            $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
                            ?>
                            <img src="<?php echo $profileImage; ?>" 
                                 alt="Profile" 
                                 class="profile-image"
                                 onerror="this.src='uploads/profile/default-avatar.png'"
                                 onload="console.log('Image loaded:', this.src)">
                            <span class="username"><?php echo $username; ?></span>
                        </div>
                        <div class="dropdown-menu">
                            <a href="profile.php" class="dropdown-item">My Profile</a>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="admin/dashboard.php" class="dropdown-item">Admin Panel</a>
                            <?php endif; ?>
                            <a href="logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="login-link">Login</a></li>
                    <li><a href="signup.php" class="signup-link">Sign Up</a></li>
                <?php endif; ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const profileContainer = document.querySelector('.profile-container');
                        const dropdownMenu = document.querySelector('.dropdown-menu');
                        
                        // Toggle dropdown on profile click
                        profileContainer.addEventListener('click', function(e) {
                            e.stopPropagation();
                            dropdownMenu.classList.toggle('show');
                        });
                        
                        // Close dropdown when clicking outside
                        document.addEventListener('click', function() {
                            dropdownMenu.classList.remove('show');
                        });
                        
                        // Prevent dropdown from closing when clicking inside it
                        dropdownMenu.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    });
                </script>
            </ul>
        </nav>
    </header>
