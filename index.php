<?php include 'header.php'; ?>

    <main>
        <section class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="hero-title-line">Find Your</span>
                    <span class="hero-title-line highlight">Dream Car</span>
                </h1>
                <p class="hero-subtitle">Discover our premium collection of luxury and performance vehicles</p>
                <div class="hero-buttons">
                    <a href="inventory.php" class="cta-button primary">View Inventory</a>
                    <a href="contact.php" class="cta-button secondary">Contact Us</a>
                </div>
                <div class="hero-features">
                    <div class="feature">
                        <i class="fas fa-car"></i>
                        <span>Premium Selection</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Certified Quality</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-handshake"></i>
                        <span>Best Deals</span>
                    </div>
                </div>
            </div>
            <a href="#categories" class="hero-scroll">
                <span>Scroll Down</span>
                <i class="fas fa-chevron-down"></i>
            </a>
        </section>

        <section id="categories" class="categories">
            <h2>Browse by Category</h2>
            <div class="category-grid">
                <div class="category-card" data-category="suv">
                    <div class="category-image">
                        <img src="images/categories/suv.jpg" alt="SUV Cars">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h3>SUV</h3>
                        <p>Spacious and versatile vehicles for any adventure</p>
                        <a href="inventory.php?category=suv" class="category-link">View SUVs</a>
                    </div>
                </div>

                <div class="category-card" data-category="supercar">
                    <div class="category-image">
                        <img src="images/categories/supercar.jpg" alt="Super Cars">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <h3>Super Cars</h3>
                        <p>High-performance machines that push boundaries</p>
                        <a href="inventory.php?category=supercar" class="category-link">View Super Cars</a>
                    </div>
                </div>

                <div class="category-card" data-category="luxury">
                    <div class="category-image">
                        <img src="images/categories/luxury.jpg" alt="Luxury Cars">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-car-alt"></i>
                        </div>
                        <h3>Luxury</h3>
                        <p>Premium vehicles with exceptional comfort</p>
                        <a href="inventory.php?category=luxury" class="category-link">View Luxury Cars</a>
                    </div>
                </div>

                <div class="category-card" data-category="sports">
                    <div class="category-image">
                        <img src="images/categories/sports.jpg" alt="Sports Cars">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h3>Sports Cars</h3>
                        <p>Dynamic and agile performance vehicles</p>
                        <a href="inventory.php?category=sports" class="category-link">View Sports Cars</a>
                    </div>
                </div>

                <div class="category-card" data-category="electric">
                    <div class="category-image">
                        <img src="images/categories/electric.jpg" alt="Electric Cars">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Electric</h3>
                        <p>Eco-friendly vehicles for the future</p>
                        <a href="inventory.php?category=electric" class="category-link">View Electric Cars</a>
                    </div>
                </div>

                <div class="category-card" data-category="classic">
                    <div class="category-image">
                        <img src="images/categories/classic.jpg" alt="Classic Cars">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h3>Classic</h3>
                        <p>Timeless vehicles with rich history</p>
                        <a href="inventory.php?category=classic" class="category-link">View Classic Cars</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-cars">
            <h2>Featured Vehicles</h2>
            <div class="car-grid">
                <?php
                // Database connection
                $host = 'localhost';
                $dbname = 'car_dealership';
                $username = 'root';
                $password = '';

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Get one featured car from each category using a subquery
                    $query = "SELECT c.* FROM cars c
                             INNER JOIN (
                                 SELECT category, MAX(price) as max_price
                                 FROM cars
                                 WHERE status = 'available'
                                 GROUP BY category
                             ) m ON c.category = m.category AND c.price = m.max_price
                             ORDER BY c.price DESC";
                    
                    $stmt = $pdo->query($query);
                    
                    while($car = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="car-card">';
                        echo '<span class="category-badge">' . ucfirst(htmlspecialchars($car['category'])) . '</span>';
                        echo '<img src="' . htmlspecialchars($car['image_url']) . '" alt="' . htmlspecialchars($car['make'] . ' ' . $car['model']) . '" class="car-image">';
                        echo '<div class="car-details">';
                        echo '<h3 class="car-title">' . htmlspecialchars($car['make'] . ' ' . $car['model']) . '</h3>';
                        echo '<p class="car-price">$' . number_format($car['price']) . '</p>';
                        echo '<div class="car-features">';
                        echo '<span class="car-feature"><i class="fas fa-calendar"></i> ' . htmlspecialchars($car['year']) . '</span>';
                        echo '<span class="car-feature"><i class="fas fa-tachometer-alt"></i> ' . number_format($car['mileage']) . ' miles</span>';
                        echo '<span class="car-feature"><i class="fas fa-gas-pump"></i> ' . htmlspecialchars($car['fuel_type']) . '</span>';
                        echo '<span class="car-feature"><i class="fas fa-cog"></i> ' . htmlspecialchars($car['transmission']) . '</span>';
                        echo '</div>';
                        echo '<a href="car-details.php?id=' . $car['id'] . '" class="view-details">View Details</a>';
                        echo '</div></div>';
                    }
                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                }
                ?>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?>
