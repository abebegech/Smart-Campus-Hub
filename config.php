<?php
/**
 * Configuration file for Smart Campus Hub
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'transport_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Application settings
define('APP_NAME', 'Smart Campus Hub');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/TransportTracking/');

// Session settings (must be set before session_start)
if (!session_id()) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
}
?>
