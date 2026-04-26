<?php
// Firebase Realtime Database Configuration
class FirebaseConfig {
    private static $config = [
        'database_url' => 'https://transport-tracking-default-rtdb.firebaseio.com',
        'api_key' => 'AIzaSyDemoKeyForTransportTracking123456789',
        'project_id' => 'transport-tracking',
        'messaging_sender_id' => '123456789012',
        'app_id' => '1:123456789012:web:abcdef123456789'
    ];
    
    public static function getConfig() {
        return self::$config;
    }
    
    public static function getDatabaseUrl() {
        return self::$config['database_url'];
    }
    
    public static function getApiKey() {
        return self::$config['api_key'];
    }
}

// Firebase REST API Helper Class
class FirebaseAPI {
    private $baseUrl;
    private $apiKey;
    
    public function __construct() {
        $config = FirebaseConfig::getConfig();
        $this->baseUrl = $config['database_url'];
        $this->apiKey = $config['api_key'];
    }
    
    // Store bus location data
    public function updateBusLocation($busId, $latitude, $longitude, $routeId, $speed = 0) {
        $data = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'route_id' => $routeId,
            'speed' => $speed,
            'timestamp' => time(),
            'last_update' => date('Y-m-d H:i:s')
        ];
        
        $url = $this->baseUrl . '/buses/' . $busId . '.json';
        return $this->put($url, $data);
    }
    
    // Get all bus locations
    public function getAllBusLocations() {
        $url = $this->baseUrl . '/buses.json';
        return $this->get($url);
    }
    
    // Get specific bus location
    public function getBusLocation($busId) {
        $url = $this->baseUrl . '/buses/' . $busId . '.json';
        return $this->get($url);
    }
    
    // Store route data
    public function updateRouteData($routeId, $data) {
        $url = $this->baseUrl . '/routes/' . $routeId . '.json';
        return $this->put($url, $data);
    }
    
    // Store stop coordinates
    public function updateStopLocation($stopId, $latitude, $longitude, $name) {
        $data = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'name' => $name,
            'active' => true
        ];
        
        $url = $this->baseUrl . '/stops/' . $stopId . '.json';
        return $this->put($url, $data);
    }
    
    // HTTP GET request
    private function get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            return json_decode($response, true);
        }
        return false;
    }
    
    // HTTP PUT request
    private function put($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    
    // HTTP POST request
    private function post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
    
    // Delete data
    private function delete($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode == 200;
    }
}
?>
