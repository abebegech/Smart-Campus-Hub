<?php
// Main Authentication Gate - Smart Campus Hub
// All visitors must sign up or sign in before accessing the system

session_start();
require_once 'login_credentials.php';

// If user is already logged in, redirect to main dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Authenticate user
    $user = authenticateUser($email, $password);
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = uniqid();
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['permissions'] = $user['permissions'];
        $_SESSION['login_time'] = time();
        
        // Redirect to main dashboard
        header('Location: main.php');
        exit;
    } else {
        $loginError = 'Invalid email or password';
    }
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $role = $_POST['role'] ?? 'user';
    
    $signupErrors = [];
    
    // Validation
    if (empty($name)) {
        $signupErrors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $signupErrors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signupErrors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $signupErrors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $signupErrors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $signupErrors[] = 'Passwords do not match';
    }
    
    // Check if email already exists
    if (getUserByEmail($email)) {
        $signupErrors[] = 'Email already registered';
    }
    
    if (empty($signupErrors)) {
        // Create new user
        $newUser = [
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'name' => $name,
            'permissions' => getPermissionsByRole($role),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Auto-login after successful signup
        $_SESSION['user_id'] = uniqid();
        $_SESSION['email'] = $newUser['email'];
        $_SESSION['name'] = $newUser['name'];
        $_SESSION['role'] = $newUser['role'];
        $_SESSION['permissions'] = $newUser['permissions'];
        $_SESSION['login_time'] = time();
        $_SESSION['is_new_user'] = true;
        
        // Redirect to main dashboard
        header('Location: main.php');
        exit;
    }
}

