<?php
require_once 'auth.php';

// Check if user is admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Manage Vehicles";

// Sample data - replace with actual database queries
$vehicles = [
    [
        'id' => 1,
        'make' => 'Toyota',
        'model' => 'Camry',
        'year' => 2023,
        'price' => 24999.99,
        'status' => 'available',
        'image' => 'vehicle1.jpg',
        'created_at' => '2023-01-15'
    ],
    [
        'id' => 2,
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2023,
        'price' => 22999.99,
        'status' => 'sold',
        'image' => 'vehicle2.jpg',
        'created_at' => '2023-02-20'
    ],
    [
        'id' => 3,
        'make' => 'Ford',
        'model' => 'Mustang',
        'year' => 2023,
        'price' => 42999.99,
        'status' => 'available',
        'image' => 'vehicle3.jpg',
        'created_at' => '2023-03-10'
    ]
];

// Handle vehicle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $vehicleId = (int)$_GET['delete'];
    // In a real application, you would delete the vehicle from the database here
    // deleteVehicle($vehicleId);
    
    // For demo, remove from the array
    $vehicles = array_filter($vehicles, function($vehicle) use ($vehicleId) {
        return $vehicle['id'] !== $vehicleId;
    });
    
    $_SESSION['success'] = 'Vehicle deleted successfully';
    header('Location: vehicles.php');
    exit();
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
        <a href="vehicle_add.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Vehicle
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Vehicles List</h6>
            <div class="d-flex">
                <input type="text" class="form-control form-control-sm me-2" placeholder="Search vehicles...">
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered data-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Make & Model</th>
                            <th>Year</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?php echo $vehicle['id']; ?></td>
                            <td>
                                <img src="../images/vehicles/<?php echo htmlspecialchars($vehicle['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?>"
                                     class="img-thumbnail" style="max-width: 80px;">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?></strong>
                            </td>
                            <td><?php echo $vehicle['year']; ?></td>
                            <td>$<?php echo number_format($vehicle['price'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $vehicle['status'] === 'available' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($vehicle['status'])); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="vehicle_view.php?id=<?php echo $vehicle['id']; ?>" 
                                   class="btn btn-sm btn-info" 
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="vehicle_edit.php?id=<?php echo $vehicle['id']; ?>" 
                                   class="btn btn-sm btn-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="vehicles.php?delete=<?php echo $vehicle['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this vehicle?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
