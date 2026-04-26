<?php
// Export Engine - Handles PDF and CSV generation
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'transport_tracking';
$username = 'root';
$password = '';

// Get export parameters
$exportFormat = $_GET['export_format'] ?? 'csv';
$reportType = $_GET['report_type'] ?? 'all';
$dateRange = $_GET['date_range'] ?? '30';

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
    
    if ($exportFormat === 'pdf') {
        generatePDFReport($pdo, $reportType, $dateInterval);
    } else {
        generateCSVReport($pdo, $reportType, $dateInterval);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
}

function generateCSVReport($pdo, $reportType, $dateInterval) {
    $filename = 'report_' . $reportType . '_' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Header
    fputcsv($output, ['Report Generated: ' . date('Y-m-d H:i:s')]);
    fputcsv($output, ['Report Type: ' . ucfirst($reportType)]);
    fputcsv($output, ['Date Range: Last ' . str_replace(['INTERVAL ', ' DAY'], ['', ' days'], $dateInterval)]);
    fputcsv($output, []);
    
    switch ($reportType) {
        case 'vehicle':
            generateVehicleCSV($pdo, $output, $dateInterval);
            break;
        case 'driver':
            generateDriverCSV($pdo, $output, $dateInterval);
            break;
        case 'permits':
            generatePermitsCSV($pdo, $output, $dateInterval);
            break;
        default:
            generateAllReportsCSV($pdo, $output, $dateInterval);
            break;
    }
    
    fclose($output);
    exit;
}

function generatePDFReport($pdo, $reportType, $dateInterval) {
    require_once 'libraries/fpdf.php';
    
    $filename = 'report_' . $reportType . '_' . date('Y-m-d') . '.pdf';
    
    $pdf = new FPDF();
    $pdf->AddPage('P', 'A4');
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    
    // Header
    $pdf->Cell(0, 10, 'Smart Campus Hub - Reports', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 6, 'Report Type: ' . ucfirst($reportType), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Date Range: Last ' . str_replace(['INTERVAL ', ' DAY'], ['', ' days'], $dateInterval), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
    $pdf->Ln(10);
    
    switch ($reportType) {
        case 'vehicle':
            generateVehiclePDF($pdo, $pdf, $dateInterval);
            break;
        case 'driver':
            generateDriverPDF($pdo, $pdf, $dateInterval);
            break;
        case 'permits':
            generatePermitsPDF($pdo, $pdf, $dateInterval);
            break;
        default:
            generateAllReportsPDF($pdo, $pdf, $dateInterval);
            break;
    }
    
    $pdfContent = $pdf->Output('S');
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    header('Expires: 0');
    
    echo $pdfContent;
    exit;
}

function generateVehicleCSV($pdo, $output, $dateInterval) {
    fputcsv($output, ['VEHICLE USAGE REPORT']);
    fputcsv($output, []);
    fputcsv($output, ['Vehicle ID', 'Vehicle Name', 'License Plate', 'Total Trips', 'Total Distance (km)', 'Total Duration (min)', 'Status']);
    
    $stmt = $pdo->query("
        SELECT 
            v.vehicle_id,
            v.vehicle_name,
            v.license_plate,
            COUNT(t.trip_id) as total_trips,
            COALESCE(SUM(t.distance), 0) as total_distance,
            COALESCE(SUM(t.duration), 0) as total_duration,
            v.status
        FROM vehicles v
        LEFT JOIN trips t ON v.vehicle_id = t.vehicle_id 
            AND DATE(t.created_at) >= DATE_SUB(CURDATE(), $dateInterval)
        GROUP BY v.vehicle_id, v.vehicle_name, v.license_plate, v.status
        ORDER BY total_trips DESC
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['vehicle_id'],
            $row['vehicle_name'],
            $row['license_plate'],
            $row['total_trips'],
            number_format($row['total_distance'], 2),
            $row['total_duration'],
            ucfirst($row['status'])
        ]);
    }
}

