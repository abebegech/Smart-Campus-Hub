<?php
session_start();
require_once 'config/database.php';
require_once 'middleware/role_check.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Require admin role
requireRole('admin');

$database = new Database();
$db = $database->getConnection();

// Get system statistics
$totalBookings = $database->count('bookings');
$totalUsers = $database->count('users');
$totalVehicles = $database->count('vehicles');
$totalDrivers = $database->count('drivers');

// Get recent activity
$recentBookingsQuery = "SELECT b.*, u.first_name, u.last_name, u.email as passenger_email,
                 r.name as route_name, r.code as route_code,
                 v.make as vehicle_make, v.model as vehicle_model,
                 du.first_name as driver_first_name, du.last_name as driver_last_name
                 FROM bookings b 
                 LEFT JOIN users u ON b.passenger_id = u.id 
                 LEFT JOIN routes r ON b.route_id = r.id 
                 LEFT JOIN vehicles v ON b.vehicle_id = v.id 
                 LEFT JOIN drivers d ON b.driver_id = d.id 
                 LEFT JOIN users du ON d.user_id = du.id 
                 ORDER BY b.created_at DESC LIMIT 10";

$stmt = $db->query($recentBookingsQuery);
$recentBookings = $stmt->fetchAll();

// Get performance metrics
$completedToday = $database->count('bookings', 'status = "completed" AND DATE(scheduled_date) = CURDATE()');
$pendingBookings = $database->count('bookings', 'status IN ("pending", "confirmed")');
$activeDrivers = $database->count('drivers', 'is_available = 1');
$activeVehicles = $database->count('vehicles', 'status = "active"');

// Revenue calculation
$revenueQuery = "SELECT SUM(total_fare) as total_revenue FROM bookings WHERE status = 'completed' AND DATE(scheduled_date) = CURDATE()";
$stmt = $db->query($revenueQuery);
$revenue = $stmt->fetch()['total_revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Transport Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* 3D Animated Background */
        .admin-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: -2;
        }

        .admin-3d-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape-3d {
            position: absolute;
            transform-style: preserve-3d;
            animation: rotate3d 20s infinite linear;
        }

        .shape-3d.cube {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            animation-delay: 0s;
        }

        .shape-3d.pyramid {
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-bottom: 100px solid rgba(255, 255, 255, 0.1);
            top: 60%;
            right: 15%;
            animation-delay: 5s;
        }

        .shape-3d.sphere {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
            border-radius: 50%;
            bottom: 20%;
            left: 20%;
            animation-delay: 10s;
        }

        @keyframes rotate3d {
            0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
            100% { transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(10px) translateX(-10px); }
            75% { transform: translateY(-10px) translateX(5px); }
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            animation: logoGlow 3s ease-in-out infinite alternate;
        }

        @keyframes logoGlow {
            from { text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
            to { text-shadow: 0 2px 4px rgba(0,0,0,0.3), 0 0 20px rgba(255,255,255,0.5); }
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-menu a:hover, .nav-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
            position: relative;
            z-index: 10;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            margin-bottom: 3rem;
            text-align: center;
            transform: perspective(1000px) rotateX(2deg);
            animation: welcomeFloat 6s ease-in-out infinite;
        }

        @keyframes welcomeFloat {
            0%, 100% { transform: perspective(1000px) rotateX(2deg) translateY(0); }
            50% { transform: perspective(1000px) rotateX(2deg) translateY(-10px); }
        }

        .welcome-section h1 {
            color: white;
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
            animation: titlePulse 4s ease-in-out infinite;
        }

        @keyframes titlePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .welcome-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            transform: perspective(1000px) rotateY(0deg);
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .stat-card:hover {
            transform: perspective(1000px) rotateY(10deg) translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }

        .stat-value {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: valueGlow 2s ease-in-out infinite alternate;
        }

        @keyframes valueGlow {
            from { filter: drop-shadow(0 0 10px rgba(255,255,255,0.5)); }
            to { filter: drop-shadow(0 0 20px rgba(255,255,255,0.8)); }
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 2px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            margin-bottom: 3rem;
            transform: perspective(1000px) rotateX(1deg);
            transition: all 0.5s ease;
        }

        .card:hover {
            transform: perspective(1000px) rotateX(1deg) translateY(-5px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }

        .card h2 {
            color: white;
            margin-bottom: 2rem;
            font-size: 2rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table tr {
            transition: all 0.3s ease;
        }

        .table tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.02);
        }

        .table td {
            color: rgba(255, 255, 255, 0.9);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-success { background: rgba(39, 174, 96, 0.2); color: #27ae60; border: 1px solid rgba(39, 174, 96, 0.3); }
        .badge-warning { background: rgba(243, 156, 18, 0.2); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.3); }
        .badge-info { background: rgba(52, 152, 219, 0.2); color: #3498db; border: 1px solid rgba(52, 152, 219, 0.3); }
        .badge-danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); }

        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
        }

        .btn-secondary:hover {
            box-shadow: 0 8px 25px rgba(149, 165, 166, 0.4);
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .welcome-section h1 {
                font-size: 2rem;
            }

            .actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- 3D Animated Background -->
    <div class="admin-bg"></div>
    <div class="admin-3d-shapes">
        <div class="shape-3d cube"></div>
        <div class="shape-3d pyramid"></div>
        <div class="shape-3d sphere"></div>
    </div>
    <div class="floating-particles" id="particles"></div>

    <!-- Professional Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">🚀 Admin Dashboard</div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="drivers.php">Drivers</a></li>
                    <li><a href="vehicles.php">Vehicles</a></li>
                    <li><a href="routes.php">Routes</a></li>
                    <li><a href="bookings.php">Bookings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Admin Control Center</h1>
            <p>Complete System Management with Advanced Analytics</p>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalBookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $totalVehicles; ?></div>
                <div class="stat-label">Total Vehicles</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $totalDrivers; ?></div>
                <div class="stat-label">Total Drivers</div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <h2>Recent System Activity</h2>
            <?php if ($recentBookings && count($recentBookings) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Passenger</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?>
                                    <br><small><?php echo htmlspecialchars($booking['passenger_email']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($booking['route_name']); ?>
                                    <br><small><?php echo htmlspecialchars($booking['route_code']); ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo getBookingStatusClass($booking['status']); ?>">
                                        <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($booking['status']))); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($booking['scheduled_date'])); ?>
                                    <br><small><?php echo date('H:i', strtotime($booking['scheduled_time'])); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: white; text-align: center;">No recent activity found.</p>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h2>System Management</h2>
            <div class="actions">
                <a href="bookings.php" class="btn">Manage Bookings</a>
                <a href="drivers.php" class="btn btn-secondary">Manage Drivers</a>
                <a href="vehicles.php" class="btn btn-secondary">Manage Vehicles</a>
                <a href="routes.php" class="btn btn-secondary">Manage Routes</a>
            </div>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 4 + 2 + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (15 + Math.random() * 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            
            // Add entrance animations
            const elements = document.querySelectorAll('.stat-card, .card');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'perspective(1000px) rotateY(90deg)';
                setTimeout(() => {
                    el.style.transition = 'all 0.8s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'perspective(1000px) rotateY(0deg)';
                }, 100 * index);
            });
        });
    </script>

    <?php
    function getBookingStatusClass($status) {
        $statusClasses = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'driver_assigned' => 'info',
            'in_progress' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'secondary'
        ];
        return $statusClasses[$status] ?? 'info';
    }
    ?>
</body>
</html>
