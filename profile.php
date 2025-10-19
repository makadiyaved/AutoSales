<?php
session_start();
require_once 'auth.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user data
$userData = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching user data: " . $e->getMessage();
}

$pageTitle = "My Profile - AutoSales";
include 'header.php';
?>

<main class="container">
    <div class="profile-container">
        <h1>My Profile</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar">
                <img src="<?php echo !empty($userData['profile_photo']) ? htmlspecialchars($userData['profile_photo']) : 'images/default-avatar.png'; ?>" 
                     alt="Profile Photo" id="profile-preview">
                <form action="update_profile_photo.php" method="post" enctype="multipart/form-data" class="mt-3">
                    <input type="file" name="profile_photo" id="profile-photo" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('profile-photo').click()">
                        Change Photo
                    </button>
                    <button type="submit" class="btn btn-primary" id="upload-btn" style="display: none;">
                        Upload
                    </button>
                </form>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($userData['username'] ?? 'User'); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
                <p>Member since: <?php echo !empty($userData['created_at']) ? date('F Y', strtotime($userData['created_at'])) : 'N/A'; ?></p>
            </div>
        </div>

        <div class="profile-details">
            <h3>Account Information</h3>
            <form action="update_profile.php" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <div class="change-password mt-4">
            <h3>Change Password</h3>
            <form action="change_password.php" method="post">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" class="form-control" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</main>

<script>
// Preview profile photo before upload
const profilePhoto = document.getElementById('profile-photo');
const profilePreview = document.getElementById('profile-preview');
const uploadBtn = document.getElementById('upload-btn');

profilePhoto.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            profilePreview.src = e.target.result;
            uploadBtn.style.display = 'inline-block';
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'footer.php'; ?>
