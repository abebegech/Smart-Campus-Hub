<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: working_login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    $database = new Database();
    $db = $database->getConnection();
    
    switch ($action) {
        case 'add_route':
            // Add new route
            $routeData = [
                'name' => $_POST['name'],
                'code' => $_POST['code'],
                'start_location_name' => $_POST['start_location_name'],
                'start_latitude' => $_POST['start_latitude'],
                'start_longitude' => $_POST['start_longitude'],
                'start_address' => $_POST['start_address'],
                'end_location_name' => $_POST['end_location_name'],
                'end_latitude' => $_POST['end_latitude'],
                'end_longitude' => $_POST['end_longitude'],
                'end_address' => $_POST['end_address'],
                'distance' => $_POST['distance'],
                'estimated_duration' => $_POST['estimated_duration'],
                'fare' => $_POST['fare'],
                'route_type' => $_POST['route_type']
            ];
            
            $database->insert('routes', $routeData);
            $success = "Route added successfully!";
            break;
            
        case 'update_status':
            // Update route status
            $routeId = $_POST['route_id'];
            $isActive = $_POST['is_active'];
            
            $database->update('routes', 
                ['is_active' => $isActive], 
                'id = ?', 
                [$routeId]
            );
            $success = "Route status updated!";
            break;
    }
}

// Get all routes
$database = new Database();
$db = $database->getConnection();

