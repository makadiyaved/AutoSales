<?php
session_start();
require_once 'auth.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user data from the form
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Initialize error log message
    $logMessage = "Profile update attempt - User ID: " . $_SESSION['user_id'] . ", IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $logMessage .= "Request data: " . print_r($_POST, true) . "\n";

    // Validate the data
    $errors = [];

    // Validate username
    if (strlen($username) < 2 || preg_match('/\d/', $username)) {
        $errors[] = 'Username must be at least 2 characters and cannot contain numbers.';
        $logMessage .= "Validation failed: Invalid username format\n";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
        $logMessage .= "Validation failed: Invalid email format\n";
    } else {
        // Check if email already exists for another user
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->rowCount() > 0) {
                $errors[] = 'This email is already registered with another account.';
                $logMessage .= "Validation failed: Email already exists\n";
            }
        } catch (PDOException $e) {
            error_log("Error checking email uniqueness: " . $e->getMessage());
            $errors[] = 'An error occurred while verifying your email. Please try again.';
            $logMessage .= "Error checking email uniqueness: " . $e->getMessage() . "\n";
        }
    }

    // Format and validate phone number
    if (!empty($phone)) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Validate length
        if (strlen($phone) > 10) {
            $errors[] = 'Phone number must be 10 digits or less.';
            $logMessage .= "Validation failed: Phone number too long\n";
        } elseif (!empty($phone)) {
            // Format as (XXX) XXX-XXXX if it's 10 digits
            if (strlen($phone) === 10) {
                $phone = '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6);
            }
        }
    }

    // If there are errors, log them and redirect back
    if (!empty($errors)) {
        error_log($logMessage . "Errors: " . implode(", ", $errors));
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: profile.php');
        exit();
    }

    // Update the user's profile in the database
    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$username, $email, $phone, $_SESSION['user_id']]);

        // Log successful update
        $logMessage .= "Profile updated successfully\n";
        error_log($logMessage);

        // Set success message
        $_SESSION['success'] = 'Profile updated successfully!';
        
        // Update session username if it was changed
        if (isset($_SESSION['username']) && $_SESSION['username'] !== $username) {
            $_SESSION['username'] = $username;
            $logMessage .= "Session username updated\n";
        }
        
        header('Location: profile.php');
        exit();

    } catch (PDOException $e) {
        // Log detailed error
        $errorMsg = "Database error updating profile: " . $e->getMessage();
        error_log($logMessage . $errorMsg . "\nSQL: " . $e->getTraceAsString());
        
        $_SESSION['error'] = 'Error updating profile. Please try again.';
        header('Location: profile.php');
        exit();
    }

} else {
    // Log invalid request method
    error_log("Invalid request method for profile update. Method: " . $_SERVER['REQUEST_METHOD'] . ", IP: " . $_SERVER['REMOTE_ADDR']);
    
    // If the request method is not POST, redirect to the profile page
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: profile.php');
    exit();
}
?>