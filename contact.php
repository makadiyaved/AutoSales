<?php
// Include header first to start session
require_once 'header.php';

// Database connection
require_once 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $car_id = $_POST['car_id'] ?? null;
    $message_text = $_POST['message'] ?? '';

    if ($name && $email && $phone && $message_text) {
        try {
            $stmt = $pdo->prepare("INSERT INTO inquiries (car_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$car_id, $name, $email, $phone, $message_text]);
            $message = "Thank you for your inquiry! We'll get back to you soon.";
        } catch(PDOException $e) {
            $message = "Sorry, there was an error submitting your inquiry. Please try again.";
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Fetch available cars for the dropdown
$cars = $pdo->query("SELECT id, make, model, year FROM cars WHERE status = 'available'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AutoSales</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main class="contact-container">
        <h1>Contact Us</h1>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>123 Auto Sales Street<br>Car City, CC 12345</p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-phone"></i>
                    <p>(555) 123-4567</p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <p>info@autosales.com</p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-clock"></i>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM<br>
                    Saturday: 10:00 AM - 4:00 PM<br>
                    Sunday: Closed</p>
                </div>
            </div>

            <div class="contact-form">
                <h2>Send Us a Message</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="car_id">Interested Vehicle (Optional)</label>
                        <select id="car_id" name="car_id">
                            <option value="">Select a vehicle</option>
                            <?php foreach ($cars as $car): ?>
                                <option value="<?php echo $car['id']; ?>">
                                    <?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@autosales.com</p>
                <p>Phone: (555) 123-4567</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 AutoSales. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 