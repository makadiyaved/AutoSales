<?php
session_start();
require_once 'auth.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

try {
    // Get user's favorite cars
    $stmt = $pdo->prepare(
        "SELECT c.* FROM cars c 
        JOIN favorites f ON c.id = f.car_id 
        WHERE f.user_id = ? AND c.status = 'available'"
    );
    $stmt->execute([$_SESSION['user_id']]);
    $favoriteCars = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching favorite cars: " . $e->getMessage();
}

$pageTitle = "My Favorites - AutoSales";
include 'header.php';
?>

<main class="container">
    <h1>My Favorite Cars</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (empty($favoriteCars)): ?>
        <div class="empty-state">
            <i class="fas fa-heart" style="font-size: 4rem; color: #e74c3c; margin-bottom: 1rem;"></i>
            <h3>No favorite cars yet</h3>
            <p>Browse our inventory and add some cars to your favorites!</p>
            <a href="inventory.php" class="btn btn-primary">Browse Inventory</a>
        </div>
    <?php else: ?>
        <div class="favorites-grid">
            <?php foreach ($favoriteCars as $car): ?>
                <div class="car-card">
                    <div class="car-card-image">
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                        <button class="favorite-btn active" data-car-id="<?php echo $car['id']; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="car-card-body">
                        <h3><?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?></h3>
                        <div class="car-price">$<?php echo number_format($car['price']); ?></div>
                        <div class="car-details">
                            <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['mileage']); ?> mi</span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                            <span><i class="fas fa-cog"></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                        </div>
                        <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-block btn-primary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
// Handle favorite button click
document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const carId = this.getAttribute('data-car-id');
            const isFavorite = this.classList.contains('active');
            
            // Toggle UI immediately for better UX
            this.classList.toggle('active');
            
            // Send AJAX request to update favorites
            fetch('update_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'car_id=' + carId + '&action=' + (isFavorite ? 'remove' : 'add')
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert UI if there was an error
                    this.classList.toggle('active');
                    alert('Failed to update favorites: ' + (data.message || 'Unknown error'));
                } else if (data.removed) {
                    // Remove the card from the DOM if item was removed
                    this.closest('.car-card').remove();
                    
                    // Check if no favorites left
                    if (document.querySelectorAll('.car-card').length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.classList.toggle('active');
                alert('An error occurred. Please try again.');
            });
        });
    });
});
</script>

<style>
.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.car-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.car-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.car-card-image {
    position: relative;
    padding-top: 66.66%; /* 3:2 aspect ratio */
    overflow: hidden;
}

.car-card-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.favorite-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #ccc;
    transition: all 0.3s ease;
}

.favorite-btn:hover {
    background: white;
    color: #e74c3c;
}

.favorite-btn.active {
    color: #e74c3c;
}

.car-card-body {
    padding: 1.5rem;
}

.car-card-body h3 {
    margin: 0 0 0.5rem;
    font-size: 1.25rem;
    color: #333;
}

.car-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.car-details {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    color: #666;
    font-size: 0.9rem;
}

.car-details i {
    margin-right: 0.25rem;
    color: var(--accent-color);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #f9f9f9;
    border-radius: 10px;
    margin-top: 2rem;
}

.empty-state h3 {
    margin: 1rem 0 0.5rem;
    color: #333;
}

.empty-state p {
    color: #666;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .favorites-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'footer.php'; ?>
