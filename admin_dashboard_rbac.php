<?php
session_start();
require_once 'config/database.php';
require_once 'middleware/rbac.php';

// RBAC: Only admins can access this dashboard
RBAC::requireRole('admin');

$database = new Database();
$db = $database->getConnection();

// Handle permit approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'approve_permit' && isset($_POST['transaction_id'])) {
        $transactionId = $_POST['transaction_id'];
        
        // Update permit status to approved
        $updateQuery = "UPDATE transport_permits SET status = 'approved' WHERE transaction_id = ?";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([$transactionId]);
        
        // Trigger PDF generation
        $permitLink = 'generate_permit.php?txn=' . $transactionId;
        $updateQuery = "UPDATE transport_permits SET permit_file = ? WHERE transaction_id = ?";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([$permitLink, $transactionId]);
        
        $_SESSION['success'] = "Permit {$transactionId} approved and PDF generated!";
        header('Location: admin_dashboard_rbac.php');
        exit;
    }
    
    if ($_POST['action'] === 'reject_permit' && isset($_POST['transaction_id'])) {
        $transactionId = $_POST['transaction_id'];
        
        // Update permit status to rejected
        $updateQuery = "UPDATE transport_permits SET status = 'rejected' WHERE transaction_id = ?";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([$transactionId]);
        
        $_SESSION['success'] = "Permit {$transactionId} rejected!";
        header('Location: admin_dashboard_rbac.php');
        exit;
    }
}

// Get permits data
$permitsQuery = "SELECT tp.*, u.first_name, u.last_name, u.email 
                FROM transport_permits tp 
                JOIN users u ON tp.user_id = u.id 
                ORDER BY tp.created_at DESC";
$permitsStmt = $db->prepare($permitsQuery);
$permitsStmt->execute();
$permits = $permitsStmt->fetchAll();

// Get fleet statistics
$fleetQuery = "SELECT 
                COUNT(DISTINCT b.id) as total_buses,
                COUNT(DISTINCT CASE WHEN bl.timestamp >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN bl.bus_id END) as active_buses,
                COUNT(DISTINCT u.id) as total_drivers
              FROM buses b
              LEFT JOIN bus_locations bl ON b.id = bl.bus_id
              LEFT JOIN users u ON b.driver_id = u.id
              WHERE b.status = 'active'";
$fleetStmt = $db->prepare($fleetQuery);
$fleetStmt->execute();
$fleetStats = $fleetStmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Campus Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            font-weight: 600;
        }
        
        .card-header h3 {
            margin: 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffc107;
            color: #856404;
        }
        
        .status-approved {
            background: #28a745;
            color: white;
        }
        
        .status-rejected {
            background: #dc3545;
            color: white;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #2c3e50;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #856404;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .screenshot-preview {
            max-width: 100px;
            max-height: 60px;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .screenshot-preview:hover {
            transform: scale(1.5);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        
        .modal-content {
            margin: 5% auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            text-align: center;
        }
        
        .modal-content img {
            max-width: 100%;
            max-height: 500px;
            border-radius: 8px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i>
                <span>Administrator</span>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <!-- Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Overview</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo count($permits); ?></div>
                            <div class="stat-label">Total Permits</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $fleetStats['active_buses']; ?></div>
                            <div class="stat-label">Active Buses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">
                                <?php echo count(array_filter($permits, fn($p) => $p['status'] === 'pending')); ?>
                            </div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permit Management -->
            <div class="card" style="grid-column: span 2;">
                <div class="card-header">
                    <h3><i class="fas fa-id-card"></i> Permit Management</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permits as $permit): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($permit['transaction_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($permit['first_name'] . ' ' . $permit['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($permit['email']); ?></td>
                                <td><?php echo htmlspecialchars($permit['permit_type'] ?? 'Monthly Pass'); ?></td>
                                <td>
                                    <?php if ($permit['payment_screenshot']): ?>
                                        <img src="uploads/payments/<?php echo htmlspecialchars($permit['payment_screenshot']); ?>" 
                                             alt="Payment Screenshot" 
                                             class="screenshot-preview"
                                             onclick="openModal('uploads/payments/<?php echo htmlspecialchars($permit['payment_screenshot']); ?>')">
                                    <?php else: ?>
                                        <span style="color: #6c757d;">No file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $permit['status']; ?>">
                                        <?php echo $permit['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($permit['status'] === 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="approve_permit">
                                                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($permit['transaction_id']); ?>">
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this permit?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="reject_permit">
                                                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($permit['transaction_id']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Reject this permit?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        <?php elseif ($permit['status'] === 'approved'): ?>
                                            <a href="generate_permit.php?txn=<?php echo htmlspecialchars($permit['transaction_id']); ?>" 
                                               class="btn btn-primary btn-sm" target="_blank">
                                                <i class="fas fa-file-pdf"></i> View PDF
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-ban"></i> Rejected
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Fleet Management -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bus"></i> Fleet Management</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $fleetStats['total_buses']; ?></div>
                            <div class="stat-label">Total Buses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $fleetStats['active_buses']; ?></div>
                            <div class="stat-label">Active</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $fleetStats['total_drivers']; ?></div>
                            <div class="stat-label">Drivers</div>
                        </div>
                    </div>
                    
                    <div class="quick-actions">
                        <a href="manage_buses.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Manage Buses
                        </a>
                        <a href="manage_routes.php" class="btn btn-primary">
                            <i class="fas fa-route"></i> Manage Routes
                        </a>
                        <a href="manage_drivers.php" class="btn btn-primary">
                            <i class="fas fa-users"></i> Manage Drivers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal for screenshot preview -->
    <div id="screenshotModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Payment Screenshot</h3>
            <img id="modalImage" src="" alt="Payment Screenshot">
        </div>
    </div>
    
    <script>
        // Modal functionality
        function openModal(imageSrc) {
            document.getElementById('screenshotModal').style.display = 'block';
            document.getElementById('modalImage').src = imageSrc;
        }
        
        document.querySelector('.close').onclick = function() {
            document.getElementById('screenshotModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('screenshotModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