$routesQuery = "SELECT * FROM routes ORDER BY created_at DESC";
$routes = $database->query($routesQuery)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes Management - Transport Tracker</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header Navigation -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-route"></i> Transport Tracker
            </div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="drivers.php"><i class="fas fa-users"></i> Drivers</a></li>
                    <li><a href="vehicles.php"><i class="fas fa-car"></i> Vehicles</a></li>
                    <li><a href="routes.php" class="active"><i class="fas fa-map-marked-alt"></i> Routes</a></li>
                    <li><a href="bookings.php"><i class="fas fa-clipboard-list"></i> Bookings</a></li>
                    <li><a href="#" onclick="handleLogout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="dashboard-container">
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: white; font-size: 2rem; margin-bottom: 0.5rem;">Routes Management</h1>
                <p style="color: rgba(255,255,255,0.8);">Manage your transport routes</p>
            </div>
            <button class="btn btn-primary" onclick="showAddRouteModal()">
                <i class="fas fa-plus"></i> Add Route
            </button>
        </div>

        <!-- Alert Container -->
        <div id="alert-container">
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Search and Filter -->
        <div class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div style="flex: 1;">
                    <input type="text" id="search-routes" class="form-input" placeholder="Search routes by name or code...">
                </div>
                <select id="filter-type" class="form-select" style="width: 200px;">
                    <option value="">All Types</option>
                    <option value="regular">Regular</option>
                    <option value="express">Express</option>
                    <option value="special">Special</option>
                </select>
                <select id="filter-status" class="form-select" style="width: 200px;">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <button class="btn btn-secondary" onclick="searchRoutes()">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>

        <!-- Routes Grid -->
        <div id="routes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
            <?php if ($routes && count($routes) > 0): ?>
                <?php foreach ($routes as $route): ?>
                    <div class="card route-card" data-route-id="<?php echo $route['id']; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.2rem;">
                                    <?php echo htmlspecialchars($route['name']); ?>
                                </h3>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                    Code: <?php echo htmlspecialchars($route['code']); ?>
                                </p>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <span class="badge badge-primary">
                                    <?php echo htmlspecialchars(ucfirst($route['route_type'])); ?>
                                </span>
                                <span class="badge badge-<?php echo $route['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $route['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <!-- Route Path -->
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px;">
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                                    <i class="fas fa-map-marker-alt" style="color: #28a745; margin-right: 0.5rem;"></i>
                                    <div>
                                        <strong><?php echo htmlspecialchars($route['start_location_name']); ?></strong>
                                        <div style="font-size: 0.8rem; color: #666;">
                                            <?php echo htmlspecialchars($route['start_address']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="text-align: center; margin: 0.5rem 0;">
                                    <i class="fas fa-arrow-down" style="color: #666;"></i>
                                </div>
                                
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-map-marker-alt" style="color: #dc3545; margin-right: 0.5rem;"></i>
                                    <div>
                                        <strong><?php echo htmlspecialchars($route['end_location_name']); ?></strong>
                                        <div style="font-size: 0.8rem; color: #666;">
                                            <?php echo htmlspecialchars($route['end_address']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Route Details -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-road" style="width: 20px; color: #666; margin-right: 0.5rem;"></i>
                                    <span><?php echo number_format($route['distance'], 1); ?> km</span>
                                </div>
                                
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-clock" style="width: 20px; color: #666; margin-right: 0.5rem;"></i>
                                    <span><?php echo $route['estimated_duration']; ?> min</span>
                                </div>
                                
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-dollar-sign" style="width: 20px; color: #666; margin-right: 0.5rem;"></i>
                                    <span>$<?php echo number_format($route['fare'], 2); ?></span>
                                </div>
                                
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-route" style="width: 20px; color: #666; margin-right: 0.5rem;"></i>
                                    <span><?php echo htmlspecialchars(ucfirst($route['route_type'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button class="btn btn-secondary" style="flex: 1;" onclick="toggleRouteStatus(<?php echo $route['id']; ?>, <?php echo $route['is_active'] ? 0 : 1; ?>)">
                                <i class="fas fa-<?php echo $route['is_active'] ? 'pause' : 'play'; ?>"></i>
                                <?php echo $route['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                            <button class="btn btn-secondary" onclick="viewRouteDetails(<?php echo $route['id']; ?>)">
                                <i class="fas fa-info-circle"></i> Details
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: #666; margin-bottom: 1rem;"></i>
                    <h3 style="color: #666;">No routes found</h3>
                    <p style="color: #999;">Start by adding your first route</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Add Route Modal -->
    <div id="add-route-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Route</h2>
                <button class="modal-close" onclick="closeAddRouteModal()">&times;</button>
            </div>
            <form method="POST" action="routes.php">
                <input type="hidden" name="action" value="add_route">
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Route Name</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Route Code</label>
                        <input type="text" name="code" class="form-input" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Location Name</label>
                        <input type="text" name="start_location_name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Location Name</label>
                        <input type="text" name="end_location_name" class="form-input" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Address</label>
                        <input type="text" name="start_address" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Address</label>
                        <input type="text" name="end_address" class="form-input">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Latitude</label>
                        <input type="number" name="start_latitude" class="form-input" step="0.000001" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Start Longitude</label>
                        <input type="number" name="start_longitude" class="form-input" step="0.000001" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">End Latitude</label>
                        <input type="number" name="end_latitude" class="form-input" step="0.000001" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Longitude</label>
                        <input type="number" name="end_longitude" class="form-input" step="0.000001" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Distance (km)</label>
                        <input type="number" name="distance" class="form-input" step="0.1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estimated Duration (minutes)</label>
                        <input type="number" name="estimated_duration" class="form-input" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Fare ($)</label>
                        <input type="number" name="fare" class="form-input" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Route Type</label>
                        <select name="route_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="regular">Regular</option>
                            <option value="express">Express</option>
                            <option value="special">Special</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddRouteModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Route</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>

    <style>
        .route-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            animation: fadeInUp 0.3s ease-out;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            color: #333;
        }
    </style>

    <script>
        function showAddRouteModal() {
            document.getElementById('add-route-modal').style.display = 'flex';
        }
        
        function closeAddRouteModal() {
            document.getElementById('add-route-modal').style.display = 'none';
        }
        
        function toggleRouteStatus(routeId, isActive) {
            if (confirm('Are you sure you want to change route status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'routes.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'update_status';
                
                const routeIdInput = document.createElement('input');
                routeIdInput.type = 'hidden';
                routeIdInput.name = 'route_id';
                routeIdInput.value = routeId;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'is_active';
                statusInput.value = isActive;
                
                form.appendChild(actionInput);
                form.appendChild(routeIdInput);
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function viewRouteDetails(routeId) {
            // Implement route details view
            alert('Route details view coming soon!');
        }
        
        function searchRoutes() {
            const searchTerm = document.getElementById('search-routes').value.toLowerCase();
            const typeFilter = document.getElementById('filter-type').value;
            const statusFilter = document.getElementById('filter-status').value;
            
            const routeCards = document.querySelectorAll('.route-card');
            
            routeCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const matchesSearch = searchTerm === '' || text.includes(searchTerm);
                
                let matchesType = true;
                if (typeFilter !== '') {
                    matchesType = text.includes(typeFilter.toLowerCase());
                }
                
                let matchesStatus = true;
                if (statusFilter !== '') {
                    matchesStatus = text.includes(statusFilter === '1' ? 'active' : 'inactive');
                }
                
                card.style.display = matchesSearch && matchesType && matchesStatus ? 'block' : 'none';
            });
        }
        
        // Add event listeners for search
        document.getElementById('search-routes').addEventListener('input', searchRoutes);
        document.getElementById('filter-type').addEventListener('change', searchRoutes);
        document.getElementById('filter-status').addEventListener('change', searchRoutes);
    </script>
</body>
</html>
