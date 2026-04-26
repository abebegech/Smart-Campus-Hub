<?php
// Dashboard Page Content
?>
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">Dashboard</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Buses</p>
                    <p class="text-2xl font-bold text-white">4</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="bus" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Active Routes</p>
                    <p class="text-2xl font-bold text-white">12</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="map" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Active Users</p>
                    <p class="text-2xl font-bold text-white">247</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
        <h2 class="text-xl font-semibold mb-4 text-white">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if (hasPermission('permits')): ?>
            <button onclick="window.location.href='main_new.php?page=permits'" 
                    class="w-full p-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Request New Permit</span>
            </button>
            <?php endif; ?>
            
            <?php if (hasPermission('tracking')): ?>
            <button onclick="window.location.href='main_new.php?page=tracking'" 
                    class="w-full p-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
                <span>View Live Tracking</span>
            </button>
            <?php endif; ?>
            
            <?php if (hasPermission('gps_broadcast')): ?>
            <button onclick="window.location.href='main_new.php?page=gps_broadcast'" 
                    class="w-full p-4 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="radio" class="w-5 h-5"></i>
                <span>Start GPS Broadcast</span>
            </button>
            <?php endif; ?>
            
            <?php if (hasPermission('system')): ?>
            <button onclick="window.location.href='main_new.php?page=system'" 
                    class="w-full p-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span>System Settings</span>
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
        <h2 class="text-xl font-semibold mb-4 text-white">Recent Activity</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i data-lucide="bus" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Campus Shuttle A departed</p>
                        <p class="text-gray-400 text-sm">Main Campus Loop</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">2 mins ago</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">New user registration</p>
                        <p class="text-gray-400 text-sm">Student account created</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">15 mins ago</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">Permit approved</p>
                        <p class="text-gray-400 text-sm">Student parking permit</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">1 hour ago</span>
            </div>
        </div>
    </div>
</div>
