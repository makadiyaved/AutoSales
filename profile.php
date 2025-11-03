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

<main class="profile-main">
    <div class="profile-hero">
        <div class="profile-hero-overlay"></div>
        <div class="profile-hero-content">
            <div class="profile-avatar-section">
                <div class="profile-avatar-container">
                    <div class="avatar-wrapper">
                        <img src="<?php echo !empty($userData['profile_photo']) ? htmlspecialchars($userData['profile_photo']) : 'uploads/profile/default-avatar.png'; ?>" 
                             alt="Profile Photo" id="profile-preview" class="profile-avatar-img">
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <form action="update_profile_photo.php" method="post" enctype="multipart/form-data" class="avatar-form">
                        <input type="file" name="profile_photo" id="profile-photo" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-gradient" onclick="document.getElementById('profile-photo').click()">
                            <i class="fas fa-edit"></i> Change Photo
                        </button>
                        <button type="submit" class="btn btn-primary" id="upload-btn" style="display: none;">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
                </div>
                
                <div class="profile-info-section">
                    <h1 class="profile-name"><?php echo htmlspecialchars($userData['username'] ?? 'User'); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
                    <div class="profile-badges">
                        <div class="badge">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Member since <?php echo !empty($userData['created_at']) ? date('F Y', strtotime($userData['created_at'])) : 'N/A'; ?></span>
                        </div>
                        <div class="badge verified">
                            <i class="fas fa-check-circle"></i>
                            <span>Verified Account</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-content-wrapper">
        <div class="container">
            <?php 
            // Display success/error messages from session
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['success']) . '
                </div>';
                unset($_SESSION['success']);
            }
            
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($_SESSION['error']) . '
                </div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="profile-sections">
                <!-- Account Information Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="card-title-section">
                            <h3 class="card-title">Account Information</h3>
                            <p class="card-subtitle">Update your personal details and contact information</p>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <form action="update_profile.php" method="post" class="profile-form">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user"></i>
                                        Username
                                    </label>
                                    <input type="text" id="username" name="username" class="form-input" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>">
                                    <span class="form-help">Choose a unique username</span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email Address
                                    </label>
                                    <input type="email" id="email" class="form-input" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                    <span class="form-help">We'll use this to contact you</span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone"></i>
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" class="form-input" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                                    <span class="form-help">Optional - for account recovery</span>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-gradient btn-large">
                                    <i class="fas fa-save"></i>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Settings Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <div class="card-icon security">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="card-title-section">
                            <h3 class="card-title">Security Settings</h3>
                            <p class="card-subtitle">Change your password to keep your account secure</p>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <form action="change_password.php" method="post" class="profile-form">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="current_password" class="form-label">
                                        <i class="fas fa-key"></i>
                                        Current Password
                                    </label>
                                    <input type="password" id="current_password" class="form-input" name="current_password" required>
                                    <span class="form-help">Enter your current password</span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-lock"></i>
                                        New Password
                                    </label>
                                    <input type="password" id="new_password" class="form-input" name="new_password" required>
                                    <span class="form-help">Choose a strong password</span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-check-circle"></i>
                                        Confirm New Password
                                    </label>
                                    <input type="password" id="confirm_password" class="form-input" name="confirm_password" required>
                                    <span class="form-help">Re-enter your new password</span>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-gradient btn-large">
                                    <i class="fas fa-shield-alt"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Enhanced Profile Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Profile photo preview and upload
    const profilePhoto = document.getElementById('profile-photo');
    const profilePreview = document.getElementById('profile-preview');
    const uploadBtn = document.getElementById('upload-btn');

    if (profilePhoto && profilePreview && uploadBtn) {
        profilePhoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                    uploadBtn.style.display = 'inline-flex';
                    uploadBtn.style.animation = 'fadeInUp 0.3s ease-out';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Enhanced form interactions
    const formInputs = document.querySelectorAll('.form-input');
    formInputs.forEach(input => {
        // Add focus/blur animations
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            this.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
            this.style.transform = 'translateY(0)';
        });

        // Add input validation feedback
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.parentElement.classList.remove('error');
                this.parentElement.classList.add('success');
            } else {
                this.parentElement.classList.remove('success');
                this.parentElement.classList.add('error');
            }
        });
    });

    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (newPassword && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            const helpText = this.parentElement.querySelector('.form-help');
            if (this.value && this.value !== newPassword.value) {
                this.style.borderColor = '#ef4444';
                this.parentElement.classList.add('error');
                helpText.textContent = 'Passwords do not match';
                helpText.style.color = '#ef4444';
            } else if (this.value && this.value === newPassword.value) {
                this.style.borderColor = '#10b981';
                this.parentElement.classList.remove('error');
                this.parentElement.classList.add('success');
                helpText.textContent = 'Passwords match ✓';
                helpText.style.color = '#10b981';
            } else {
                this.style.borderColor = '';
                this.parentElement.classList.remove('error', 'success');
                helpText.textContent = 'Re-enter your new password';
                helpText.style.color = '';
            }
        });
    }

    // Enhanced form submission handling
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                // Add loading state
                submitButton.classList.add('loading');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                submitButton.disabled = true;
                
                // Re-enable after 3 seconds (in case of errors)
                setTimeout(() => {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }, 3000);
                
                // Allow the form to submit
                return true;
            }
        });
    });

    // Smooth scroll for any anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe profile cards for scroll animations
    document.querySelectorAll('.profile-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Add hover effects to cards
    document.querySelectorAll('.profile-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Enhanced avatar hover effect
    const avatarWrapper = document.querySelector('.avatar-wrapper');
    if (avatarWrapper) {
        avatarWrapper.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        avatarWrapper.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'footer.php'; ?>
