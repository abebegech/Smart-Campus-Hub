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
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-700 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">System Status</p>
                        <p class="text-green-400 font-semibold">Online</p>
                    </div>
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-700 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Database</p>
                        <p class="text-blue-400 font-semibold">Connected</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="database" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-700 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Last Backup</p>
                        <p class="text-yellow-400 font-semibold">2 hours ago</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Configuration -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">System Configuration</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">System Name</label>
                <input type="text" value="Smart Campus Hub" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Time Zone</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option>UTC-05:00 (Eastern)</option>
                    <option>UTC-06:00 (Central)</option>
                    <option>UTC-07:00 (Mountain)</option>
                    <option>UTC-08:00 (Pacific)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Auto Backup</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option>Every 6 hours</option>
                    <option>Every 12 hours</option>
                    <option>Daily</option>
                    <option>Weekly</option>
                </select>
            </div>
        </div>
        
        <div class="flex space-x-3 mt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Save Configuration
            </button>
            <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Reset to Default
            </button>
        </div>
    </div>
    
    <!-- User Management -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">User Management</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Add New User</label>
                <input type="email" placeholder="Email address" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">User Role</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option>Student</option>
                    <option>Driver</option>
                    <option>Administrator</option>
                </select>
            </div>
        </div>
        
        <div class="flex space-x-3 mt-4">
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Add User
            </button>
            <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                View All Users
            </button>
        </div>
    </div>
    
    <!-- Database Management -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Database Management</h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                <div>
                    <p class="text-white font-medium">Database Size</p>
                    <p class="text-gray-400 text-sm">245.7 MB</p>
                </div>
                <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    Optimize
                </button>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                <div>
                    <p class="text-white font-medium">Last Backup</p>
                    <p class="text-gray-400 text-sm">2024-04-23 06:00:00</p>
                </div>
                <button class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    Backup Now
                </button>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                <div>
                    <p class="text-white font-medium">System Logs</p>
                    <p class="text-gray-400 text-sm">1,247 entries</p>
                </div>
                <button class="px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                    View Logs
                </button>
            </div>
        </div>
    </div>
</div>
