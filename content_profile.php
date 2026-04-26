<?php
// User profile content
session_start();
require_once 'config.php';

$userRole = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? null;

// Sample user data (in real app, this would come from database)
$userData = [
    'id' => $userId ?? 1,
    'name' => 'John Doe',
    'email' => 'john.doe@campus.edu',
    'role' => $userRole,
    'student_id' => 'STU000001',
    'phone' => '+1 (555) 123-4567',
    'department' => 'Computer Science',
    'year' => '3rd Year',
    'join_date' => '2023-09-01',
    'last_login' => '2025-04-20 10:30 AM',
    'profile_picture' => null,
    'notifications' => [
        'email' => true,
        'push' => true,
        'sms' => false
    ],
    'preferences' => [
        'theme' => 'dark',
        'language' => 'en',
        'timezone' => 'UTC-5'
    ]
];

$userStats = [
    'total_trips' => 156,
    'this_month' => 23,
    'favorite_route' => 'Campus Loop',
    'permits_issued' => 3,
    'active_permits' => 1,
    'total_saved' => '$45.50'
];

$recentActivity = [
    ['type' => 'trip', 'message' => 'Completed trip on Campus Loop', 'time' => '2 hours ago', 'icon' => 'map-pin'],
    ['type' => 'permit', 'message' => 'Renewed monthly permit', 'time' => '1 day ago', 'icon' => 'ticket'],
    ['type' => 'login', 'message' => 'Logged in from mobile device', 'time' => '2 days ago', 'icon' => 'log-in'],
    ['type' => 'payment', 'message' => 'Payment of $15.00 processed', 'time' => '3 days ago', 'icon' => 'credit-card']
];
?>

