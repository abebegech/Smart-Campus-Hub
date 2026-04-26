<?php
// Role-Based Access Control (RBAC) Middleware
class RBAC {
    private static $current_user = null;
    
    public static function checkRole($required_role) {
        self::loadUser();
        
        if (!self::$current_user) {
            self::redirect('login.php');
        }
        
        if (self::$current_user['role'] !== $required_role) {
            self::denyAccess();
        }
    }
    
    public static function requireRole($role) {
        self::checkRole($role);
    }
    
    public static function requireAnyRole($roles) {
        self::loadUser();
        
        if (!self::$current_user) {
            self::redirect('login.php');
        }
        
        if (!in_array(self::$current_user['role'], $roles)) {
            self::denyAccess();
        }
    }
    
    public static function requireAuth() {
        self::loadUser();
        
        if (!self::$current_user) {
            self::redirect('login.php');
        }
    }
    
    public static function canAccessOwnData($user_id) {
        self::loadUser();
        
        if (!self::$current_user) {
            return false;
        }
        
        // Admins can access all data
        if (self::$current_user['role'] === 'admin') {
            return true;
        }
        
        // Users can only access their own data
        return self::$current_user['id'] == $user_id;
    }
    
    public static function getCurrentUser() {
        self::loadUser();
        return self::$current_user;
    }
    
    public static function getCurrentUserId() {
        self::loadUser();
        return self::$current_user ? self::$current_user['id'] : null;
    }
    
    public static function getCurrentRole() {
        self::loadUser();
        return self::$current_user ? self::$current_user['role'] : null;
    }
    
    private static function loadUser() {
        if (self::$current_user === null) {
            if (isset($_SESSION['user_id'])) {
                require_once 'config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                $query = "SELECT id, first_name, last_name, email, role FROM users WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_SESSION['user_id']]);
                self::$current_user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                self::$current_user = false;
            }
        }
    }
    
    private static function denyAccess() {
        http_response_code(403);
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Access Denied - Smart Campus Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .error-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 24px;
            color: #dc3545;
            margin-bottom: 10px;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #34495e;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">Access Denied</div>
        <h1 class="error-title">Access Denied</h1>
        <p class="error-message">You do not have permission to access this page. Please contact your administrator if you believe this is an error.</p>
        <a href="index.php" class="btn">Go to Dashboard</a>
        <a href="login.php" class="btn">Login</a>
    </div>
</body>
</html>';
        exit;
    }
    
    private static function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Helper functions for specific role checks
    public static function isStudent() {
        return self::getCurrentRole() === 'student';
    }
    
    public static function isDriver() {
        return self::getCurrentRole() === 'driver';
    }
    
    public static function isAdmin() {
        return self::getCurrentRole() === 'admin';
    }
}
?>
