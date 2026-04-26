<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get parameters
$userId = $_GET['user_id'] ?? '';
$newRole = $_GET['role'] ?? '';

if (empty($userId) || empty($newRole)) {
    $_SESSION['error'] = "Invalid parameters";
    header('Location: check_roles.php');
    exit;
}

// Validate role
$validRoles = ['admin', 'driver', 'student'];
if (!in_array($newRole, $validRoles)) {
    $_SESSION['error'] = "Invalid role specified";
    header('Location: check_roles.php');
    exit;
}

// Update user role
$database = new Database();
$db = $database->getConnection();

$query = "UPDATE users SET role = ? WHERE id = ?";
$stmt = $db->prepare($query);
$success = $stmt->execute([$newRole, $userId]);

if ($success) {
    $_SESSION['success'] = "User role updated to: " . ucfirst($newRole);
} else {
    $_SESSION['error'] = "Failed to update user role";
}

header('Location: check_roles.php');
exit;
?>
