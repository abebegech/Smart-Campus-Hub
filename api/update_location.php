<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../middleware/role_check.php';

// Check if user is logged in and is a driver
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isDriver()) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Drivers only.']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['latitude']) || !isset($data['longitude'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get driver ID
$driverQuery = "SELECT id FROM drivers WHERE user_id = ?";
$driverStmt = $db->prepare($driverQuery);
$driverStmt->execute([$_SESSION['user_id']]);
$driver = $driverStmt->fetch();

if (!$driver) {
    http_response_code(404);
    echo json_encode(['error' => 'Driver not found']);
    exit;
}

// Update or insert location
$locationQuery = "INSERT INTO driver_locations (driver_id, latitude, longitude, accuracy, created_at) 
                  VALUES (?, ?, ?, ?, NOW()) 
                  ON DUPLICATE KEY UPDATE 
                  latitude = VALUES(latitude), 
                  longitude = VALUES(longitude), 
                  accuracy = VALUES(accuracy), 
                  created_at = VALUES(created_at)";
$locationStmt = $db->prepare($locationQuery);
$success = $locationStmt->execute([
    $driver['id'],
    $data['latitude'],
    $data['longitude'],
    $data['accuracy'] ?? 0
]);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Location updated successfully',
        'data' => [
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'],
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update location']);
}
?>
