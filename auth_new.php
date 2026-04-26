<?php
// CENTRALIZED AUTHENTICATION SYSTEM
session_start();

// User database with roles and permissions
$USERS = [
    'admin@campus.com' => [
        'password' => 'admin123',
        'role' => 'admin',
        'name' => 'System Administrator',
        'permissions' => ['dashboard', 'tracking', 'permits', 'gps_broadcast', 'system', 'users', 'reports']
    ],
    'student@campus.com' => [
        'password' => 'student123',
        'role' => 'student',
        'name' => 'Student User',
        'permissions' => ['dashboard', 'tracking', 'permits']
    ],
    'driver@campus.com' => [
        'password' => 'driver123',
        'role' => 'driver',
        'name' => 'Driver User',
        'permissions' => ['dashboard', 'gps_broadcast']
    ]
];

// Check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Check if user has specific permission
function hasPermission($permission) {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = getUserRole();
    global $USERS;
    
    // Find user by email to get permissions
    foreach ($USERS as $email => $user) {
        if ($user['role'] === $userRole) {
            return in_array($permission, $user['permissions']);
        }
    }
    
    return false;
}

// Authenticate user
function authenticate($email, $password) {
    global $USERS;
    
    if (isset($USERS[$email]) && $USERS[$email]['password'] === $password) {
        $user = $USERS[$email];
        
        // Set session variables
        $_SESSION['user_id'] = uniqid();
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['permissions'] = $user['permissions'];
        $_SESSION['login_time'] = time();
        
        return true;
    }
    
    return false;
}

// Get current user
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'name' => $_SESSION['name'],
        'role' => $_SESSION['role'],
        'permissions' => $_SESSION['permissions'],
        'login_time' => $_SESSION['login_time']
    ];
}

// Logout user
function logout() {
    session_destroy();
}

// Redirect based on role
function redirectByRole() {
    if (!isAuthenticated()) {
        header('Location: login_new.php');
        exit;
    }
    
    $role = getUserRole();
    
    switch ($role) {
        case 'admin':
            header('Location: main_new.php?page=dashboard');
            break;
        case 'student':
            header('Location: main_new.php?page=dashboard');
            break;
        case 'driver':
            header('Location: main_new.php?page=dashboard');
            break;
        default:
            header('Location: login_new.php');
            break;
    }
    exit;
}

// Check authentication and redirect if not logged in
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login_new.php');
        exit;
    }
}

// Get role-based menu items
function getRoleBasedMenu() {
    $role = getUserRole();
    
    // Define all available menus
    $allMenus = [
        'dashboard' => [
            'icon' => 'layout-dashboard',
            'label' => 'Dashboard',
            'page' => 'dashboard',
            'permission' => 'dashboard'
        ],
        'tracking' => [
            'icon' => 'map-pin',
            'label' => 'Live Tracking',
            'page' => 'tracking',
            'permission' => 'tracking'
        ],
        'permits' => [
            'icon' => 'file-text',
            'label' => 'My Permits',
            'page' => 'permits',
            'permission' => 'permits'
        ],
        'gps_broadcast' => [
            'icon' => 'radio',
            'label' => 'GPS Broadcast',
            'page' => 'gps_broadcast',
            'permission' => 'gps_broadcast'
        ],
        'system' => [
            'icon' => 'settings',
            'label' => 'System Settings',
            'page' => 'system',
            'permission' => 'system'
        ],
        'users' => [
            'icon' => 'users',
            'label' => 'User Management',
            'page' => 'users',
            'permission' => 'users'
        ],
        'reports' => [
            'icon' => 'bar-chart',
            'label' => 'Reports',
            'page' => 'reports',
            'permission' => 'reports'
        ]
    ];
    
    // Role-based menu filtering
    $roleMenus = [];
    switch ($role) {
        case 'admin':
            // Admin gets everything except GPS Broadcast and Permits
            $adminExcluded = ['permits', 'gps_broadcast'];
            foreach ($allMenus as $key => $menu) {
                if (!in_array($key, $adminExcluded) && hasPermission($menu['permission'])) {
                    $roleMenus[$key] = $menu;
                }
            }
            break;
            
        case 'driver':
            // Driver gets Dashboard, GPS Broadcast, and Tracking only
            $driverAllowed = ['dashboard', 'gps_broadcast', 'tracking'];
            foreach ($allMenus as $key => $menu) {
                if (in_array($key, $driverAllowed) && hasPermission($menu['permission'])) {
                    $roleMenus[$key] = $menu;
                }
            }
            break;
            
        case 'student':
            // Student gets Dashboard, Tracking, and Permits only
            $studentAllowed = ['dashboard', 'tracking', 'permits'];
            foreach ($allMenus as $key => $menu) {
                if (in_array($key, $studentAllowed) && hasPermission($menu['permission'])) {
                    $roleMenus[$key] = $menu;
                }
            }
            break;
            
        default:
            // Default to basic permissions
            foreach ($allMenus as $key => $menu) {
                if (hasPermission($menu['permission'])) {
                    $roleMenus[$key] = $menu;
                }
            }
            break;
    }
    
    return $roleMenus;
}

// Get bus capacity data for sidebar
function getBusCapacityData() {
    // Sample bus capacity data with proper calculation
    return [
        ['id' => 'BUS-001', 'name' => 'Campus Shuttle A', 'capacity' => 50, 'current' => 35, 'status' => 'active'], // 70% - Yellow
        ['id' => 'BUS-002', 'name' => 'Engineering Route', 'capacity' => 50, 'current' => 48, 'status' => 'active'], // 96% - Red
        ['id' => 'BUS-003', 'name' => 'Library Express', 'capacity' => 50, 'current' => 20, 'status' => 'idle'], // 40% - Green
        ['id' => 'BUS-004', 'name' => 'Sports Complex', 'capacity' => 50, 'current' => 0, 'status' => 'maintenance'] // 0% - Green
    ];
}

// Get capacity color based on percentage
function getCapacityColor($current, $capacity) {
    if ($capacity == 0) return 'bg-green-500'; // Avoid division by zero
    $percentage = ($current / $capacity) * 100;
    if ($percentage < 70) return 'bg-green-500';
    if ($percentage <= 90) return 'bg-yellow-500';
    return 'bg-red-500';
}

// Get capacity percentage
function getCapacityPercentage($current, $capacity) {
    if ($capacity == 0) return 0; // Avoid division by zero
    return round(($current / $capacity) * 100);
}

?>
