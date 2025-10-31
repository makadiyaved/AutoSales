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
                        <div class="profile-container">
                            <img src="uploads/profile/<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" 
                                 alt="Profile" 
                                 class="profile-image"
                                 onerror="this.src='uploads/profile/default-avatar.png'">
                            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
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
                <style>
                    .user-menu {
                        position: relative;
                        display: inline-block;
                        padding: 8px 15px;
                        border-radius: 4px;
                    }
                    .profile-container {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        cursor: pointer;
                        padding: 0;
                        border-radius: 20px;
                        transition: background-color 0.3s;
                        height: 32px;
                    }
                    .profile-container:hover {
                        background-color: rgba(255, 255, 255, 0.1);
                    }
                    .profile-image {
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        object-fit: cover;
                        border: 2px solid #fff;
                    }
                    .dropdown-menu {
                        display: none;
                        position: absolute;
                        right: 0;
                        background-color: #fff;
                        min-width: 200px;
                        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                        border-radius: 4px;
                        z-index: 1;
                        margin-top: 10px;
                    }
                    .dropdown-menu a {
                        color: #333;
                        padding: 10px 15px;
                        text-decoration: none;
                        display: block;
                        transition: background-color 0.3s;
                    }
                    .dropdown-menu a:hover {
                        background-color: #f5f5f5;
                    }
                    .dropdown-menu.show {
                        display: block;
                    }
                    .profile-container {
                        cursor: pointer;
                    }
                </style>
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
