<?php
// Live tracking content
session_start();
require_once 'config.php';

$userRole = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? null;

// Sample bus data (in real app, this would come from database/GPS)
$buses = [
    [
        'id' => 'A-42',
        'driver' => 'Mike Johnson',
        'route' => 'Campus Loop',
        'status' => 'active',
        'speed' => 45,
        'passengers' => 28,
        'capacity' => 40,
        'lat' => 40.7128,
        'lng' => -74.0060,
        'next_stop' => 'Library',
        'eta' => '5 mins',
        'battery' => 85,
        'fuel' => 70
    ],
    [
        'id' => 'B-15',
        'driver' => 'Sarah Davis',
        'route' => 'Express Route',
        'status' => 'active',
        'speed' => 55,
        'passengers' => 35,
        'capacity' => 40,
        'lat' => 40.7580,
        'lng' => -73.9855,
        'next_stop' => 'Main Gate',
        'eta' => '3 mins',
        'battery' => 92,
        'fuel' => 85
    ],
    [
        'id' => 'C-23',
        'driver' => 'Tom Wilson',
        'route' => 'Night Service',
        'status' => 'delayed',
        'speed' => 30,
        'passengers' => 15,
        'capacity' => 40,
        'lat' => 40.7489,
        'lng' => -73.9680,
        'next_stop' => 'Dormitory A',
        'eta' => '12 mins',
        'battery' => 67,
        'fuel' => 45
    ],
    [
        'id' => 'D-31',
        'driver' => 'Emily Chen',
        'route' => 'Campus Loop',
        'status' => 'active',
        'speed' => 40,
        'passengers' => 22,
        'capacity' => 40,
        'lat' => 40.7282,
        'lng' => -73.9942,
        'next_stop' => 'Science Building',
        'eta' => '8 mins',
        'battery' => 78,
        'fuel' => 60
    ]
];

$routes = [
    ['name' => 'Campus Loop', 'color' => '#667eea', 'buses' => 2],
    ['name' => 'Express Route', 'color' => '#22c55e', 'buses' => 1],
    ['name' => 'Night Service', 'color' => '#f59e0b', 'buses' => 1]
];

$trackingStats = [
    'total_buses' => count($buses),
    'active_buses' => count(array_filter($buses, fn($b) => $b['status'] === 'active')),
    'delayed_buses' => count(array_filter($buses, fn($b) => $b['status'] === 'delayed')),
    'total_passengers' => array_sum(array_column($buses, 'passengers')),
    'avg_speed' => round(array_sum(array_column($buses, 'speed')) / count($buses))
];
?>

