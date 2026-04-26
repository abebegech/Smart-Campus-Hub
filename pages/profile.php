<?php
// Profile Page
require_once '../auth.php';
$currentUser = getCurrentUser();
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <!-- User Profile Card -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center space-x-6 mb-6">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-10 h-10 text-white"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white"><?php echo $currentUser['name']; ?></h2>
                <p class="text-gray-400"><?php echo ucfirst($userRole); ?></p>
                <p class="text-sm text-gray-500"><?php echo $currentUser['email']; ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-400 text-sm mb-1">User ID</p>
                <p class="text-white font-medium"><?php echo $currentUser['id']; ?></p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Department</p>
                <p class="text-white font-medium">
                    <?php 
                    echo $userRole === 'admin' ? 'System Administration' : 
                         ($userRole === 'driver' ? 'Transportation' : 'Student Services');
                    ?>
                </p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Member Since</p>
                <p class="text-white font-medium"><?php echo date('F j, Y', $currentUser['login_time']); ?></p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Last Login</p>
                <p class="text-white font-medium"><?php echo date('M j, Y H:i', $currentUser['login_time']); ?></p>
            </div>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Account Settings</h3>
        
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Display Name</label>
                <input type="text" value="<?php echo $currentUser['name']; ?>" readonly
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                <input type="email" value="<?php echo $currentUser['email']; ?>" readonly
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                <input type="password" placeholder="Enter current password"
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                <input type="password" placeholder="Enter new password"
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                <input type="password" placeholder="Confirm new password"
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Password
                </button>
                <button type="button" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Preferences -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Preferences</h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-medium">Email Notifications</p>
                    <p class="text-gray-400 text-sm">Receive updates about your permits and trips</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:border-transparent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:content-[''] peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-medium">SMS Alerts</p>
                    <p class="text-gray-400 text-sm">Get instant alerts for route changes</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:border-transparent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:content-[''] peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-medium">Dark Mode</p>
                    <p class="text-gray-400 text-sm">Use dark theme across all pages</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:border-transparent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all after:content-[''] peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Language</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option value="en" selected>English</option>
                    <option value="es">Español</option>
                    <option value="fr">Français</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if (hasPermission('permits')): ?>
                <button onclick="loadPage('permits')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-400"></i>
                    <span class="text-white">View My Permits</span>
                </button>
            <?php endif; ?>

            <?php if (hasPermission('tracking')): ?>
                <button onclick="loadPage('tracking')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="map-pin" class="w-5 h-5 text-green-400"></i>
                    <span class="text-white">Live Tracking</span>
                </button>
            <?php endif; ?>

            <?php if ($userRole === 'admin' && hasPermission('system')): ?>
                <button onclick="loadPage('system')" 
                        class="flex items-center space-x-2 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                    <i data-lucide="settings" class="w-5 h-5 text-purple-400"></i>
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
