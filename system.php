<?php
// System Settings Page (Admin Only)
require_once '../auth_new.php';
$currentUser = getCurrentUser();
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <!-- System Overview -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">System Overview</h3>
            <span class="text-sm text-gray-400">Last updated: <?php echo date('Y-m-d H:i:s'); ?></span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="users" class="w-8 h-8 text-white"></i>
                </div>
                <p class="text-2xl font-bold text-white">1,247</p>
                <p class="text-gray-400">Total Users</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="credit-card" class="w-8 h-8 text-white"></i>
                </div>
                <p class="text-2xl font-bold text-white">847</p>
                <p class="text-gray-400">Active Permits</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="bus" class="w-8 h-8 text-white"></i>
                </div>
                <p class="text-2xl font-bold text-white">12</p>
                <p class="text-gray-400">Active Vehicles</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="map-pin" class="w-8 h-8 text-white"></i>
                </div>
                <p class="text-2xl font-bold text-white">3,456</p>
                <p class="text-gray-400">Trips Today</p>
            </div>
        </div>
    </div>

    <!-- System Settings -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">System Configuration</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-white font-medium mb-3">General Settings</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">System Name</label>
                        <input type="text" value="Smart Campus Hub" readonly
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Campus Name</label>
                        <input type="text" value="University Campus" readonly
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Time Zone</label>
                        <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                            <option value="UTC-5" selected>Eastern Time (UTC-5)</option>
                            <option value="UTC-8">Pacific Time (UTC-8)</option>
                            <option value="UTC-0">GMT (UTC+0)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-medium mb-3">Notification Settings</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Email Notifications</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:border-transparent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:content-[''] peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">SMS Alerts</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:border-transparent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:content-[''] peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Push Notifications</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:border-transparent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:content-[''] peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">User Management</h3>
        
        <div class="space-y-4">
            <button onclick="loadPage('users')" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                <span class="text-white">Manage All Users</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="exportUsers()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="download" class="w-5 h-5 text-green-400"></i>
                <span class="text-white">Export User Data</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="importUsers()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="upload" class="w-5 h-5 text-purple-400"></i>
                <span class="text-white">Import User Data</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>
    </div>

    <!-- Database Management -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Database Management</h3>
        
        <div class="space-y-4">
            <button onclick="backupDatabase()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="database" class="w-5 h-5 text-yellow-400"></i>
                <span class="text-white">Backup Database</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="restoreDatabase()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="refresh-cw" class="w-5 h-5 text-orange-400"></i>
                <span class="text-white">Restore Database</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="optimizeDatabase()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="zap" class="w-5 h-5 text-purple-400"></i>
                <span class="text-white">Optimize Database</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="clearCache()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="trash-2" class="w-5 h-5 text-red-400"></i>
                <span class="text-white">Clear System Cache</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Security Settings</h3>
        
        <div class="space-y-4">
            <button onclick="viewSecurityLogs()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="shield" class="w-5 h-5 text-red-400"></i>
                <span class="text-white">View Security Logs</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="managePermissions()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="key" class="w-5 h-5 text-yellow-400"></i>
                <span class="text-white">Manage Permissions</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
            
            <button onclick="configureTwoFactor()" 
                    class="w-full p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="smartphone" class="w-5 h-5 text-blue-400"></i>
                <span class="text-white">Configure 2FA</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>
    </div>
</div>

<script>
function loadPage(page) {
    window.location.href = 'main.php?page=' + page;
}

function exportUsers() {
    window.open('export_users.php', '_blank');
}

function importUsers() {
    window.open('import_users.php', '_blank');
}

function backupDatabase() {
    if (confirm('Are you sure you want to backup the database? This may take a few minutes.')) {
        window.open('backup_database.php', '_blank');
    }
}

function restoreDatabase() {
    if (confirm('Are you sure you want to restore the database? This will overwrite current data.')) {
        window.open('restore_database.php', '_blank');
    }
}

function optimizeDatabase() {
    if (confirm('Are you sure you want to optimize the database? This may temporarily slow down the system.')) {
        window.open('optimize_database.php', '_blank');
    }
}

function clearCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        window.open('clear_cache.php', '_blank');
    }
}

function viewSecurityLogs() {
    window.open('security_logs.php', '_blank');
}

function managePermissions() {
    window.open('manage_permissions.php', '_blank');
}

function configureTwoFactor() {
    window.open('configure_2fa.php', '_blank');
}
</script>