<div class="space-y-6 animate-fade-in">
    <!-- Header Section -->
    <div class="glass rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">Live Tracking</h2>
                <p class="text-dark-text-secondary">Real-time bus tracking and fleet monitoring</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="px-4 py-2 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg hover:bg-green-500/20 transition-colors flex items-center space-x-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Auto Refresh: ON</span>
                </button>
                <button onclick="exportTrackingData()" class="px-4 py-2 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors flex items-center space-x-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Export Data</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tracking Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="bus" class="w-8 h-8 text-primary"></i>
                <span class="text-xs text-primary bg-primary/20 px-2 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $trackingStats['total_buses']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Total Buses</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="activity" class="w-8 h-8 text-green-400"></i>
                <span class="text-xs text-green-400 bg-green-400/20 px-2 py-1 rounded-full animate-pulse">Live</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $trackingStats['active_buses']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Active</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-yellow-400"></i>
                <span class="text-xs text-yellow-400 bg-yellow-400/20 px-2 py-1 rounded-full">Alert</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $trackingStats['delayed_buses']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Delayed</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="users" class="w-8 h-8 text-blue-400"></i>
                <span class="text-xs text-blue-400 bg-blue-400/20 px-2 py-1 rounded-full">Current</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $trackingStats['total_passengers']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Passengers</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="gauge" class="w-8 h-8 text-purple-400"></i>
                <span class="text-xs text-purple-400 bg-purple-400/20 px-2 py-1 rounded-full">Average</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $trackingStats['avg_speed']; ?></h3>
            <p class="text-sm text-dark-text-secondary">km/h Speed</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Live Map -->
        <div class="lg:col-span-2">
            <div class="glass rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
                        <i data-lucide="map" class="w-5 h-5 text-primary"></i>
                        <span>Live Map</span>
                    </h3>
                    <div class="flex space-x-2">
                        <button onclick="centerMapOnAllBuses()" class="text-primary hover:text-primary/80 transition-colors" title="Center on all buses">
                            <i data-lucide="maximize-2" class="w-4 h-4"></i>
                        </button>
                        <button onclick="toggleMapStyle()" class="text-primary hover:text-primary/80 transition-colors" title="Toggle map style">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </button>
                        <button onclick="fullscreenMap()" class="text-primary hover:text-primary/80 transition-colors" title="Fullscreen">
                            <i data-lucide="maximize" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div id="liveMap" class="h-96 bg-dark-bg rounded-lg relative">
                    <!-- Map will be initialized here -->
                    <div class="absolute inset-0 flex items-center justify-center" id="mapLoading">
                        <div class="text-center">
                            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-primary mx-auto mb-2"></i>
                            <p class="text-dark-text-secondary">Loading map...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Map Legend -->
                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-dark-text-secondary">Active</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-dark-text-secondary">Delayed</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-dark-text-secondary">Offline</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Routes Overview -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="route" class="w-5 h-5 text-primary"></i>
                    <span>Routes</span>
                </h3>
                <div class="space-y-3">
                    <?php foreach ($routes as $route): ?>
                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-white/5 transition-colors cursor-pointer" onclick="filterByRoute('<?php echo $route['name']; ?>')">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full" style="background-color: <?php echo $route['color']; ?>;"></div>
                            <span class="text-white"><?php echo $route['name']; ?></span>
                        </div>
                        <span class="text-sm text-dark-text-secondary"><?php echo $route['buses']; ?> buses</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Active Buses List -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="bus" class="w-5 h-5 text-primary"></i>
                    <span>Active Buses</span>
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php foreach ($buses as $bus): ?>
                    <div class="glass-dark rounded-lg p-3 hover:bg-white/5 transition-colors cursor-pointer" onclick="focusOnBus('<?php echo $bus['id']; ?>')">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-primary to-secondary rounded flex items-center justify-center">
                                    <span class="text-white text-xs font-bold"><?php echo $bus['id']; ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white"><?php echo $bus['driver']; ?></p>
                                    <p class="text-xs text-dark-text-secondary"><?php echo $bus['route']; ?></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                <?php echo $bus['status'] === 'active' ? 'bg-green-400/20 text-green-400' : 'bg-yellow-400/20 text-yellow-400'; ?>">
                                <span class="w-2 h-2 bg-current rounded-full mr-1 animate-pulse"></span>
                                <?php echo ucfirst($bus['status']); ?>
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-dark-text-secondary">Speed:</span>
                                <span class="text-white ml-1"><?php echo $bus['speed']; ?> km/h</span>
                            </div>
                            <div>
                                <span class="text-dark-text-secondary">ETA:</span>
                                <span class="text-white ml-1"><?php echo $bus['eta']; ?></span>
                            </div>
                            <div>
                                <span class="text-dark-text-secondary">Next:</span>
                                <span class="text-white ml-1"><?php echo $bus['next_stop']; ?></span>
                            </div>
                            <div>
                                <span class="text-dark-text-secondary">Load:</span>
                                <span class="text-white ml-1"><?php echo $bus['passengers']; ?>/<?php echo $bus['capacity']; ?></span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-dark-border rounded-full h-1">
                                <div class="bg-gradient-to-r from-primary to-secondary h-1 rounded-full transition-all duration-300" 
                                     style="width: <?php echo ($bus['passengers'] / $bus['capacity']) * 100; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="zap" class="w-5 h-5 text-primary"></i>
                    <span>Quick Actions</span>
                </h3>
                <div class="space-y-3">
                    <button onclick="sendBroadcastMessage()" class="w-full px-4 py-3 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Broadcast Message</span>
                    </button>
                    <button onclick="showEmergencyAlert()" class="w-full px-4 py-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg hover:bg-red-500/20 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        <span>Emergency Alert</span>
                    </button>
                    <button onclick="generateTrackingReport()" class="w-full px-4 py-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg hover:bg-green-500/20 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Tracking Report</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Broadcast Message Modal -->
