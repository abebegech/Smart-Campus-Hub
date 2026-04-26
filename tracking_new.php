<?php
// Live Tracking Page Content
?>
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">Live Vehicle Tracking</h1>
    
    <!-- Map Container -->
    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
        <div id="liveMap" class="w-full h-96 rounded-lg"></div>
        
        <!-- Map Legend -->
        <div class="flex items-center space-x-6 text-sm mt-4">
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
    
    <!-- Vehicle List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-lucide="bus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold">BUS-001</p>
                        <p class="text-sm text-gray-400">Campus Shuttle A</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">25 km/h</div>
                    <div class="text-xs text-green-400">Active</div>
                </div>
            </div>
            <div class="text-sm text-gray-400">Route: Main Campus Loop</div>
            <div class="text-sm text-gray-400">Driver: Mike Johnson</div>
            <div class="text-sm text-gray-400">Passengers: 35/50</div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-lucide="bus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold">BUS-002</p>
                        <p class="text-sm text-gray-400">Engineering Route</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">30 km/h</div>
                    <div class="text-xs text-green-400">Active</div>
                </div>
            </div>
            <div class="text-sm text-gray-400">Route: Engineering Building</div>
            <div class="text-sm text-gray-400">Driver: Sarah Wilson</div>
            <div class="text-sm text-gray-400">Passengers: 48/50</div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i data-lucide="bus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold">BUS-003</p>
                        <p class="text-sm text-gray-400">Library Express</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">0 km/h</div>
                    <div class="text-xs text-yellow-400">Idle</div>
                </div>
            </div>
            <div class="text-sm text-gray-400">Route: Library Route</div>
            <div class="text-sm text-gray-400">Driver: David Chen</div>
            <div class="text-sm text-gray-400">Passengers: 20/50</div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i data-lucide="bus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold">BUS-004</p>
                        <p class="text-sm text-gray-400">Sports Complex</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">0 km/h</div>
                    <div class="text-xs text-red-400">Maintenance</div>
                </div>
            </div>
            <div class="text-sm text-gray-400">Route: Maintenance Garage</div>
            <div class="text-sm text-gray-400">Driver: Tom Davis</div>
            <div class="text-sm text-gray-400">Passengers: 0/50</div>
        </div>
    </div>
</div>

<script>
// Initialize map
let map = L.map('liveMap').setView([40.7128, -74.0060], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

// Sample vehicle locations
const vehicles = [
    {id: 'BUS-001', lat: 40.7128, lng: -74.0060, name: 'Campus Shuttle A', status: 'active'},
    {id: 'BUS-002', lat: 40.7150, lng: -74.0080, name: 'Engineering Route', status: 'active'},
    {id: 'BUS-003', lat: 40.7100, lng: -74.0040, name: 'Library Express', status: 'idle'},
    {id: 'BUS-004', lat: 40.7080, lng: -74.0020, name: 'Sports Complex', status: 'maintenance'}
];

// Add markers
const markers = {};
vehicles.forEach(vehicle => {
    const color = vehicle.status === 'active' ? '#10b981' : 
                 vehicle.status === 'idle' ? '#f59e0b' : '#ef4444';
    
    const marker = L.circleMarker([vehicle.lat, vehicle.lng], {
        radius: 15,
        fillColor: color,
        color: '#fff',
        weight: 2,
        opacity: 0.8
    }).addTo(map)
    .bindPopup(`<b>${vehicle.name}</b><br>Status: ${vehicle.status}<br>ID: ${vehicle.id}`);
    
    markers[vehicle.id] = marker;
});

// Real-time location updates
function updateVehicleLocations() {
    fetch('get_locations_new.php')
        .then(response => response.json())
        .then(data => {
            if (data.vehicles) {
                data.vehicles.forEach(vehicle => {
                    if (markers[vehicle.id]) {
                        const newLatLng = [vehicle.lat, vehicle.lng];
                        markers[vehicle.id].setLatLng(newLatLng);
                        
                        // Update popup content
                        const color = vehicle.status === 'active' ? '#10b981' : 
                                     vehicle.status === 'idle' ? '#f59e0b' : '#ef4444';
                        markers[vehicle.id].setStyle({
                            fillColor: color,
                            color: '#fff',
                            weight: 2,
                            opacity: 0.8
                        });
                        
                        markers[vehicle.id].setPopupContent(
                            `<b>${vehicle.name}</b><br>Status: ${vehicle.status}<br>ID: ${vehicle.id}<br>Speed: ${vehicle.speed || 0} km/h`
                        );
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error updating vehicle locations:', error);
        });
}

// Auto-refresh every 5 seconds
setInterval(updateVehicleLocations, 5000);

// Initial load
updateVehicleLocations();
</script>
