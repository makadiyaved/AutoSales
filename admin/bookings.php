<?php
require_once 'auth.php';

// Check if user is admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Manage Bookings";

// Sample data - replace with actual database queries
$bookings = [
    [
        'id' => 'BK1001',
        'customer_name' => 'John Doe',
        'vehicle' => 'Toyota Camry 2023',
        'booking_date' => '2023-06-15',
        'pickup_date' => '2023-06-20',
        'return_date' => '2023-06-25',
        'total_amount' => 750.00,
        'status' => 'confirmed',
        'created_at' => '2023-05-10 14:30:00'
    ],
    [
        'id' => 'BK1002',
        'customer_name' => 'Jane Smith',
        'vehicle' => 'Honda Civic 2023',
        'booking_date' => '2023-06-18',
        'pickup_date' => '2023-07-01',
        'return_date' => '2023-07-05',
        'total_amount' => 600.00,
        'status' => 'pending',
        'created_at' => '2023-05-15 10:15:00'
    ],
    [
        'id' => 'BK1003',
        'customer_name' => 'Robert Johnson',
        'vehicle' => 'Ford Mustang 2023',
        'booking_date' => '2023-05-20',
        'pickup_date' => '2023-06-10',
        'return_date' => '2023-06-15',
        'total_amount' => 1200.00,
        'status' => 'completed',
        'created_at' => '2023-05-05 16:45:00'
    ]
];

// Handle booking status update
if (isset($_POST['update_status']) && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $bookingId = $_POST['booking_id'];
    $newStatus = $_POST['status'];
    
    // In a real application, you would update the booking status in the database here
    // updateBookingStatus($bookingId, $newStatus);
    
    // For demo, update the status in the array
    foreach ($bookings as &$booking) {
        if ($booking['id'] === $bookingId) {
            $booking['status'] = $newStatus;
            break;
        }
    }
    
    $_SESSION['success'] = 'Booking status updated successfully';
    header('Location: bookings.php');
    exit();
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
        <div>
            <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm me-2">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </button>
            <a href="booking_add.php" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> New Booking
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Bookings</h6>
            <div class="d-flex">
                <input type="text" class="form-control form-control-sm me-2" placeholder="Search bookings...">
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="bookingsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Booking Date</th>
                            <th>Pickup Date</th>
                            <th>Return Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['vehicle']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></td>
                            <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($booking['status']) {
                                        'confirmed' => 'success',
                                        'pending' => 'warning',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="booking_view.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal" 
                                        data-booking-id="<?php echo $booking['id']; ?>" data-current-status="<?php echo $booking['status']; ?>"
                                        title="Update Status">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <a href="booking_edit.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="booking_invoice.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-secondary" title="Invoice">
                                    <i class="fas fa-file-invoice"></i>
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

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Booking Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" id="bookingId">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Page level plugins -->
<script>
// Initialize DataTable
$(document).ready(function() {
    $('#bookingsTable').DataTable({
        "order": [[0, "desc"]]
    });
    
    // Handle status modal
    var statusModal = document.getElementById('statusModal');
    statusModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var bookingId = button.getAttribute('data-booking-id');
        var currentStatus = button.getAttribute('data-current-status');
        
        var modalBookingId = statusModal.querySelector('#bookingId');
        var modalStatus = statusModal.querySelector('#status');
        
        modalBookingId.value = bookingId;
        modalStatus.value = currentStatus;
    });
});
</script>