<div class="space-y-6 animate-fade-in">
    <!-- Profile Header -->
    <div class="glass rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">Profile Settings</h2>
                <p class="text-dark-text-secondary">Manage your account settings and preferences</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportProfileData()" class="px-4 py-2 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors flex items-center space-x-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Export Data</span>
                </button>
                <button onclick="showDeleteAccountModal()" class="px-4 py-2 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg hover:bg-red-500/20 transition-colors flex items-center space-x-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Delete Account</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Info and Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                    <span>Basic Information</span>
                </h3>
                <form onsubmit="updateProfile(event)" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark-text-secondary mb-2">Full Name</label>
                            <input type="text" name="name" value="<?php echo $userData['name']; ?>" required 
                                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-text-secondary mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo $userData['email']; ?>" required 
                                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-text-secondary mb-2">Phone</label>
                            <input type="tel" name="phone" value="<?php echo $userData['phone']; ?>" 
                                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-text-secondary mb-2">Department</label>
                            <input type="text" name="department" value="<?php echo $userData['department']; ?>" 
                                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                            Save Changes
                        </button>
                        <button type="button" onclick="resetProfileForm()" class="px-6 py-2 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="shield" class="w-5 h-5 text-primary"></i>
                    <span>Security Settings</span>
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border border-dark-border rounded-lg">
                        <div>
                            <h4 class="text-white font-medium">Change Password</h4>
                            <p class="text-sm text-dark-text-secondary">Update your account password</p>
                        </div>
                        <button onclick="showChangePasswordModal()" class="px-4 py-2 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors">
                            Change
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 border border-dark-border rounded-lg">
                        <div>
                            <h4 class="text-white font-medium">Two-Factor Authentication</h4>
                            <p class="text-sm text-dark-text-secondary">Add an extra layer of security</p>
                        </div>
                        <button onclick="toggle2FA()" class="px-4 py-2 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg hover:bg-green-500/20 transition-colors">
                            Enable
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 border border-dark-border rounded-lg">
                        <div>
                            <h4 class="text-white font-medium">Active Sessions</h4>
                            <p class="text-sm text-dark-text-secondary">Manage your active login sessions</p>
                        </div>
                        <button onclick="showSessionsModal()" class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/20 transition-colors">
                            Manage
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="bell" class="w-5 h-5 text-primary"></i>
                    <span>Notification Preferences</span>
                </h3>
                <form onsubmit="updateNotifications(event)" class="space-y-4">
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-3 border border-dark-border rounded-lg cursor-pointer hover:bg-white/5">
                            <div>
                                <span class="text-white font-medium">Email Notifications</span>
                                <p class="text-sm text-dark-text-secondary">Receive updates via email</p>
                            </div>
                            <input type="checkbox" name="email_notifications" <?php echo $userData['notifications']['email'] ? 'checked' : ''; ?> 
                                   class="w-5 h-5 text-primary bg-dark-bg border-dark-border rounded focus:ring-primary">
                        </label>
                        <label class="flex items-center justify-between p-3 border border-dark-border rounded-lg cursor-pointer hover:bg-white/5">
                            <div>
                                <span class="text-white font-medium">Push Notifications</span>
                                <p class="text-sm text-dark-text-secondary">Receive push notifications</p>
                            </div>
                            <input type="checkbox" name="push_notifications" <?php echo $userData['notifications']['push'] ? 'checked' : ''; ?> 
                                   class="w-5 h-5 text-primary bg-dark-bg border-dark-border rounded focus:ring-primary">
                        </label>
                        <label class="flex items-center justify-between p-3 border border-dark-border rounded-lg cursor-pointer hover:bg-white/5">
                            <div>
                                <span class="text-white font-medium">SMS Notifications</span>
                                <p class="text-sm text-dark-text-secondary">Receive SMS alerts</p>
                            </div>
                            <input type="checkbox" name="sms_notifications" <?php echo $userData['notifications']['sms'] ? 'checked' : ''; ?> 
                                   class="w-5 h-5 text-primary bg-dark-bg border-dark-border rounded focus:ring-primary">
                        </label>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                        Save Preferences
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Profile Card -->
            <div class="glass rounded-xl p-6 text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-primary to-secondary rounded-full mx-auto mb-4 flex items-center justify-center">
                    <span class="text-white text-3xl font-bold"><?php echo strtoupper(substr($userData['name'], 0, 2)); ?></span>
                </div>
                <h3 class="text-xl font-bold text-white"><?php echo $userData['name']; ?></h3>
                <p class="text-dark-text-secondary"><?php echo ucfirst($userData['role']); ?></p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">Student ID:</span>
                        <span class="text-white"><?php echo $userData['student_id']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">Department:</span>
                        <span class="text-white"><?php echo $userData['department']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">Year:</span>
                        <span class="text-white"><?php echo $userData['year']; ?></span>
                    </div>
                </div>
            </div>

            <!-- User Stats -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="bar-chart" class="w-5 h-5 text-primary"></i>
                    <span>Your Statistics</span>
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-dark-text-secondary">Total Trips</span>
                        <span class="text-white font-bold"><?php echo $userStats['total_trips']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-dark-text-secondary">This Month</span>
                        <span class="text-white font-bold"><?php echo $userStats['this_month']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-dark-text-secondary">Favorite Route</span>
                        <span class="text-white font-bold"><?php echo $userStats['favorite_route']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-dark-text-secondary">Active Permits</span>
                        <span class="text-white font-bold"><?php echo $userStats['active_permits']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-dark-text-secondary">Total Saved</span>
                        <span class="text-white font-bold text-green-400"><?php echo $userStats['total_saved']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="glass rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                    <i data-lucide="activity" class="w-5 h-5 text-primary"></i>
                    <span>Recent Activity</span>
                </h3>
                <div class="space-y-3">
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-primary/20 text-primary">
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
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-dark rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Change Password</h3>
            <button onclick="closeChangePasswordModal()" class="text-dark-text-secondary hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form onsubmit="changePassword(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-dark-text-secondary mb-2">Current Password</label>
                <input type="password" name="current_password" required 
                       class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-text-secondary mb-2">New Password</label>
                <input type="password" name="new_password" required 
                       class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-text-secondary mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" required 
                       class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                    Change Password
                </button>
                <button type="button" onclick="closeChangePasswordModal()" class="flex-1 px-4 py-3 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-dark rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white text-red-400">Delete Account</h3>
            <button onclick="closeDeleteAccountModal()" class="text-dark-text-secondary hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4">
                <p class="text-red-300 text-sm">
                    <strong>Warning:</strong> This action cannot be undone. All your data will be permanently deleted.
                </p>
            </div>
            <form onsubmit="deleteAccount(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Type "DELETE" to confirm</label>
                    <input type="text" name="confirm_delete" required 
                           class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        Delete Account
                    </button>
                    <button type="button" onclick="closeDeleteAccountModal()" class="flex-1 px-4 py-3 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Profile management functions
function updateProfile(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg z-50';
        successDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Profile updated successfully!';
        document.body.appendChild(successDiv);
        lucide.createIcons();
        
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Remove message after 3 seconds
        setTimeout(() => successDiv.remove(), 3000);
    }, 1500);
}

function resetProfileForm() {
    document.querySelector('form[onsubmit="updateProfile(event)"]').reset();
}

function showChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
    document.querySelector('#changePasswordModal form').reset();
}

function changePassword(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    if (formData.get('new_password') !== formData.get('confirm_password')) {
        alert('Passwords do not match!');
        return;
    }
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Changing...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg z-50';
        successDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Password changed successfully!';
        document.body.appendChild(successDiv);
        lucide.createIcons();
        
        // Reset and close
        event.target.reset();
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        closeChangePasswordModal();
        
        // Remove message after 3 seconds
        setTimeout(() => successDiv.remove(), 3000);
    }, 1500);
}

function updateNotifications(event) {
    event.preventDefault();
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg z-50';
        successDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Notification preferences saved!';
        document.body.appendChild(successDiv);
        lucide.createIcons();
        
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Remove message after 3 seconds
        setTimeout(() => successDiv.remove(), 3000);
    }, 1500);
}

function toggle2FA() {
    alert('Two-factor authentication setup would open here');
}

function showSessionsModal() {
    alert('Active sessions management would open here');
}

function showDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    document.querySelector('#deleteAccountModal form').reset();
}

function deleteAccount(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    if (formData.get('confirm_delete') !== 'DELETE') {
        alert('Please type "DELETE" to confirm');
        return;
    }
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Deleting...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        alert('Account deletion simulated. In production, this would permanently delete your account.');
        closeDeleteAccountModal();
        
        // Redirect to login
        window.location.href = 'logout.php';
    }, 2000);
}

function exportProfileData() {
    window.open('generate_permit.php?export=profile', '_blank');
}
</script>