function getPermissionsByRole($role) {
    $permissions = [
        'admin' => ['dashboard', 'permits', 'tracking', 'profile', 'settings', 'users'],
        'user' => ['dashboard', 'permits', 'profile'],
        'driver' => ['dashboard', 'tracking', 'profile']
    ];
    
    return $permissions[$role] ?? ['dashboard', 'profile'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Smart Campus Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-field {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .input-field:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .tab-button {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .tab-button.active {
            background: rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
        .role-option {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .role-option:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #667eea;
        }
        .role-option.selected {
            background: rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen flex items-center justify-center">
    <div class="absolute inset-0 bg-black/20"></div>
    
    <div class="relative z-10 w-full max-w-4xl mx-4">
        <!-- Welcome Header -->
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="bus" class="w-12 h-12 text-white"></i>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4">Smart Campus Hub</h1>
            <p class="text-xl text-gray-300 mb-2">Transport Management System</p>
            <p class="text-gray-400">Sign in or create an account to get started</p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex justify-center mb-6">
            <div class="glass-morphism rounded-full p-1 flex">
                <button onclick="showTab('signin')" id="signin-tab" class="tab-button active px-6 py-3 rounded-full text-white font-medium transition-all">
                    <i data-lucide="log-in" class="w-4 h-4 inline mr-2"></i>
                    Sign In
                </button>
                <button onclick="showTab('signup')" id="signup-tab" class="tab-button px-6 py-3 rounded-full text-white font-medium transition-all">
                    <i data-lucide="user-plus" class="w-4 h-4 inline mr-2"></i>
                    Sign Up
                </button>
            </div>
        </div>

        <!-- Sign In Form -->
        <div id="signin-form" class="glass-morphism rounded-2xl p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Welcome Back</h2>
            
            <?php if (isset($loginError)): ?>
            <div class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
                <div class="flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span><?php echo $loginError; ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="login" value="1">
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" 
                               name="email" 
                               required 
                               placeholder="Enter your email"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="Enter your password"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    <i data-lucide="log-in" class="w-4 h-4 inline mr-2"></i>
                    Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-300 text-sm">
                    Don't have an account? 
                    <button onclick="showTab('signup')" class="text-primary hover:text-primary/80 transition-colors">
                        Create one
                    </button>
                </p>
            </div>
        </div>

        <!-- Sign Up Form -->
        <div id="signup-form" class="glass-morphism rounded-2xl p-8 shadow-2xl hidden">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Create Account</h2>
            
            <?php if (!empty($signupErrors)): ?>
            <div class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
                <div class="space-y-1">
                    <?php foreach ($signupErrors as $error): ?>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="signup" value="1">
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               name="name" 
                               required 
                               placeholder="Enter your full name"
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" 
                               name="email" 
                               required 
                               placeholder="Enter your email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="Create a password (min 6 chars)"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" 
                               name="confirm_password" 
                               required 
                               placeholder="Confirm your password"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Select Your Role</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="role-option rounded-lg p-3 text-center cursor-pointer">
                            <input type="radio" name="role" value="admin" class="hidden" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'checked' : ''; ?>>
                            <i data-lucide="shield" class="w-6 h-6 mx-auto mb-1 text-red-400"></i>
                            <span class="text-xs text-white">Admin</span>
                        </label>
                        <label class="role-option rounded-lg p-3 text-center cursor-pointer">
                            <input type="radio" name="role" value="user" class="hidden" <?php echo ($_POST['role'] ?? 'user') === 'user' ? 'checked' : ''; ?>>
                            <i data-lucide="user" class="w-6 h-6 mx-auto mb-1 text-blue-400"></i>
                            <span class="text-xs text-white">User</span>
                        </label>
                        <label class="role-option rounded-lg p-3 text-center cursor-pointer">
                            <input type="radio" name="role" value="driver" class="hidden" <?php echo ($_POST['role'] ?? '') === 'driver' ? 'checked' : ''; ?>>
                            <i data-lucide="bus" class="w-6 h-6 mx-auto mb-1 text-green-400"></i>
                            <span class="text-xs text-white">Driver</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    <i data-lucide="user-plus" class="w-4 h-4 inline mr-2"></i>
                    Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-300 text-sm">
                    Already have an account? 
                    <button onclick="showTab('signin')" class="text-primary hover:text-primary/80 transition-colors">
                        Sign in
                    </button>
                </p>
            </div>
        </div>

        <!-- Demo Accounts -->
        <div class="mt-6 glass-morphism rounded-xl p-6">
            <h3 class="text-white font-semibold mb-4 text-center">Demo Accounts</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="text-center">
                    <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i data-lucide="shield" class="w-6 h-6 text-red-400"></i>
                    </div>
                    <p class="text-white font-semibold">Admin</p>
                    <p class="text-gray-400">admin@campus.com</p>
                    <p class="text-gray-400">admin123</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i data-lucide="user" class="w-6 h-6 text-blue-400"></i>
                    </div>
                    <p class="text-white font-semibold">User</p>
                    <p class="text-gray-400">user@campus.com</p>
                    <p class="text-gray-400">user123</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i data-lucide="bus" class="w-6 h-6 text-green-400"></i>
                    </div>
                    <p class="text-white font-semibold">Driver</p>
                    <p class="text-gray-400">driver@campus.com</p>
                    <p class="text-gray-400">driver123</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
        
        function showTab(tab) {
            // Hide all forms
            document.getElementById('signin-form').classList.add('hidden');
            document.getElementById('signup-form').classList.add('hidden');
            
            // Remove active class from all tabs
            document.getElementById('signin-tab').classList.remove('active');
            document.getElementById('signup-tab').classList.remove('active');
            
            // Show selected form and activate tab
            if (tab === 'signin') {
                document.getElementById('signin-form').classList.remove('hidden');
                document.getElementById('signin-tab').classList.add('active');
            } else {
                document.getElementById('signup-form').classList.remove('hidden');
                document.getElementById('signup-tab').classList.add('active');
            }
        }
        
        // Role selection styling
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.role-option').forEach(option => {
                    option.classList.remove('selected');
                });
                this.closest('.role-option').classList.add('selected');
            });
        });
        
        // Set initial selected state
        const selectedRole = document.querySelector('input[name="role"]:checked');
        if (selectedRole) {
            selectedRole.closest('.role-option').classList.add('selected');
        }
        
        // Add some visual effects
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.glass-morphism');
            forms.forEach((form, index) => {
                form.style.opacity = '0';
                form.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    form.style.transition = 'all 0.5s ease';
                    form.style.opacity = '1';
                    form.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });
        });
    </script>