function generateDriverCSV($pdo, $output, $dateInterval) {
    fputcsv($output, ['DRIVER PERFORMANCE REPORT']);
    fputcsv($output, []);
    fputcsv($output, ['Driver Name', 'Email', 'Total Trips', 'On-Time %', 'Average Duration (min)', 'Status']);
    
    $stmt = $pdo->query("
        SELECT 
            u.name,
            u.email,
            COUNT(t.trip_id) as total_trips,
            ROUND(AVG(CASE WHEN t.actual_arrival <= t.scheduled_arrival THEN 100 ELSE 0 END), 2) as on_time_percentage,
            ROUND(AVG(t.duration), 2) as avg_duration,
            u.status
        FROM users u
        LEFT JOIN trips t ON u.user_id = t.driver_id 
            AND DATE(t.created_at) >= DATE_SUB(CURDATE(), $dateInterval)
        WHERE u.role = 'driver'
        GROUP BY u.user_id, u.name, u.email, u.status
        ORDER BY on_time_percentage DESC
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['name'],
            $row['email'],
            $row['total_trips'],
            $row['on_time_percentage'] . '%',
            $row['avg_duration'],
            ucfirst($row['status'])
        ]);
    }
}

function generatePermitsCSV($pdo, $output, $dateInterval) {
    fputcsv($output, ['PERMITS REPORT']);
    fputcsv($output, []);
    fputcsv($output, ['Permit ID', 'User Name', 'Email', 'Type', 'Amount', 'Status', 'Issue Date', 'Expiry Date']);
    
    $stmt = $pdo->query("
        SELECT 
            p.permit_id,
            u.name,
            u.email,
            p.permit_type,
            p.amount,
            p.status,
            p.created_at,
            p.expiry_date
        FROM permits p
        JOIN users u ON p.user_id = u.user_id
        WHERE DATE(p.created_at) >= DATE_SUB(CURDATE(), $dateInterval)
        ORDER BY p.created_at DESC
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['permit_id'],
            $row['name'],
            $row['email'],
            $row['permit_type'],
            '$' . number_format($row['amount'], 2),
            ucfirst($row['status']),
            $row['created_at'],
            $row['expiry_date']
        ]);
    }
}

function generateAllReportsCSV($pdo, $output, $dateInterval) {
    // Summary Statistics
    fputcsv($output, ['EXECUTIVE SUMMARY']);
    fputcsv($output, []);
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_trips FROM trips WHERE DATE(created_at) >= DATE_SUB(CURDATE(), $dateInterval)");
    $totalTrips = $stmt->fetch(PDO::FETCH_ASSOC)['total_trips'];
    
    $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as active_users FROM user_sessions WHERE last_activity >= DATE_SUB(NOW(), $dateInterval)");
    $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as permits_issued FROM permits WHERE created_at >= DATE_SUB(CURDATE(), $dateInterval)");
    $permitsIssued = $stmt->fetch(PDO::FETCH_ASSOC)['permits_issued'];
    
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as revenue FROM permits WHERE status = 'approved' AND payment_date >= DATE_SUB(CURDATE(), $dateInterval)");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];
    
    fputcsv($output, ['Total Trips', $totalTrips]);
    fputcsv($output, ['Active Users', $activeUsers]);
    fputcsv($output, ['Permits Issued', $permitsIssued]);
    fputcsv($output, ['Total Revenue', '$' . number_format($revenue, 2)]);
    
    fputcsv($output, []);
    generateVehicleCSV($pdo, $output, $dateInterval);
    fputcsv($output, []);
    generateDriverCSV($pdo, $output, $dateInterval);
    fputcsv($output, []);
    generatePermitsCSV($pdo, $output, $dateInterval);
}

