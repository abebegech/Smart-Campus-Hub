<?php
session_start();
require_once 'config/database.php';
require_once 'middleware/rbac.php';

// RBAC: Students and Admins can access live tracking
RBAC::requireAnyRole(['student', 'admin']);

$database = new Database();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Bus Tracking - Smart Campus Hub</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .tracking-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        #map {
            height: 600px;
            width: 100%;
        }
        
        .stats-bar {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .bus-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        
        .bus-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .bus-list-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .refresh-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .refresh-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #e9ecef;
            border-top: 2px solid #2c3e50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .bus-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .bus-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #2c3e50;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .bus-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .bus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .bus-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .bus-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #28a745;
            animation: pulse 2s infinite;
        }
        
        .status-dot.inactive {
            background: #6c757d;
            animation: none;
        }
        
        .bus-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 14px;
        }
        
        .bus-detail {
            color: #6c757d;
        }
        
        .bus-detail strong {
            color: #2c3e50;
        }
        
        .last-update {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            text-align: right;
        }
        
        .no-buses {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .no-buses i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            #map {
                height: 400px;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .bus-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>
                <i class="fas fa-map-marked-alt"></i>
                Live Bus Tracking
            </h1>
            <div class="user-info">
                <i class="fas fa-user"></i>
                <span><?php echo RBAC::isStudent() ? 'Student' : 'Administrator'; ?></span>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="tracking-section">
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-value" id="activeBuses">0</div>
                    <div class="stat-label">Active Buses</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="avgSpeed">0</div>
                    <div class="stat-label">Avg Speed (km/h)</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="lastUpdate">--:--</div>
                    <div class="stat-label">Last Update</div>
                </div>
            </div>
            <div id="map"></div>
        </div>
        
        <div class="bus-list">
            <div class="bus-list-header">
                <div class="bus-list-title">
                    <i class="fas fa-bus"></i> Active Buses
                </div>
                <div class="refresh-indicator">
                    <div class="refresh-spinner"></div>
                    <span>Auto-refreshing...</span>
                </div>
            </div>
            <div id="busGrid" class="bus-grid">
                <!-- Bus cards will be populated by JavaScript -->
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([40.7128, -74.0060], 13);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Bus markers and data
        const busMarkers = {};
        let busData = [];
        
        // Custom bus icon
        const busIcon = L.divIcon({
            html: '<div style="background: #2c3e50; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">Bus</div>',
            className: 'bus-marker',
            iconSize: [30, 30]
        });
        
        // Fetch bus locations
        async function fetchBusLocations() {
            try {
                const response = await fetch('create_gps_api.php');
                const data = await response.json();
                
                if (data.success) {
                    updateBusData(data.buses);
                    updateMap(data.buses);
                    updateStats(data.buses);
                } else {
                    console.error('Failed to fetch bus locations:', data.error);
                }
            } catch (error) {
                console.error('Error fetching bus locations:', error);
            }
        }
        
        function updateBusData(buses) {
            busData = buses;
            updateBusGrid(buses);
        }
        
        function updateBusGrid(buses) {
            const busGrid = document.getElementById('busGrid');
            
            if (buses.length === 0) {
                busGrid.innerHTML = `
                    <div class="no-buses" style="grid-column: 1 / -1;">
                        <i class="fas fa-bus-slash"></i>
                        <h3>No Active Buses</h3>
                        <p>No buses are currently broadcasting their location.</p>
                    </div>
                `;
                return;
            }
            
            busGrid.innerHTML = buses.map(bus => `
                <div class="bus-card">
                    <div class="bus-header">
                        <div class="bus-number">Bus ${bus.bus_number || bus.id}</div>
                        <div class="bus-status">
                            <div class="status-dot"></div>
                            <span>Active</span>
                        </div>
                    </div>
                    <div class="bus-details">
                        <div class="bus-detail"><strong>Driver:</strong> ${bus.driver_name || 'Unknown'}</div>
                        <div class="bus-detail"><strong>Speed:</strong> ${bus.speed_kmh} km/h</div>
                        <div class="bus-detail"><strong>License:</strong> ${bus.license_plate || 'N/A'}</div>
                        <div class="bus-detail"><strong>Heading:</strong> ${Math.round(bus.heading)}°</div>
                    </div>
                    <div class="last-update">
                        Last seen: ${formatTime(bus.last_update)}
                    </div>
                </div>
            `).join('');
        }
        
        function updateMap(buses) {
            // Update existing markers and add new ones
            buses.forEach(bus => {
                if (busMarkers[bus.id]) {
                    // Update existing marker position
                    busMarkers[bus.id].setLatLng([bus.lat, bus.lng]);
                } else {
                    // Add new marker
                    const marker = L.marker([bus.lat, bus.lng], { icon: busIcon })
                        .addTo(map);
                    
                    // Create popup content
                    const popupContent = `
                        <div style="min-width: 200px;">
                            <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Bus ${bus.bus_number || bus.id}</h4>
                            <div style="font-size: 14px;">
                                <div><strong>Driver:</strong> ${bus.driver_name || 'Unknown'}</div>
                                <div><strong>Speed:</strong> ${bus.speed_kmh} km/h</div>
                                <div><strong>License:</strong> ${bus.license_plate || 'N/A'}</div>
                                <div><strong>Last Update:</strong> ${formatTime(bus.last_update)}</div>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent);
                    busMarkers[bus.id] = marker;
                }
            });
            
            // Remove markers for buses that are no longer active
            Object.keys(busMarkers).forEach(busId => {
                if (!buses.find(bus => bus.id == busId)) {
                    map.removeLayer(busMarkers[busId]);
                    delete busMarkers[busId];
                }
            });
            
            // Update map bounds to show all buses
            if (buses.length > 0) {
                const bounds = L.latLngBounds(buses.map(bus => [bus.lat, bus.lng]));
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
        
        function updateStats(buses) {
            document.getElementById('activeBuses').textContent = buses.length;
            
            const avgSpeed = buses.length > 0 
                ? (buses.reduce((sum, bus) => sum + bus.speed_kmh, 0) / buses.length).toFixed(1)
                : 0;
            document.getElementById('avgSpeed').textContent = avgSpeed;
            
            document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString([], { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
        
        function formatTime(timestamp) {
            const date = new Date(timestamp * 1000);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds
            
            if (diff < 60) {
                return 'Just now';
            } else if (diff < 3600) {
                return Math.floor(diff / 60) + ' min ago';
            } else {
                return date.toLocaleTimeString();
            }
        }
        
        // Initial load
        fetchBusLocations();
        
        // Auto-refresh every 5 seconds
        setInterval(fetchBusLocations, 5000);
        
        // Add custom marker styles
        const style = document.createElement('style');
        style.textContent = `
            .bus-marker {
                background: #2c3e50;
                border: 2px solid white;
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                text-align: center;
                line-height: 26px;
                font-size: 12px;
                font-weight: bold;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
