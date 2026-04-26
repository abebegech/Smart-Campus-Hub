<?php
// Don't start session here - it's already started in the calling file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user role
function getUserRole($userId) {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    return $user ? $user['role'] : 'student';
}

// Check if user has specific role
function hasRole($requiredRole) {
    $userRole = getUserRole($_SESSION['user_id']);
    return $userRole === $requiredRole;
}

// Check if user is admin
function isAdmin() {
    return hasRole('admin');
}

// Check if user is driver
function isDriver() {
    return hasRole('driver');
}

// Check if user is student
function isStudent() {
    return hasRole('student');
}

// Redirect if user doesn't have required role
function requireRole($requiredRole) {
    if (!hasRole($requiredRole)) {
        $_SESSION['error'] = "Access denied. You don't have permission to access this page.";
        header('Location: working_dashboard.php');
        exit;
    }
}

// Get current user's role
$currentRole = getUserRole($_SESSION['user_id']);
?>