// PDF generation functions (simplified for brevity)
function generateVehiclePDF($pdo, $pdf, $dateInterval) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Vehicle Usage Report', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    $stmt = $pdo->query("
        SELECT 
            v.vehicle_id,
            v.vehicle_name,
            COUNT(t.trip_id) as total_trips,
            COALESCE(SUM(t.distance), 0) as total_distance
        FROM vehicles v
        LEFT JOIN trips t ON v.vehicle_id = t.vehicle_id 
            AND DATE(t.created_at) >= DATE_SUB(CURDATE(), $dateInterval)
        GROUP BY v.vehicle_id, v.vehicle_name
        ORDER BY total_trips DESC
    ");
    
    $pdf->Cell(40, 6, 'Vehicle ID', 1);
    $pdf->Cell(60, 6, 'Vehicle Name', 1);
    $pdf->Cell(30, 6, 'Trips', 1);
    $pdf->Cell(40, 6, 'Distance (km)', 1);
    $pdf->Ln();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(40, 6, $row['vehicle_id'], 1);
        $pdf->Cell(60, 6, $row['vehicle_name'], 1);
        $pdf->Cell(30, 6, $row['total_trips'], 1);
        $pdf->Cell(40, 6, number_format($row['total_distance'], 2), 1);
        $pdf->Ln();
    }
}

function generateDriverPDF($pdo, $pdf, $dateInterval) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Driver Performance Report', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    $stmt = $pdo->query("
        SELECT 
            u.name,
            COUNT(t.trip_id) as total_trips,
            ROUND(AVG(CASE WHEN t.actual_arrival <= t.scheduled_arrival THEN 100 ELSE 0 END), 2) as on_time_percentage
        FROM users u
        LEFT JOIN trips t ON u.user_id = t.driver_id 
            AND DATE(t.created_at) >= DATE_SUB(CURDATE(), $dateInterval)
        WHERE u.role = 'driver'
        GROUP BY u.user_id, u.name
        ORDER BY on_time_percentage DESC
    ");
    
    $pdf->Cell(60, 6, 'Driver Name', 1);
    $pdf->Cell(30, 6, 'Trips', 1);
    $pdf->Cell(40, 6, 'On-Time %', 1);
    $pdf->Ln();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(60, 6, $row['name'], 1);
        $pdf->Cell(30, 6, $row['total_trips'], 1);
        $pdf->Cell(40, 6, $row['on_time_percentage'] . '%', 1);
        $pdf->Ln();
    }
}

function generatePermitsPDF($pdo, $pdf, $dateInterval) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Permits Report', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    $stmt = $pdo->query("
        SELECT 
            p.permit_id,
            u.name,
            p.permit_type,
            p.amount,
            p.status
        FROM permits p
        JOIN users u ON p.user_id = u.user_id
        WHERE DATE(p.created_at) >= DATE_SUB(CURDATE(), $dateInterval)
        ORDER BY p.created_at DESC
    ");
    
    $pdf->Cell(30, 6, 'Permit ID', 1);
    $pdf->Cell(50, 6, 'User Name', 1);
    $pdf->Cell(30, 6, 'Type', 1);
    $pdf->Cell(30, 6, 'Amount', 1);
    $pdf->Cell(30, 6, 'Status', 1);
    $pdf->Ln();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(30, 6, $row['permit_id'], 1);
        $pdf->Cell(50, 6, $row['name'], 1);
        $pdf->Cell(30, 6, $row['permit_type'], 1);
        $pdf->Cell(30, 6, '$' . number_format($row['amount'], 2), 1);
        $pdf->Cell(30, 6, ucfirst($row['status']), 1);
        $pdf->Ln();
    }
}

function generateAllReportsPDF($pdo, $pdf, $dateInterval) {
    generateVehiclePDF($pdo, $pdf, $dateInterval);
    $pdf->AddPage();
    generateDriverPDF($pdo, $pdf, $dateInterval);
    $pdf->AddPage();
    generatePermitsPDF($pdo, $pdf, $dateInterval);
}
?>
