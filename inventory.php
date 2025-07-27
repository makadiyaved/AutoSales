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

// Get category from URL if set
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$make = isset($_GET['make']) ? $_GET['make'] : '';

// Build query
$query = "SELECT * FROM cars WHERE status = 'available'";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (make LIKE ? OR model LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($price_range) {
    list($min_price, $max_price) = explode('-', $price_range);
    if ($max_price === '+') {
        $query .= " AND price >= ?";
        $params[] = $min_price;
    } else {
        $query .= " AND price BETWEEN ? AND ?";
        $params[] = $min_price;
        $params[] = $max_price;
    }
}

if ($make) {
    $query .= " AND make = ?";
    $params[] = $make;
}

$query .= " ORDER BY price DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique makes for filter
$makes = $pdo->query("SELECT DISTINCT make FROM cars ORDER BY make")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inventory - AutoSales</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .inventory-container {
            padding: 100px 5% 4rem;
        }
        
        .filters {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--section-bg);
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .filter-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-group select, 
        .filter-group input {
            padding: 0.8rem 1.2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            min-width: 200px;
            background: white;
        }
        
        .filter-group button {
            padding: 0.8rem 1.5rem;
            background: var(--gradient-1);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .filter-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }
        
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }
        
        .car-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .car-card:hover .car-image {
            transform: scale(1.05);
        }
        
        .car-details {
            padding: 1.5rem;
        }
        
        .car-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        
        .car-price {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .car-features {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1rem 0;
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        .car-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .car-feature i {
            color: var(--accent-color);
        }
        
        .view-details {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: var(--gradient-1);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
            width: 100%;
        }
        
        .view-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }
        
        .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(0,0,0,0.7);
            color: white;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group select,
            .filter-group input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="inventory-container">
        <h1>Our Inventory</h1>
        
        <div class="filters">
            <form method="GET" action="">
                <div class="filter-group">
                    <select name="category">
                        <option value="">All Categories</option>
                        <option value="suv" <?php echo $category === 'suv' ? 'selected' : ''; ?>>SUV</option>
                        <option value="supercar" <?php echo $category === 'supercar' ? 'selected' : ''; ?>>Supercar</option>
                        <option value="luxury" <?php echo $category === 'luxury' ? 'selected' : ''; ?>>Luxury</option>
                        <option value="sports" <?php echo $category === 'sports' ? 'selected' : ''; ?>>Sports</option>
                        <option value="electric" <?php echo $category === 'electric' ? 'selected' : ''; ?>>Electric</option>
                        <option value="classic" <?php echo $category === 'classic' ? 'selected' : ''; ?>>Classic</option>
                    </select>
                    
                    <select name="make">
                        <option value="">All Makes</option>
                        <?php foreach ($makes as $car_make): ?>
                            <option value="<?php echo htmlspecialchars($car_make); ?>" <?php echo $make === $car_make ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($car_make); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="price_range">
                        <option value="">Price Range</option>
                        <option value="0-50000" <?php echo $price_range === '0-50000' ? 'selected' : ''; ?>>$0 - $50,000</option>
                        <option value="50000-100000" <?php echo $price_range === '50000-100000' ? 'selected' : ''; ?>>$50,000 - $100,000</option>
                        <option value="100000-200000" <?php echo $price_range === '100000-200000' ? 'selected' : ''; ?>>$100,000 - $200,000</option>
                        <option value="200000+" <?php echo $price_range === '200000+' ? 'selected' : ''; ?>>$200,000+</option>
                    </select>
                    
                    <input type="text" name="search" placeholder="Search by make, model, or keyword" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <div class="car-grid">
            <?php foreach ($cars as $car): ?>
                <div class="car-card">
                    <span class="category-badge"><?php echo ucfirst(htmlspecialchars($car['category'])); ?></span>
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>" 
                         class="car-image">
                    <div class="car-details">
                        <h3 class="car-title"><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                        <p class="car-price">$<?php echo number_format($car['price']); ?></p>
                        <div class="car-features">
                            <span class="car-feature">
                                <i class="fas fa-calendar"></i>
                                <?php echo htmlspecialchars($car['year']); ?>
                            </span>
                            <span class="car-feature">
                                <i class="fas fa-tachometer-alt"></i>
                                <?php echo number_format($car['mileage']); ?> miles
                            </span>
                            <span class="car-feature">
                                <i class="fas fa-gas-pump"></i>
                                <?php echo htmlspecialchars($car['fuel_type']); ?>
                            </span>
                            <span class="car-feature">
                                <i class="fas fa-cog"></i>
                                <?php echo htmlspecialchars($car['transmission']); ?>
                            </span>
                        </div>
                        <a href="car-details.php?id=<?php echo $car['id']; ?>" class="view-details">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html> 