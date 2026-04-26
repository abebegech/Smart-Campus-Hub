<?php
// Live Tracking Page
require_once '../auth.php';
$currentUser = getCurrentUser();
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <!-- Map Container -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Live Vehicle Tracking</h3>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-400">Last updated: <span id="lastUpdate"><?php echo date('H:i:s'); ?></span></span>
                <button onclick="refreshMap()" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Refresh
                </button>
            </div>
        </div>
        
        <!-- Map -->
        <div id="liveMap" class="h-96 bg-gray-700 rounded-lg"></div>
        
        <!-- Map Legend -->
        <div class="mt-4 flex items-center space-x-6 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-gray-300">Active</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                <span class="text-gray-300">Idle</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <span class="text-gray-300">Offline</span>
            </div>
        </div>
    </div>
    </div>

    <!-- Vehicle List -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Active Vehicles</h3>
        <div class="space-y-3">
            <?php
            // Sample vehicle data - in real app, this would come from database
            $vehicles = [
                ['id' => 'BUS-001', 'name' => 'Campus Shuttle A', 'route' => 'Main Campus Loop', 'status' => 'active', 'speed' => '25 km/h', 'location' => 'Main Gate'],
                ['id' => 'BUS-002', 'name' => 'Engineering Route', 'route' => 'Engineering Building', 'status' => 'active', 'speed' => '30 km/h', 'location' => 'Engineering Block'],
                ['id' => 'BUS-003', 'name' => 'Library Express', 'route' => 'Library Route', 'status' => 'idle', 'speed' => '0 km/h', 'location' => 'Library'],
                ['id' => 'BUS-004', 'name' => 'Sports Complex', 'route' => 'Sports Route', 'status' => 'maintenance', 'speed' => '0 km/h', 'location' => 'Maintenance Garage']
            ];
            
            foreach ($vehicles as $vehicle):
            ?>
                <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                            <?php
                            $iconColor = $vehicle['status'] === 'active' ? 'text-green-400' : 
                                       ($vehicle['status'] === 'idle' ? 'text-yellow-400' : 'text-red-400');
                            ?>
                            <i data-lucide="bus" class="w-5 h-5 <?php echo $iconColor; ?>"></i>
                        </div>
                        <div>
                            <p class="text-white font-medium"><?php echo $vehicle['name']; ?></p>
                            <p class="text-gray-400 text-sm"><?php echo $vehicle['route']; ?></p>
                            <p class="text-gray-500 text-xs">ID: <?php echo $vehicle['id']; ?></p>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="space-y-1">
                            <span class="text-xs text-gray-400">Status: 
                                <?php
                                $statusColor = $vehicle['status'] === 'active' ? 'text-green-400' : 
                                              ($vehicle['status'] === 'idle' ? 'text-yellow-400' : 'text-red-400');
                                $statusText = ucfirst($vehicle['status']);
                                ?>
                                <span class="<?php echo $statusColor; ?>"><?php echo $statusText; ?></span>
                            </span>
                            <span class="text-xs text-gray-400">Speed: <span class="text-white"><?php echo $vehicle['speed']; ?></span></span>
                            <span class="text-xs text-gray-400">Location: <span class="text-white"><?php echo $vehicle['location']; ?></span></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Driver Actions (for drivers only) -->
    <?php if ($userRole === 'driver'): ?>
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Driver Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button onclick="startTrip()" class="flex items-center space-x-2 p-4 bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="play" class="w-5 h-5 text-white"></i>
                    <span class="text-white">Start Trip</span>
                </button>
                
                <button onclick="endTrip()" class="flex items-center space-x-2 p-4 bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="stop" class="w-5 h-5 text-white"></i>
                    <span class="text-white">End Trip</span>
                </button>
            </div>
            
            <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-400 mb-2">Current Trip Status:</p>
                <p class="text-white font-medium" id="tripStatus">No active trip</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize map
let map;
let markers = [];

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    loadVehicleLocations();
});

function initializeMap() {
    // Create map
    map = L.map('liveMap').setView([40.7128, -74.0060], 13); // Default to campus coordinates
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
}

function loadVehicleLocations() {
    // In real implementation, this would fetch from get_locations.php
    // For now, using sample data
    const vehicles = [
        {id: 'BUS-001', lat: 40.7128, lng: -74.0060, name: 'Campus Shuttle A', status: 'active'},
        {id: 'BUS-002', lat: 40.7150, lng: -74.0080, name: 'Engineering Route', status: 'active'},
        {id: 'BUS-003', lat: 40.7100, lng: -74.0040, name: 'Library Express', status: 'idle'},
        {id: 'BUS-004', lat: 40.7080, lng: -74.0020, name: 'Sports Complex', status: 'maintenance'}
    ];
    
    updateMapMarkers(vehicles);
}

function updateMapMarkers(vehicles) {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    // Add new markers
    vehicles.forEach(vehicle => {
        const color = vehicle.status === 'active' ? '#10b981' : 
                     vehicle.status === 'idle' ? '#f59e0b' : '#ef4444';
        
        const marker = L.circleMarker([vehicle.lat, vehicle.lng], {
            radius: 15,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 0.8
        }).addTo(map);
        
        marker.bindPopup(`
            <div class="p-2">
                <h4 class="font-bold">${vehicle.name}</h4>
                <p class="text-sm">Status: ${vehicle.status}</p>
                <p class="text-xs">ID: ${vehicle.id}</p>
            </div>
        `);
        
        markers.push(marker);
    });
    
    // Update last update time
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
}

function refreshMap() {
    loadVehicleLocations();
}

function startTrip() {
    // Start GPS tracking
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            position => {
                // Send location to server
                const locationData = {
                    driver_id: '<?php echo $currentUser['id']; ?>',
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    timestamp: new Date().toISOString()
                };
                
                // POST to server
                fetch('update_location.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(locationData)
                });
                
                // Update UI
                document.getElementById('tripStatus').textContent = 'Trip in progress...';
                document.getElementById('tripStatus').className = 'text-green-400 font-medium';
            },
            error => {
                console.error('GPS error:', error);
            }
        );
    }
}

function endTrip() {
    // Stop GPS tracking
    if (navigator.geolocation) {
        navigator.geolocation.clearWatch();
        document.getElementById('tripStatus').textContent = 'Trip ended';
        document.getElementById('tripStatus').className = 'text-red-400 font-medium';
    }
}

// Auto-refresh every 5 seconds (will be replaced by main.php implementation)
setInterval(() => {
    refreshMap();
}, 5000);
</script>
