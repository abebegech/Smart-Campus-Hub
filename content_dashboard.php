<?php
// Dashboard content with tracking, permits, and PDF functionality
session_start();
require_once 'config.php';

// Get user role for conditional display
$userRole = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? null;

// Sample data (in real app, this would come from database)
$stats = [
    'total_buses' => 12,
    'active_buses' => 8,
    'total_permits' => 245,
    'pending_permits' => 12,
    'today_trips' => 156,
    'active_users' => 892
];

$recentActivity = [
    ['type' => 'permit', 'message' => 'New permit request from John Doe', 'time' => '2 mins ago', 'icon' => 'ticket'],
    ['type' => 'bus', 'message' => 'Bus A-42 completed route', 'time' => '15 mins ago', 'icon' => 'check-circle'],
    ['type' => 'alert', 'message' => 'Bus B-15 delayed by 10 mins', 'time' => '1 hour ago', 'icon' => 'alert-triangle'],
    ['type' => 'permit', 'message' => 'Permit approved for Jane Smith', 'time' => '2 hours ago', 'icon' => 'check-circle']
];

$activeBuses = [
    ['id' => 'A-42', 'driver' => 'Mike Johnson', 'route' => 'Campus Loop', 'status' => 'active', 'passengers' => 28, 'capacity' => 40, 'lat' => 40.7128, 'lng' => -74.0060],
    ['id' => 'B-15', 'driver' => 'Sarah Davis', 'route' => 'Express Route', 'status' => 'delayed', 'passengers' => 35, 'capacity' => 40, 'lat' => 40.7580, 'lng' => -73.9855],
    ['id' => 'C-23', 'driver' => 'Tom Wilson', 'route' => 'Night Service', 'status' => 'active', 'passengers' => 15, 'capacity' => 40, 'lat' => 40.7489, 'lng' => -73.9680]
];
?>

<div class="space-y-6 animate-fade-in">
    <!-- Welcome Section -->
    <div class="glass rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">Welcome back, <?php echo ucfirst($userRole); ?>!</h2>
                <p class="text-dark-text-secondary">Here's what's happening with your transport system today.</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="showQuickVerifyModal()" class="px-4 py-2 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors flex items-center space-x-2">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                    <span>Quick Verify</span>
                </button>
                <button onclick="generateReport()" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all flex items-center space-x-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Generate Report</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="bus" class="w-8 h-8 text-primary"></i>
                <span class="text-xs text-green-400 bg-green-400/20 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $stats['total_buses']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Total Buses</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="activity" class="w-8 h-8 text-green-400"></i>
                <span class="text-xs text-green-400 bg-green-400/20 px-2 py-1 rounded-full">Live</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $stats['active_buses']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Active Buses</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="ticket" class="w-8 h-8 text-blue-400"></i>
                <span class="text-xs text-blue-400 bg-blue-400/20 px-2 py-1 rounded-full">+5%</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $stats['total_permits']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Total Permits</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="clock" class="w-8 h-8 text-yellow-400"></i>
                <span class="text-xs text-yellow-400 bg-yellow-400/20 px-2 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $stats['pending_permits']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Pending Permits</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="trending-up" class="w-8 h-8 text-purple-400"></i>
                <span class="text-xs text-purple-400 bg-purple-400/20 px-2 py-1 rounded-full">+8%</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $stats['today_trips']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Today's Trips</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="users" class="w-8 h-8 text-indigo-400"></i>
                <span class="text-xs text-indigo-400 bg-indigo-400/20 px-2 py-1 rounded-full">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $stats['active_users']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Active Users</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Active Buses Section -->
        <div class="lg:col-span-2 space-y-4">
            <div class="glass rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-primary"></i>
                        <span>Active Buses</span>
                    </h3>
                    <button onclick="refreshBusData()" class="text-primary hover:text-primary/80 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <?php foreach ($activeBuses as $bus): ?>
                    <div class="glass-dark rounded-lg p-4 hover:bg-white/5 transition-colors cursor-pointer" onclick="focusOnBus('<?php echo $bus['id']; ?>')">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold"><?php echo $bus['id']; ?></span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white"><?php echo $bus['driver']; ?></h4>
                                    <p class="text-sm text-dark-text-secondary"><?php echo $bus['route']; ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    <?php echo $bus['status'] === 'active' ? 'bg-green-400/20 text-green-400' : 'bg-yellow-400/20 text-yellow-400'; ?>">
                                    <span class="w-2 h-2 bg-current rounded-full mr-1 animate-pulse"></span>
                                    <?php echo ucfirst($bus['status']); ?>
                                </span>
                                <p class="text-sm text-dark-text-secondary mt-1"><?php echo $bus['passengers']; ?>/<?php echo $bus['capacity']; ?> passengers</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="w-full bg-dark-border rounded-full h-2">
                                <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full transition-all duration-300" 
                                     style="width: <?php echo ($bus['passengers'] / $bus['capacity']) * 100; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Live Map Preview -->
            <div class="glass rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
                        <i data-lucide="map" class="w-5 h-5 text-primary"></i>
                        <span>Live Map</span>
                    </h3>
                    <button onclick="expandMap()" class="text-primary hover:text-primary/80 transition-colors">
                        <i data-lucide="maximize" class="w-4 h-4"></i>
                    </button>
                </div>
                <div id="dashboardMap" class="h-64 bg-dark-bg rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i data-lucide="map" class="w-12 h-12 text-dark-text-secondary mx-auto mb-2"></i>
                        <p class="text-dark-text-secondary">Interactive map loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="space-y-4">
            <!-- Recent Activity -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="activity" class="w-5 h-5 text-primary"></i>
                    <span>Recent Activity</span>
                </h3>
                <div class="space-y-3">
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-white/5 transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                            <?php 
                            $colorClasses = [
                                'permit' => 'bg-blue-400/20 text-blue-400',
                                'bus' => 'bg-green-400/20 text-green-400',
                                'alert' => 'bg-yellow-400/20 text-yellow-400'
                            ];
                            echo $colorClasses[$activity['type']] ?? 'bg-gray-400/20 text-gray-400';
                            ?>">
                            <i data-lucide="<?php echo $activity['icon']; ?>" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white"><?php echo $activity['message']; ?></p>
                            <p class="text-xs text-dark-text-secondary"><?php echo $activity['time']; ?></p>
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
                    <button onclick="loadPage('permits')" class="w-full px-4 py-3 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="ticket" class="w-4 h-4"></i>
                        <span>Manage Permits</span>
                    </button>
                    <button onclick="loadPage('tracking')" class="w-full px-4 py-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg hover:bg-green-500/20 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>View Tracking</span>
                    </button>
                    <button onclick="showNotifications()" class="w-full px-4 py-3 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/20 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        <span>Notifications</span>
                    </button>
                    <button onclick="generatePDFReport()" class="w-full px-4 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all flex items-center justify-center space-x-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Generate PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Verify Modal -->
