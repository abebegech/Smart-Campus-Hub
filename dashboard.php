<?php
// Dashboard Content Page
require_once '../auth.php';
$currentUser = getCurrentUser();
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">
                    Welcome back, <?php echo $currentUser['name']; ?>!
                </h2>
                <p class="text-gray-400">
                    Here's your transport management overview
                </p>
            </div>
            <div class="text-right">
                <span class="text-sm text-gray-400">
                    <?php echo date('l, F j, Y'); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Buses</p>
                    <p class="text-2xl font-bold text-white">4</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <i data-lucide="bus" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Active Routes</p>
                    <p class="text-2xl font-bold text-white">12</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i data-lucide="map" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Today's Trips</p>
                    <p class="text-2xl font-bold text-white">156</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                    <i data-lucide="navigation" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Active Students</p>
                    <p class="text-2xl font-bold text-white">1,247</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Recent Activity</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">New permit issued</p>
                        <p class="text-gray-400 text-sm">John Doe - Student ID: 2024001</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">2 minutes ago</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Bus location updated</p>
                        <p class="text-gray-400 text-sm">BUS-001 - Main Campus Route</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">5 minutes ago</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Route delay reported</p>
                        <p class="text-gray-400 text-sm">BUS-003 - Engineering Route</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">12 minutes ago</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (hasPermission('permits')): ?>
                <button onclick="loadPage('permits')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-400"></i>
                    <span class="text-white">Issue New Permit</span>
                </button>
            <?php endif; ?>

            <?php if (hasPermission('tracking')): ?>
                <button onclick="loadPage('tracking')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="map-pin" class="w-5 h-5 text-green-400"></i>
                    <span class="text-white">Live Tracking</span>
                </button>
            <?php endif; ?>

            <?php if ($userRole === 'admin' && hasPermission('users')): ?>
                <button onclick="loadPage('users')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="users" class="w-5 h-5 text-purple-400"></i>
                    <span class="text-white">Manage Users</span>
                </button>
            <?php endif; ?>

            <?php if ($userRole === 'admin' && hasPermission('system')): ?>
                <button onclick="loadPage('system')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="settings" class="w-5 h-5 text-yellow-400"></i>
                    <span class="text-white">System Settings</span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function loadPage(page) {
    window.location.href = 'main.php?page=' + page;
}
</script>
