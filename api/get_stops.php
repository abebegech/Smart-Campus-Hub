<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get bus stops from database or return mock data
try {
    $stopsQuery = "SELECT * FROM bus_stops WHERE is_active = 1 ORDER BY name";
    $stmt = $db->query($stopsQuery);
    $stops = $stmt->fetchAll();

    if (empty($stops)) {
        // Return mock data if no stops in database
        $mockStops = [
            ['id' => 1, 'name' => 'Main Campus Gate', 'latitude' => 9.1450, 'longitude' => 40.4897],
            ['id' => 2, 'name' => 'Library', 'latitude' => 9.1460, 'longitude' => 40.4907],
            ['id' => 3, 'name' => 'Student Center', 'latitude' => 9.1440, 'longitude' => 40.4887],
            ['id' => 4, 'name' => 'Science Building', 'latitude' => 9.1470, 'longitude' => 40.4917],
            ['id' => 5, 'name' => 'Dormitory A', 'latitude' => 9.1430, 'longitude' => 40.4877]
        ];
        echo json_encode($mockStops);
    } else {
        echo json_encode($stops);
    }
} catch (Exception $e) {
    // Return mock data on error
    $mockStops = [
        ['id' => 1, 'name' => 'Main Campus Gate', 'latitude' => 9.1450, 'longitude' => 40.4897],
        ['id' => 2, 'name' => 'Library', 'latitude' => 9.1460, 'longitude' => 40.4907],
        ['id' => 3, 'name' => 'Student Center', 'latitude' => 9.1440, 'longitude' => 40.4887]
    ];
    echo json_encode($mockStops);
}
?>