<div id="quickVerifyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-dark rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Quick Permit Verify</h3>
            <button onclick="closeQuickVerifyModal()" class="text-dark-text-secondary hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form onsubmit="quickVerifyPermit(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-dark-text-secondary mb-2">Transaction ID</label>
                <input type="text" id="quickVerifyTxn" required 
                       placeholder="Enter Transaction ID (e.g., TXN2025041712345678)"
                       class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary
                              placeholder-dark-text-secondary focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                    Verify Permit
                </button>
                <button type="button" onclick="closeQuickVerifyModal()" class="flex-1 px-4 py-3 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
        <div id="quickVerifyResult" class="mt-4 p-3 rounded-lg hidden"></div>
    </div>
</div>

<script>
// Initialize dashboard functionality
function initializeDashboard() {
    // Initialize mini map
    setTimeout(() => {
        const mapElement = document.getElementById('dashboardMap');
        if (mapElement && typeof L !== 'undefined') {
            const map = L.map('dashboardMap').setView([40.7128, -74.0060], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add bus markers
            const buses = <?php echo json_encode($activeBuses); ?>;
            buses.forEach(bus => {
                const marker = L.marker([bus.lat, bus.lng]).addTo(map);
                marker.bindPopup(`<b>Bus ${bus.id}</b><br>Driver: ${bus.driver}<br>Route: ${bus.route}<br>Status: ${bus.status}`);
            });
        }
    }, 1000);
}

// Quick verify functions
function showQuickVerifyModal() {
    document.getElementById('quickVerifyModal').classList.remove('hidden');
    document.getElementById('quickVerifyTxn').focus();
}

function closeQuickVerifyModal() {
    document.getElementById('quickVerifyModal').classList.add('hidden');
    document.getElementById('quickVerifyResult').classList.add('hidden');
    document.getElementById('quickVerifyTxn').value = '';
}

function quickVerifyPermit(event) {
    event.preventDefault();
    const txnId = document.getElementById('quickVerifyTxn').value;
    const resultDiv = document.getElementById('quickVerifyResult');
    
    resultDiv.classList.remove('hidden');
    resultDiv.className = 'mt-4 p-3 rounded-lg bg-blue-500/20 border border-blue-500/50 text-blue-300 text-sm';
    resultDiv.innerHTML = '<div class="flex items-center space-x-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Verifying permit...</span></div>';
    lucide.createIcons();
    
    // Simulate verification
    setTimeout(() => {
        if (txnId.startsWith('TXN')) {
            resultDiv.className = 'mt-4 p-3 rounded-lg bg-green-500/20 border border-green-500/50 text-green-300 text-sm';
            resultDiv.innerHTML = `
                <div class="flex items-start space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4 mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Permit Verified Successfully</p>
                        <p class="text-xs mt-1">Transaction ID: ${txnId}</p>
                        <p class="text-xs mt-1">Status: Active | Valid until: Dec 31, 2026</p>
                    </div>
                </div>
            `;
        } else {
            resultDiv.className = 'mt-4 p-3 rounded-lg bg-red-500/20 border border-red-500/50 text-red-300 text-sm';
            resultDiv.innerHTML = `
                <div class="flex items-start space-x-2">
                    <i data-lucide="x-circle" class="w-4 h-4 mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Permit Not Found</p>
                        <p class="text-xs mt-1">Please check the Transaction ID and try again.</p>
                    </div>
                </div>
            `;
        }
        lucide.createIcons();
    }, 1500);
}

// Other dashboard functions
function focusOnBus(busId) {
    // Focus on specific bus in tracking view
    loadPage('tracking');
    setTimeout(() => {
        if (typeof focusOnBusInMap === 'function') {
            focusOnBusInMap(busId);
        }
    }, 500);
}

function refreshBusData() {
    // Refresh bus data
    location.reload();
}

function expandMap() {
    // Open full tracking view
    loadPage('tracking');
}

function generateReport() {
    // Generate PDF report
    window.open('generate_permit.php?report=dashboard', '_blank');
}

function generatePDFReport() {
    // Generate PDF report
    window.open('generate_permit.php?report=summary', '_blank');
}

// Auto-refresh dashboard data
setInterval(() => {
    // Update stats periodically
    const statsElements = document.querySelectorAll('.text-2xl');
    statsElements.forEach(el => {
        const currentValue = parseInt(el.textContent);
        if (!isNaN(currentValue)) {
            // Simulate small changes
            const change = Math.random() > 0.5 ? 1 : 0;
            el.textContent = currentValue + change;
        }
    });
}, 30000); // Update every 30 seconds
</script>
