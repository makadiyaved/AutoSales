<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'car_dealership';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get car ID from URL
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$car_id) {
    header('Location: inventory.php');
    exit();
}

// Get car details
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header('Location: inventory.php');
    exit();
}

// Get related cars (same category, excluding current car)
$related_stmt = $pdo->prepare("SELECT * FROM cars WHERE category = ? AND id != ? AND status = 'available' LIMIT 3");
$related_stmt->execute([$car['category'], $car_id]);
$related_cars = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['make'] . ' ' . $car['model'] . ' - AutoSales') ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .car-details-container {
            padding: 120px 5% 4rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-button i {
            margin-right: 8px;
        }
        
        .car-detail-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        
        .car-gallery {
            position: relative;
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1rem;
        }
        
        .thumbnail-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        
        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        
        .thumbnail:hover, .thumbnail.active {
            opacity: 1;
            transform: scale(1.05);
        }
        
        .car-info {
            padding: 1rem 0;
        }
        
        .car-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        
        .car-price {
            font-size: 1.8rem;
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .car-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 2rem 0;
            padding: 1.5rem;
            background: var(--section-bg);
            border-radius: 10px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .meta-item i {
            color: var(--accent-color);
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
        
        .meta-text span {
            display: block;
            font-size: 0.9rem;
            color: var(--light-text);
        }
        
        .meta-text strong {
            font-weight: 600;
            color: var(--text-color);
        }
        
        .section-title {
            font-size: 1.8rem;
            margin: 3rem 0 1.5rem;
            color: var(--text-color);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: var(--gradient-1);
        }
        
        .car-description {
            line-height: 1.8;
            color: var(--light-text);
            margin-bottom: 2rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem;
            background: var(--section-bg);
            border-radius: 8px;
        }
        
        .feature-item i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--gradient-1);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .related-cars {
            margin: 4rem 0;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .related-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .related-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .related-info {
            padding: 1.5rem;
        }
        
        .related-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .related-price {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .related-link {
            display: inline-block;
            margin-top: 1rem;
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .related-link:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        
        @media (max-width: 992px) {
            .car-detail-wrapper {
                grid-template-columns: 1fr;
            }
            
            .main-image {
                height: 350px;
            }
            
            .car-meta {
                grid-template-columns: 1fr;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="car-details-container">
        <a href="inventory.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
        
        <div class="car-detail-wrapper">
            <div class="car-gallery">
                <img src="<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>" class="main-image" id="mainImage">
                <div class="thumbnail-container">
                    <img src="<?= htmlspecialchars($car['image_url']) ?>" alt="Thumbnail 1" class="thumbnail active" onclick="changeImage(this, '<?= htmlspecialchars($car['image_url']) ?>')">
                    <!-- Additional thumbnails can be added here if you have more images -->
                </div>
            </div>
            
            <div class="car-info">
                <h1 class="car-title"><?= htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year']) ?></h1>
                <div class="car-price">$<?= number_format($car['price'], 2) ?></div>
                
                <div class="car-meta">
                    <div class="meta-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <div class="meta-text">
                            <span>Mileage</span>
                            <strong><?= number_format($car['mileage']) ?> mi</strong>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-paint-brush"></i>
                        <div class="meta-text">
                            <span>Color</span>
                            <strong><?= htmlspecialchars($car['color']) ?></strong>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-cog"></i>
                        <div class="meta-text">
                            <span>Transmission</span>
                            <strong><?= htmlspecialchars($car['transmission']) ?></strong>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-gas-pump"></i>
                        <div class="meta-text">
                            <span>Fuel Type</span>
                            <strong><?= htmlspecialchars($car['fuel_type']) ?></strong>
                        </div>
                    </div>
                </div>
                
                <p class="car-description">
                    <?= nl2br(htmlspecialchars($car['description'])) ?>
                </p>
                
                <div class="features-grid">
                    <div class="feature-item">
                        <i class="fas fa-car"></i>
                        <span>Make: <?= htmlspecialchars($car['make']) ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-car-side"></i>
                        <span>Model: <?= htmlspecialchars($car['model']) ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Year: <?= htmlspecialchars($car['year']) ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-tag"></i>
                        <span>Category: <?= ucfirst(htmlspecialchars($car['category'])) ?></span>
                    </div>
                </div>
                
                <button class="btn btn-block" onclick="window.location.href='contact.php?car=<?= urlencode($car['make'] . ' ' . $car['model']) ?>'">
                    <i class="fas fa-envelope"></i> Contact Us About This Car
                </button>
                
            </div>
        </div>
        
        <?php if (!empty($related_cars)): ?>
        <div class="related-cars">
            <h2 class="section-title">You May Also Like</h2>
            <div class="related-grid">
                <?php foreach ($related_cars as $related): ?>
                <div class="related-card">
                    <img src="<?= htmlspecialchars($related['image_url']) ?>" alt="<?= htmlspecialchars($related['make'] . ' ' . $related['model']) ?>">
                    <div class="related-info">
                        <h3 class="related-title"><?= htmlspecialchars($related['make'] . ' ' . $related['model']) ?></h3>
                        <div class="related-price">$<?= number_format($related['price'], 2) ?></div>
                        <div style="margin: 0.5rem 0; font-size: 0.9rem; color: #666;">
                            <span><?= number_format($related['mileage']) ?> mi</span> • 
                            <span><?= htmlspecialchars($related['year']) ?></span>
                        </div>
                        <a href="car-details.php?id=<?= $related['id'] ?>" class="related-link">View Details <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script>
        function changeImage(element, src) {
            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            
            // Add active class to clicked thumbnail
            element.classList.add('active');
            
            // Change main image
            document.getElementById('mainImage').src = src;
        }
        
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
