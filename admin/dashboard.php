<?php
require_once 'auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AutoSales</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: white;
            padding: 1.5rem 0;
        }
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #334155;
        }
        .sidebar-header h2 {
            color: white;
            margin: 0;
        }
        .sidebar-menu {
            margin-top: 1.5rem;
        }
        .menu-item {
            padding: 0.75rem 1.5rem;
            color: #e2e8f0;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        .menu-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        .menu-item:hover, .menu-item.active {
            background: #334155;
            color: white;
        }
        .main-content {
            flex: 1;
            background: #f1f5f9;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .header h1 {
            margin: 0;
            color: #1e293b;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .stat-card h3 {
            margin: 0 0 0.5rem;
            color: #64748b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #1e293b;
            margin: 0.5rem 0;
        }
        .stat-card .change {
            color: #10b981;
            font-size: 0.9rem;
        }
        .recent-activity {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .recent-activity h2 {
            margin-top: 0;
            color: #1e293b;
            font-size: 1.25rem;
        }
        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0369a1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        .activity-details {
            flex: 1;
        }
        .activity-title {
            font-weight: 500;
            margin: 0 0 0.25rem;
            color: #1e293b;
        }
        .activity-time {
            color: #64748b;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>AutoSales Admin</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="vehicles.php" class="menu-item">
                    <i class="fas fa-car"></i>
                    Vehicles
                </a>
                <a href="users.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    Users
                </a>
                <a href="bookings.php" class="menu-item">
                    <i class="fas fa-calendar-check"></i>
                    Bookings
                </a>
                <a href="reports.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
                <a href="?logout=1" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Vehicles</h3>
                    <div class="value">42</div>
                    <div class="change">+12% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value">128</div>
                    <div class="change">+8% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="value">56</div>
                    <div class="change">+23% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <div class="value">$24,890</div>
                    <div class="change">+15% from last month</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="activity-details">
                        <h3 class="activity-title">New vehicle added</h3>
                        <p class="activity-time">10 minutes ago</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background: #f0fdf4; color: #059669;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="activity-details">
                        <h3 class="activity-title">Booking confirmed #12345</h3>
                        <p class="activity-time">2 hours ago</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background: #fef2f2; color: #dc2626;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="activity-details">
                        <h3 class="activity-title">New user registered</h3>
                        <p class="activity-time">5 hours ago</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="activity-details">
                        <h3 class="activity-title">New contact form submission</h3>
                        <p class="activity-time">1 day ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