<div id="broadcastModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-dark rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Broadcast Message</h3>
            <button onclick="closeBroadcastModal()" class="text-dark-text-secondary hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form onsubmit="sendBroadcast(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-dark-text-secondary mb-2">Message</label>
                <textarea name="message" required rows="3" placeholder="Enter your message..."
                          class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary
                                 placeholder-dark-text-secondary focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-text-secondary mb-2">Target</label>
                <select name="target" class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                    <option value="all">All Buses</option>
                    <option value="route">Specific Route</option>
                    <option value="bus">Specific Bus</option>
                </select>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                    Send Broadcast
                </button>
                <button type="button" onclick="closeBroadcastModal()" class="flex-1 px-4 py-3 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let map = null;
let markers = [];
let autoRefreshInterval = null;
let mapStyle = 'standard';

// Initialize map when page loads
function initializeMap() {
    if (typeof L !== 'undefined') {
        // Remove loading indicator
        document.getElementById('mapLoading').style.display = 'none';
        
        // Initialize map
        map = L.map('liveMap').setView([40.7128, -74.0060], 12);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add bus markers
        updateBusMarkers();
        
        // Start auto-refresh
        startAutoRefresh();
    }
}

function updateBusMarkers() {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    // Add new markers
    const buses = <?php echo json_encode($buses); ?>;
    buses.forEach(bus => {
        const color = bus.status === 'active' ? '#22c55e' : bus.status === 'delayed' ? '#f59e0b' : '#ef4444';
        
        const marker = L.circleMarker([bus.lat, bus.lng], {
            radius: 8,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);
        
        marker.bindPopup(`
            <div class="text-sm">
                <strong>Bus ${bus.id}</strong><br>
                Driver: ${bus.driver}<br>
                Route: ${bus.route}<br>
                Speed: ${bus.speed} km/h<br>
                Passengers: ${bus.passengers}/${bus.capacity}<br>
                Next Stop: ${bus.next_stop}<br>
                ETA: ${bus.eta}<br>
                Status: <span style="color: ${color}">${bus.status}</span>
            </div>
        `);
        
        markers.push(marker);
    });
}

function focusOnBus(busId) {
    const buses = <?php echo json_encode($buses); ?>;
    const bus = buses.find(b => b.id === busId);
    
    if (bus && map) {
        map.flyTo([bus.lat, bus.lng], 15, {
            duration: 1.5
        });
        
        // Open popup for this bus
        const markerIndex = buses.findIndex(b => b.id === busId);
        if (markers[markerIndex]) {
            markers[markerIndex].openPopup();
        }
    }
}

function centerMapOnAllBuses() {
    if (map && markers.length > 0) {
        const group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

function toggleMapStyle() {
    // Toggle between different map styles
    mapStyle = mapStyle === 'standard' ? 'dark' : 'standard';
    // Implementation would depend on the map service used
    console.log('Map style changed to:', mapStyle);
}

function fullscreenMap() {
    const mapElement = document.getElementById('liveMap');
    if (mapElement.requestFullscreen) {
        mapElement.requestFullscreen();
    } else if (mapElement.webkitRequestFullscreen) {
        mapElement.webkitRequestFullscreen();
    }
}

function filterByRoute(routeName) {
    // Filter buses by route
    const buses = <?php echo json_encode($buses); ?>;
    const filteredBuses = buses.filter(bus => bus.route === routeName);
    
    // Update markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    filteredBuses.forEach(bus => {
        const color = bus.status === 'active' ? '#22c55e' : bus.status === 'delayed' ? '#f59e0b' : '#ef4444';
        
        const marker = L.circleMarker([bus.lat, bus.lng], {
            radius: 10,
            fillColor: color,
            color: '#fff',
            weight: 3,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);
        
        markers.push(marker);
    });
    
    // Center map on filtered buses
    if (markers.length > 0) {
        centerMapOnAllBuses();
    }
}

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        // Simulate real-time updates
        updateBusMarkers();
        
        // Update stats
        const statsElements = document.querySelectorAll('.text-2xl');
        statsElements.forEach(el => {
            const currentValue = parseInt(el.textContent);
            if (!isNaN(currentValue) && Math.random() > 0.7) {
                // Randomly update some values
                const change = Math.random() > 0.5 ? 1 : -1;
                el.textContent = Math.max(0, currentValue + change);
            }
        });
    }, 10000); // Update every 10 seconds
}

