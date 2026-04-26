<?php
/**
 * Transport Tracking System Setup Script
 * This script helps set up the database and create admin user
 */

echo "<h1>Transport Tracking System Setup</h1>";

// Step 1: Check database connection
echo "<h2>Step 1: Checking Database Connection</h2>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p style='color: green;'>Database connection: SUCCESS</p>";
    } else {
        echo "<p style='color: red;'>Database connection: FAILED</p>";
        echo "<p>Please check your database configuration in config/database.php</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure MySQL is running and the database exists.</p>";
    exit;
}

// Step 2: Check if users table exists
echo "<h2>Step 2: Checking Database Tables</h2>";

try {
    $query = "SHOW TABLES LIKE 'users'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color: green;'>Users table exists</p>";
        
        // Check if admin user exists
        $query = "SELECT * FROM users WHERE email = 'admin@transport.com'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $adminUser = $stmt->fetch();
        
        if ($adminUser) {
            echo "<p style='color: green;'>Admin user exists</p>";
            echo "<p>Email: admin@transport.com</p>";
            echo "<p>Password: admin123</p>";
        } else {
            echo "<p style='color: orange;'>Admin user not found. Creating admin user...</p>";
            
            // Create admin user
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password, role, first_name, last_name, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute(['admin', 'admin@transport.com', $hashedPassword, 'admin', 'Admin', 'User', 1]);
            
            if ($result) {
                echo "<p style='color: green;'>Admin user created successfully</p>";
                echo "<p>Email: admin@transport.com</p>";
                echo "<p>Password: admin123</p>";
            } else {
                echo "<p style='color: red;'>Failed to create admin user</p>";
            }
        }
        
        // Check if regular user exists
        $query = "SELECT * FROM users WHERE email = 'user@transport.com'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $regularUser = $stmt->fetch();
        
        if ($regularUser) {
            echo "<p style='color: green;'>Regular user exists</p>";
        } else {
            echo "<p style='color: orange;'>Regular user not found. Creating regular user...</p>";
            
            // Create regular user
            $hashedPassword = password_hash('user123', PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password, role, first_name, last_name, phone, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute(['johnuser', 'user@transport.com', $hashedPassword, 'user', 'John', 'Doe', '+1234567890', 1]);
            
            if ($result) {
                echo "<p style='color: green;'>Regular user created successfully</p>";
                echo "<p>Email: user@transport.com</p>";
                echo "<p>Password: user123</p>";
            } else {
                echo "<p style='color: red;'>Failed to create regular user</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>Users table does not exist. Please import the database.sql file first.</p>";
        echo "<p>Steps to fix:</p>";
        echo "<ol>";
        echo "<li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>";
        echo "<li>Create database: transport_tracking</li>";
        echo "<li>Import the database.sql file</li>";
        echo "<li>Run this setup script again</li>";
        echo "</ol>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking tables: " . $e->getMessage() . "</p>";
}

// Step 3: Test login functionality
echo "<h2>Step 3: Test Login</h2>";
echo "<p>You can now test the login system:</p>";
echo "<ul>";
echo "<li><a href='login.php'>Go to Login Page</a></li>";
echo "<li>Use admin credentials: admin@transport.com / admin123</li>";
echo "<li>Or use regular credentials: user@transport.com / user123</li>";
echo "</ul>";

// Step 4: Check file permissions
echo "<h2>Step 4: File Permissions</h2>";

$uploadDirs = ['uploads', 'uploads/drivers', 'uploads/vehicles'];
foreach ($uploadDirs as $dir) {
    if (!is_dir($dir)) {
        echo "<p style='color: orange;'>Creating directory: $dir</p>";
        mkdir($dir, 0755, true);
    } else {
        echo "<p style='color: green;'>Directory exists: $dir</p>";
    }
}

echo "<h2>Setup Complete!</h2>";
echo "<p style='color: green;'>The system should now be ready to use.</p>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";

// Debug information
echo "<h3>Debug Information:</h3>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
?>
