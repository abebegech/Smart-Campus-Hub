<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: working_login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get user information
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($userQuery);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Bus Tracking - Transport Tracker</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-menu a:hover, .nav-menu a.active {
            background-color: #34495e;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .tracking-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
            height: calc(100vh - 200px);
        }

        .map-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        #map {
            height: 100%;
            min-height: 500px;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .eta-panel {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .eta-panel h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .stop-selector {
            margin-bottom: 1rem;
        }

        .stop-selector select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .eta-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .eta-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border-left: 4px solid #3498db;
        }

        .eta-item h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .eta-details {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #666;
        }

        .eta-time {
            font-weight: bold;
            color: #27ae60;
        }

        .status-panel {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .status-panel h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #27ae60;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .bus-info {
            background: #f8f9fa;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .bus-info strong {
            color: #2c3e50;
        }

        .legend {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .legend h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .legend-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .legend-icon.bus {
            background: #3498db;
        }

        .legend-icon.stop {
            background: #e74c3c;
        }

        .last-update {
            font-size: 0.8rem;
            color: #666;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .tracking-container {
                grid-template-columns: 1fr;
                height: auto;
            }

            .sidebar {
                display: grid;
                grid-template-columns: 1fr;
            }

            #map {
                height: 400px;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <div class="logo">Transport Tracker</div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="working_dashboard.php">Dashboard</a></li>
                    <li><a href="realtime_tracking.php" class="active">Live Tracking</a></li>
                    <li><a href="driver.php">Driver Portal</a></li>
                    <li><a href="permits.php">Permits</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="tracking-container">
            <div class="map-section">
                <div id="map"></div>
            </div>

            <div class="sidebar">
                <div class="eta-panel">
                    <h3>Bus ETA</h3>
                    <div class="stop-selector">
                        <select id="stopSelect">
                            <option value="">Select a stop...</option>
                        </select>
                    </div>
                    <div class="eta-list" id="etaList">
                        <p style="color: #666; text-align: center;">Select a stop to see ETA</p>
                    </div>
                </div>

                <div class="status-panel">
                    <h3>System Status</h3>
                    <div class="status-indicator">
                        <div class="status-dot"></div>
                        <span>Live Tracking Active</span>
                    </div>
                    <div id="busList">
                        <p style="color: #666;">Loading buses...</p>
                    </div>
                    <div class="last-update" id="lastUpdate">
                        Last update: --
                    </div>
                </div>

                <div class="legend">
                    <h3>Legend</h3>
                    <div class="legend-item">
                        <div class="legend-icon bus">BUS</div>
                        <span>Active Bus</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon stop">STOP</div>
                        <span>Bus Stop</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Firebase SDK (optional - for real-time updates) -->
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>
    
    <script>
        // Initialize the bus tracker
        let busTracker;
        let stops = [];

        // Load stops and initialize tracker
        document.addEventListener('DOMContentLoaded', async function() {
            await loadStops();
            initializeTracker();
            updateETAList();
        });

        async function loadStops() {
            try {
                const response = await fetch('api/get_stops.php');
                stops = await response.json();
                
                const stopSelect = document.getElementById('stopSelect');
                stops.forEach(stop => {
                    const option = document.createElement('option');
                    option.value = stop.id;
                    option.textContent = stop.name;
                    stopSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Failed to load stops:', error);
            }
        }

        function initializeTracker() {
            // Load tracker.js functionality
            if (typeof BusTracker !== 'undefined') {
                busTracker = new BusTracker();
            } else {
                // Fallback initialization
                initializeMap();
                startMockUpdates();
            }
        }

        function initializeMap() {
            // Initialize Leaflet map
            const map = L.map('map').setView([9.1450, 40.4897], 13);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            // Add bus stops
            stops.forEach(stop => {
                const marker = L.marker([stop.latitude, stop.longitude]).addTo(map);
                marker.bindPopup(`<b>${stop.name}</b><br>Bus Stop`);
            });

            // Add mock buses
            addMockBuses(map);
        }

        function addMockBuses(map) {
            const busIcon = L.divIcon({
                html: '<div style="background-color: #3498db; color: white; padding: 5px; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold;">BUS</div>',
                iconSize: [30, 30],
                className: 'bus-marker'
            });

            const mockBuses = [
                { id: 'BUS001', lat: 9.1450, lng: 40.4897 },
                { id: 'BUS002', lat: 9.1460, lng: 40.4907 }
            ];

            mockBuses.forEach(bus => {
                const marker = L.marker([bus.lat, bus.lng], { icon: busIcon }).addTo(map);
                marker.bindPopup(`<b>${bus.id}</b><br>Route: Campus Loop`);
            });
        }

        function startMockUpdates() {
            // Update bus list
            updateBusList();
            
            // Update every 5 seconds
            setInterval(() => {
                updateBusList();
                updateETAList();
                updateLastUpdateTime();
            }, 5000);
        }

        function updateBusList() {
            const busList = document.getElementById('busList');
            const mockBuses = [
                { id: 'BUS001', route: 'Campus Loop', status: 'Active' },
                { id: 'BUS002', route: 'Science Route', status: 'Active' }
            ];

            busList.innerHTML = mockBuses.map(bus => `
                <div class="bus-info">
                    <strong>${bus.id}</strong> - ${bus.route}<br>
                    Status: ${bus.status}
                </div>
            `).join('');
        }

        function updateETAList() {
            const stopSelect = document.getElementById('stopSelect');
            const etaList = document.getElementById('etaList');
            
            if (!stopSelect.value) {
                etaList.innerHTML = '<p style="color: #666; text-align: center;">Select a stop to see ETA</p>';
                return;
            }

            // Mock ETA calculations
            const mockETA = [
                { busId: 'BUS001', eta: 5, distance: 2.1 },
                { busId: 'BUS002', eta: 12, distance: 4.8 }
            ];

            etaList.innerHTML = mockETA.map(eta => `
                <div class="eta-item">
                    <h4>${eta.busId}</h4>
                    <div class="eta-details">
                        <span>Distance: ${eta.distance} km</span>
                        <span class="eta-time">${eta.eta} min</span>
                    </div>
                </div>
            `).join('');
        }

        function updateLastUpdateTime() {
            const lastUpdate = document.getElementById('lastUpdate');
            lastUpdate.textContent = `Last update: ${new Date().toLocaleTimeString()}`;
        }

        // Stop selector change handler
        document.getElementById('stopSelect').addEventListener('change', updateETAList);
    </script>

    <!-- Load tracker.js -->
    <script src="js/tracker.js"></script>
</body>
</html>