</body>
</html>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">Here's your transport management overview</p>
        </div>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-value" id="total-bookings"><?php echo $totalBookings; ?></div>
                <div class="stat-label">Total Bookings</div>
                <div style="margin-top: 1rem;">
                    <span style="color: #28a745; font-size: 0.9rem;">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value" id="active-drivers"><?php echo $activeDrivers; ?></div>
                <div class="stat-label">Active Drivers</div>
                <div style="margin-top: 1rem;">
                    <span style="color: #28a745; font-size: 0.9rem;">
                        <i class="fas fa-arrow-up"></i> 8% from last week
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-value" id="active-vehicles"><?php echo $activeVehicles; ?></div>
                <div class="stat-label">Active Vehicles</div>
                <div style="margin-top: 1rem;">
                    <span style="color: #dc3545; font-size: 0.9rem;">
                        <i class="fas fa-arrow-down"></i> 3% from last week
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div class="stat-value" id="total-routes"><?php echo $totalRoutes; ?></div>
                <div class="stat-label">Total Routes</div>
                <div style="margin-top: 1rem;">
                    <span style="color: #28a745; font-size: 0.9rem;">
                        <i class="fas fa-arrow-up"></i> 5% from last month
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- 3D Globe -->
            <div class="globe-container">
                <div class="card-header">
                    <h3 class="card-title">Global Tracking View</h3>
                </div>
                <div id="globe-container"></div>
            </div>

            <!-- Real-time Tracking -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Real-time Tracking</h3>
                    <span class="badge badge-success">Live</span>
                </div>
                <div id="real-time-tracking"></div>
            </div>
        </div>

        <!-- Secondary Content Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                    <a href="bookings.php" class="btn btn-secondary">View All</a>
                </div>
                <div id="recent-activity">
                    <?php if ($recentBookings && count($recentBookings) > 0): ?>
                        <?php foreach ($recentBookings as $index => $booking): ?>
                            <div class="activity-item slide-in-left" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                                <div class="activity-icon activity-booking">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-message">
                                        New booking #<?php echo htmlspecialchars($booking['booking_reference']); ?> created
                                    </div>
                                    <div class="activity-time">
                                        <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 2rem;">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performance Metrics</h3>
                </div>
                <div id="performance-chart"></div>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Active Bookings</h3>
                <a href="bookings.php" class="btn btn-secondary">View All</a>
            </div>
            <div id="active-bookings">
                <?php if ($activeBookings && count($activeBookings) > 0): ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Passenger</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Scheduled</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong>
                                        </td>
                                        <td>
                                            <?php 
                                            // Get passenger name
                                            $passengerQuery = "SELECT first_name, last_name FROM users WHERE id = ?";
                                            $passengerStmt = $db->prepare($passengerQuery);
                                            $passengerStmt->execute([$booking['passenger_id']]);
                                            $passenger = $passengerStmt->fetch();
                                            echo htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            // Get route name
                                            $routeQuery = "SELECT name FROM routes WHERE id = ?";
                                            $routeStmt = $db->prepare($routeQuery);
                                            $routeStmt->execute([$booking['route_id']]);
                                            $route = $routeStmt->fetch();
                                            echo htmlspecialchars($route['name']);
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo getBadgeClass($booking['status']); ?>">
                                                <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($booking['status']))); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('M d, H:i', strtotime($booking['scheduled_date'] . ' ' . $booking['scheduled_time'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">No active bookings</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Active Drivers -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Available Drivers</h3>
                <a href="drivers.php" class="btn btn-secondary">View All</a>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Contact</th>
                            <th>License</th>
                            <th>Experience</th>
                            <th>Rating</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($activeDriversData && count($activeDriversData) > 0): ?>
                            <?php foreach ($activeDriversData as $driver): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; margin-right: 1rem;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']); ?></strong>
                                                <div style="font-size: 0.875rem; color: #666;">
                                                    <?php echo htmlspecialchars($driver['email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        // Get user phone from users table
                                        $userPhoneQuery = "SELECT phone FROM users WHERE id = ?";
                                        $userPhoneStmt = $db->prepare($userPhoneQuery);
                                        $userPhoneStmt->execute([$driver['user_id']]);
                                        $userPhone = $userPhoneStmt->fetch();
                                        echo htmlspecialchars($userPhone['phone'] ?? 'Not provided');
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($driver['license_number']); ?></td>
                                    <td><?php echo $driver['experience']; ?> years</td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <span style="color: #ffc107; margin-right: 0.5rem;">
                                                <i class="fas fa-star"></i>
                                            </span>
                                            <?php echo number_format($driver['rating'], 1); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($driver['registration_number']): ?>
                                            <?php echo htmlspecialchars($driver['make'] . ' ' . $driver['model'] . ' (' . $driver['registration_number'] . ')'); ?>
                                        <?php else: ?>
                                            <span style="color: #666;">No vehicle assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Available</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #666; padding: 2rem;">
                                    No available drivers
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="js/main.js"></script>
    
    <?php
    // Helper function to get badge class
    function getBadgeClass($status) {
        $statusClasses = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'driver_assigned' => 'primary',
            'in_progress' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];
        return $statusClasses[$status] ?? 'primary';
    }
    ?>

    <style>
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
        }
        
        .activity-item:hover {
            background: #f8f9fa;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
        }
        
        .activity-booking {
            background: linear-gradient(45deg, #667eea, #764ba2);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-message {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .activity-time {
            font-size: 0.875rem;
            color: #666;
        }
    </style>
</body>
</html>
