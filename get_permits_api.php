<?php
// API endpoint for permits data
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT tp.*, u.first_name, u.last_name, u.email 
              FROM transport_permits tp 
              JOIN users u ON tp.user_id = u.id 
              ORDER BY tp.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $permits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'permits' => $permits,
        'count' => count($permits)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
