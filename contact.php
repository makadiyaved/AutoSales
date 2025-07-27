<?php
// Database connection
$host = 'localhost';
$dbname = 'car_dealership';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

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
    <style>
        .contact-container {
            padding: 100px 5% 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .contact-info {
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .contact-form {
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-btn {
            background: #3498db;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background: #2980b9;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .contact-info-item {
            margin-bottom: 1.5rem;
        }

        .contact-info-item i {
            margin-right: 0.5rem;
            color: #3498db;
        }

        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
            </ul>
        </nav>
    </header>

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