function toggleAutoRefresh() {
    const btn = document.getElementById('autoRefreshBtn');
    
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
        btn.innerHTML = '<i data-lucide="refresh-cw" class="w-4 h-4"></i><span>Auto Refresh: OFF</span>';
        btn.className = 'px-4 py-2 bg-gray-500/10 border border-gray-500/20 text-gray-400 rounded-lg hover:bg-gray-500/20 transition-colors flex items-center space-x-2';
    } else {
        startAutoRefresh();
        btn.innerHTML = '<i data-lucide="refresh-cw" class="w-4 h-4"></i><span>Auto Refresh: ON</span>';
        btn.className = 'px-4 py-2 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg hover:bg-green-500/20 transition-colors flex items-center space-x-2';
    }
    
    lucide.createIcons();
}

function sendBroadcastMessage() {
    document.getElementById('broadcastModal').classList.remove('hidden');
}

function closeBroadcastModal() {
    document.getElementById('broadcastModal').classList.add('hidden');
}

function sendBroadcast(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const message = formData.get('message');
    const target = formData.get('target');
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;
    
    // Simulate sending
    setTimeout(() => {
        // Show success
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg z-50';
        successDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Message broadcasted successfully!';
        document.body.appendChild(successDiv);
        lucide.createIcons();
        
        // Reset and close
        event.target.reset();
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        closeBroadcastModal();
        
        // Remove message after 3 seconds
        setTimeout(() => successDiv.remove(), 3000);
    }, 1500);
}

function showEmergencyAlert() {
    if (confirm('Are you sure you want to send an emergency alert to all buses?')) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'fixed top-4 right-4 bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg z-50';
        alertDiv.innerHTML = '<i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>Emergency alert sent to all buses!';
        document.body.appendChild(alertDiv);
        lucide.createIcons();
        
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

function generateTrackingReport() {
    window.open('generate_permit.php?report=tracking', '_blank');
}

function exportTrackingData() {
    window.open('generate_permit.php?export=tracking', '_blank');
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if Leaflet is loaded
    if (typeof L !== 'undefined') {
        initializeMap();
    } else {
        // Wait for Leaflet to load
        setTimeout(function() {
            if (typeof L !== 'undefined') {
                initializeMap();
            } else {
                // Fallback - create a simple map placeholder
                const mapElement = document.getElementById('liveMap');
                if (mapElement) {
                    mapElement.innerHTML = `
                        <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg">
                            <div class="text-center">
                                <i data-lucide="map" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-gray-600">Map loading...</p>
                                <button onclick="loadMapScript()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Load Map
                                </button>
                            </div>
                        </div>
                    `;
                    lucide.createIcons();
                }
            }
        }, 1000);
    }
});

function loadMapScript() {
    // Load Leaflet dynamically
    const leafletCSS = document.createElement('link');
    leafletCSS.rel = 'stylesheet';
    leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(leafletCSS);
    
    const leafletJS = document.createElement('script');
    leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    leafletJS.onload = function() {
        initializeMap();
    };
    document.head.appendChild(leafletJS);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
