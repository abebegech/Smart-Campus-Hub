<?php
// GPS Broadcast Page Content
?>
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">GPS Broadcast</h1>
    
    <!-- Driver Status -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">GPS Broadcast Status</h3>
            <div class="flex items-center space-x-2">
                <span id="tripStatus" class="px-3 py-1 bg-gray-700 rounded-full text-sm">No active trip</span>
                <button onclick="toggleTrip()" id="tripToggle" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="play" class="w-4 h-4 inline mr-2"></i>
                    Start Trip
                </button>
            </div>
        </div>
        
        <div class="text-center">
            <p class="text-gray-400 mb-4">Current Location Status</p>
            <div class="flex items-center justify-center space-x-4">
                <div class="text-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <p class="text-green-400 text-sm mt-2">GPS Active</p>
                </div>
                <div class="text-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <p class="text-blue-400 text-sm mt-2">Broadcasting</p>
                </div>
                <div class="text-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <p class="text-yellow-400 text-sm mt-2">Signal Weak</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Location Details -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h3 class="text-lg font-semibold mb-4">Current Location</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">Latitude</p>
                <p class="text-white font-mono" id="latitude">--</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Longitude</p>
                <p class="text-white font-mono" id="longitude">--</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Last Update</p>
                <p class="text-white" id="lastUpdate">--</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Accuracy</p>
                <p class="text-white" id="accuracy">--</p>
            </div>
        </div>
    </div>
    
    <!-- Trip History -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h3 class="text-lg font-semibold mb-4">Today's Trip History</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Main Campus Loop</p>
                        <p class="text-gray-400 text-sm">45 min</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400">08:30 AM</span>
                    <span class="text-xs text-green-400">Completed</span>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Engineering Building</p>
                        <p class="text-gray-400 text-sm">20 min</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400">10:15 AM</span>
                    <span class="text-xs text-green-400">Completed</span>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Library Route</p>
                        <p class="text-gray-400 text-sm">30 min</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400">02:45 PM</span>
                    <span class="text-xs text-blue-400">In Progress</span>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                        <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Sports Complex</p>
                        <p class="text-gray-400 text-sm">25 min</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400">04:30 PM</span>
                    <span class="text-xs text-gray-400">Scheduled</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Broadcast Settings -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h3 class="text-lg font-semibold mb-4">Broadcast Settings</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Update Interval</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option value="5">5 seconds</option>
                    <option value="10">10 seconds</option>
                    <option value="30">30 seconds</option>
                    <option value="60">1 minute</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Route Information</label>
                <input type="text" placeholder="Current route name" 
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Vehicle ID</label>
                <input type="text" value="BUS-<?php echo $_SESSION['user_id'] ?? 'UNKNOWN'; ?>" readonly
                       class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white">
            </div>
        </div>
    </div>
</div>

<script>
let watchId = null;
let isTracking = false;

function toggleTrip() {
    const toggle = document.getElementById('tripToggle');
    const status = document.getElementById('tripStatus');
    
    if (!isTracking) {
        startGPSTracking();
        isTracking = true;
        toggle.innerHTML = '<i data-lucide="stop" class="w-4 h-4 inline mr-2"></i>End Trip';
        toggle.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors';
        status.textContent = 'Trip in progress...';
        status.className = 'px-3 py-1 bg-blue-600 rounded-full text-sm';
    } else {
        stopGPSTracking();
        isTracking = false;
        toggle.innerHTML = '<i data-lucide="play" class="w-4 h-4 inline mr-2"></i>Start Trip';
        toggle.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
        status.textContent = 'Trip ended';
        status.className = 'px-3 py-1 bg-gray-600 rounded-full text-sm';
    }
    
    // Re-initialize Lucide icons
    lucide.createIcons();
}

function startGPSTracking() {
    if (navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(
            position => {
                updateLocationDisplay(position);
                sendLocationToServer(position.coords.latitude, position.coords.longitude, position.coords.accuracy);
            },
            error => {
                console.error('GPS Error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    }
}

function stopGPSTracking() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
}

function updateLocationDisplay(position = null) {
    if (position) {
        document.getElementById('latitude').textContent = position.coords.latitude.toFixed(6);
        document.getElementById('longitude').textContent = position.coords.longitude.toFixed(6);
        document.getElementById('accuracy').textContent = position.coords.accuracy.toFixed(2) + ' meters';
    }
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
}

function sendLocationToServer(lat, lng, accuracy) {
    const locationData = {
        driver_id: '<?php echo $_SESSION['user_id'] ?? ''; ?>',
        latitude: lat,
        longitude: lng,
        accuracy: accuracy,
        timestamp: new Date().toISOString(),
        trip_status: isTracking ? 'active' : 'stopped'
    };
    
    fetch('update_location_new.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(locationData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Location updated successfully');
        }
    })
    .catch(error => {
        console.error('Error updating location:', error);
    });
}

// Auto-update location display
setInterval(() => {
    if (isTracking && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => updateLocationDisplay(position),
            error => console.error('GPS Error:', error)
        );
    }
}, 1000);
</script>
