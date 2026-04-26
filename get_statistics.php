<?php
// Dynamic Statistics API
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'transport_tracking';
$username = 'root';
$password = '';

// Get filter parameters
$dateRange = $_GET['date_range'] ?? '30';
$reportType = $_GET['report_type'] ?? 'all';

// Convert date range to SQL interval
$dateInterval = match($dateRange) {
    '7' => 'INTERVAL 7 DAY',
    '30' => 'INTERVAL 30 DAY',
    '90' => 'INTERVAL 90 DAY',
    '180' => 'INTERVAL 180 DAY',
    '365' => 'INTERVAL 365 DAY',
    default => 'INTERVAL 30 DAY'
};

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get Total Trips
    $tripQuery = "SELECT COUNT(*) as total_trips FROM trips WHERE DATE(created_at) >= DATE_SUB(CURDATE(), $dateInterval)";
    if ($reportType === 'vehicle') {
        $tripQuery .= " AND vehicle_id IS NOT NULL";
    }
    $stmt = $pdo->query($tripQuery);
    $totalTrips = $stmt->fetch(PDO::FETCH_ASSOC)['total_trips'];
    
    // Get Active Users (logged in within specified period)
    $userInterval = $dateRange <= 7 ? 'INTERVAL 7 DAY' : $dateInterval;
    $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as active_users FROM user_sessions WHERE last_activity >= DATE_SUB(NOW(), $userInterval)");
    $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];
    
    // Get Permits Issued
    $permitQuery = "SELECT COUNT(*) as permits_issued FROM permits WHERE created_at >= DATE_SUB(CURDATE(), $dateInterval)";
    if ($reportType === 'permits') {
        $permitQuery .= " AND status = 'approved'";
    }
    $stmt = $pdo->query($permitQuery);
    $permitsIssued = $stmt->fetch(PDO::FETCH_ASSOC)['permits_issued'];
    
    // Get Revenue from permits table (approved payments only)
    $revenueQuery = "SELECT COALESCE(SUM(amount), 0) as revenue FROM permits WHERE status = 'approved' AND payment_date >= DATE_SUB(CURDATE(), $dateInterval)";
    $stmt = $pdo->query($revenueQuery);
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_trips' => $totalTrips,
            'active_users' => $activeUsers,
            'permits_issued' => $permitsIssued,
            'revenue' => $revenue
        ],
        'filters' => [
            'date_range' => $dateRange,
            'report_type' => $reportType
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (PDOException $e) {
    // Fallback to sample data if database fails
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'data' => [
            'total_trips' => 1247,
            'active_users' => 89,
            'permits_issued' => 89,
            'revenue' => 12450
        ],
        'filters' => [
            'date_range' => $dateRange,
            'report_type' => $reportType
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
