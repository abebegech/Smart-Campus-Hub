<?php
// User Management API
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'transport_tracking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        createUser();
        break;
    case 'read':
        getUsers();
        break;
    case 'update':
        updateUser();
        break;
    case 'delete':
        deleteUser();
        break;
    case 'toggle_status':
        toggleUserStatus();
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

function createUser() {
    global $pdo;
    
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($name) || empty($role) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        return;
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (email, name, role, password, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
    ");
    
    if ($stmt->execute([$email, $name, $role, $hashedPassword])) {
        $userId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'User created successfully', 'user_id' => $userId]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create user']);
    }
}

function getUsers() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT user_id, email, name, role, status, created_at, last_login, profile_image 
        FROM users 
        ORDER BY created_at DESC
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $users]);
}

function updateUser() {
    global $pdo;
    
    $userId = $_POST['user_id'] ?? '';
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($userId) || empty($email) || empty($name) || empty($role)) {
        echo json_encode(['success' => false, 'error' => 'User ID, email, name, and role are required']);
        return;
    }
    
    // Check if email exists for another user
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        return;
    }
    
    // Update user
    if (!empty($password)) {
        // Update with new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            UPDATE users 
            SET email = ?, name = ?, role = ?, password = ?, updated_at = NOW() 
            WHERE user_id = ?
        ");
        $result = $stmt->execute([$email, $name, $role, $hashedPassword, $userId]);
    } else {
        // Update without password change
        $stmt = $pdo->prepare("
            UPDATE users 
            SET email = ?, name = ?, role = ?, updated_at = NOW() 
            WHERE user_id = ?
        ");
        $result = $stmt->execute([$email, $name, $role, $userId]);
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update user']);
    }
}

function deleteUser() {
    global $pdo;
    
    $userId = $_POST['user_id'] ?? '';
    
    if (empty($userId)) {
        echo json_encode(['success' => false, 'error' => 'User ID is required']);
        return;
    }
    
    // Check if user is admin (prevent deletion of admin users)
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['role'] === 'admin') {
        echo json_encode(['success' => false, 'error' => 'Cannot delete admin users']);
        return;
    }
    
    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    if ($stmt->execute([$userId])) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
    }
}

function toggleUserStatus() {
    global $pdo;
    
    $userId = $_POST['user_id'] ?? '';
    
    if (empty($userId)) {
        echo json_encode(['success' => false, 'error' => 'User ID is required']);
        return;
    }
    
    // Get current status
    $stmt = $pdo->prepare("SELECT status FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        return;
    }
    
    // Toggle status
    $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
    $stmt = $pdo->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE user_id = ?");
    
    if ($stmt->execute([$newStatus, $userId])) {
        echo json_encode(['success' => true, 'message' => "User status updated to {$newStatus}", 'new_status' => $newStatus]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update user status']);
    }
}
?>
