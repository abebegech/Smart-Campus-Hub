<?php
// Real-Time Location Provider
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Sample vehicle location data - in real app, this would come from database
$vehicleLocations = [
    [
        'id' => 'BUS-001',
        'name' => 'Campus Shuttle A',
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'status' => 'active',
        'speed' => 25,
        'route' => 'Main Campus Loop',
        'driver' => 'Mike Johnson',
        'last_update' => date('Y-m-d H:i:s'),
        'passengers' => 35,
        'capacity' => 50
    ],
    [
        'id' => 'BUS-002',
        'name' => 'Engineering Route',
        'latitude' => 40.7150,
        'longitude' => -74.0080,
        'status' => 'active',
        'speed' => 30,
        'route' => 'Engineering Building',
        'driver' => 'Sarah Wilson',
        'last_update' => date('Y-m-d H:i:s'),
        'passengers' => 48,
        'capacity' => 50
    ],
    [
        'id' => 'BUS-003',
        'name' => 'Library Express',
        'latitude' => 40.7100,
        'longitude' => -74.0040,
        'status' => 'idle',
        'speed' => 0,
        'route' => 'Library Route',
        'driver' => 'David Chen',
        'last_update' => date('Y-m-d H:i:s'),
        'passengers' => 20,
        'capacity' => 50
    ],
    [
        'id' => 'BUS-004',
        'name' => 'Sports Complex',
        'latitude' => 40.7080,
        'longitude' => -74.0020,
        'status' => 'maintenance',
        'speed' => 0,
        'route' => 'Maintenance Garage',
        'driver' => 'Tom Davis',
        'last_update' => date('Y-m-d H:i:s'),
        'passengers' => 0,
        'capacity' => 50
    ]
];

// Return JSON response
echo json_encode([
    'success' => true,
    'data' => $vehicleLocations,
    'timestamp' => date('Y-m-d H:i:s'),
    'total_vehicles' => count($vehicleLocations)
]);
?>
