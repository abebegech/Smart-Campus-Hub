<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get routes with coordinates from database or return mock data
try {
    $routesQuery = "SELECT * FROM routes WHERE is_active = 1 ORDER BY name";
    $stmt = $db->query($routesQuery);
    $routes = $stmt->fetchAll();

    if (empty($routes)) {
        // Return mock data if no routes in database
        $mockRoutes = [
            [
                'id' => 1,
                'name' => 'Campus Loop',
                'coordinates' => [
                    [9.1450, 40.4897],
                    [9.1460, 40.4907],
                    [9.1470, 40.4917],
                    [9.1480, 40.4927],
                    [9.1450, 40.4897]
                ]
            ],
            [
                'id' => 2,
                'name' => 'Science Route',
                'coordinates' => [
                    [9.1440, 40.4887],
                    [9.1450, 40.4897],
                    [9.1460, 40.4907],
                    [9.1470, 40.4917]
                ]
            ]
        ];
        echo json_encode($mockRoutes);
    } else {
        // Add coordinates to routes if missing
        foreach ($routes as &$route) {
            if (!isset($route['coordinates'])) {
                // Generate mock coordinates for demonstration
                $baseLat = 9.1450;
                $baseLng = 40.4897;
                $route['coordinates'] = [
                    [$baseLat, $baseLng],
                    [$baseLat + 0.001, $baseLng + 0.001],
                    [$baseLat + 0.002, $baseLng + 0.002],
                    [$baseLat + 0.003, $baseLng + 0.003],
                    [$baseLat, $baseLng]
                ];
            }
        }
        echo json_encode($routes);
    }
} catch (Exception $e) {
    // Return mock data on error
    $mockRoutes = [
        [
            'id' => 1,
            'name' => 'Campus Loop',
            'coordinates' => [
                [9.1450, 40.4897],
                [9.1460, 40.4907],
                [9.1470, 40.4917]
            ]
        ]
    ];
    echo json_encode($mockRoutes);
}
?>
