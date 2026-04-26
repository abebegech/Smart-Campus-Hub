<?php
// Location Update Handler for Driver GPS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['driver_id']) && isset($input['latitude']) && isset($input['longitude'])) {
        // Validate coordinates
        $lat = filter_var($input['latitude'], FILTER_VALIDATE_FLOAT);
        $lng = filter_var($input['longitude'], FILTER_VALIDATE_FLOAT);
        $accuracy = $input['accuracy'] ?? 0;
        $timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');
        
        if ($lat !== false && $lng !== false) {
            // In real implementation, this would save to database
            // For demo, we'll just log the update
            $logEntry = sprintf(
                "[%s] Driver %s: Lat=%.6f, Lng=%.6f, Accuracy=%.2fm, Status=%s\n",
                $timestamp,
                $input['driver_id'],
                $lat,
                $lng,
                $accuracy,
                $input['trip_status'] ?? 'unknown'
            );
            
            // Append to log file (in production, use database)
            file_put_contents('gps_updates.log', $logEntry, FILE_APPEND | LOCK_EX);
            
            echo json_encode([
                'success' => true,
                'message' => 'Location updated successfully',
                'timestamp' => $timestamp,
                'coordinates' => [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'accuracy' => $accuracy
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid coordinates provided'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: driver_id, latitude, longitude'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST method allowed'
    ]);
}
?>
