<?php
// User Management Page (Admin Only)
// Temporarily bypass auth for testing
$currentUser = [
    'name' => 'System Administrator',
    'role' => 'admin',
    'email' => 'admin@campus.com'
];
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">User Management</h1>
    
    <!-- Add New User -->
    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
        <h2 class="text-lg font-semibold mb-4 text-white">Add New User</h2>
        <form id="userForm" class="space-y-4">
            <input type="hidden" id="userId" name="user_id" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="user@campus.com" 
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" 
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">User Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="driver">Driver</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" 
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                    <small class="text-gray-400 text-xs">Leave blank to keep existing password</small>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                    Add User
                </button>
                <button type="button" onclick="resetForm()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Clear Form
                </button>
            </div>
        </form>
    </div>
    
    <!-- User List -->
    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
        <h2 class="text-lg font-semibold mb-4 text-white">Current Users</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-white">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Last Login</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <!-- Users will be loaded here via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-gray-400" id="userCount">
                Loading users...
            </div>
        </div>
    </div>
    
    <!-- User Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Users</p>
                    <p class="text-2xl font-bold text-white" id="totalUsers">0</p>
                </div>
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Active Today</p>
                    <p class="text-2xl font-bold text-white" id="activeUsers">0</p>
                </div>
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">New This Week</p>
                    <p class="text-2xl font-bold text-white" id="newUsers">0</p>
                </div>
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentUsers = [];
let editingUserId = null;

// Load users when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});

// Form submission
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    saveUser();
});

function loadUsers() {
    fetch('../user_management_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=read'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentUsers = data.data;
            displayUsers(data.data);
            updateStatistics(data.data);
        } else {
            console.error('Failed to load users:', data.error);
            // Fallback to sample data
            loadSampleUsers();
        }
    })
    .catch(error => {
        console.error('Error loading users:', error);
        // Fallback to sample data
        loadSampleUsers();
    });
}

function loadSampleUsers() {
    const sampleUsers = [
        {user_id: 1, email: 'admin@campus.com', name: 'System Administrator', role: 'admin', status: 'active', created_at: '2024-01-01 00:00:00', last_login: '2024-04-23 08:00:00'},
        {user_id: 2, email: 'student@campus.com', name: 'Student User', role: 'student', status: 'active', created_at: '2024-02-15 00:00:00', last_login: '2024-04-23 07:30:00'},
        {user_id: 3, email: 'driver@campus.com', name: 'Driver User', role: 'driver', status: 'active', created_at: '2024-03-10 00:00:00', last_login: '2024-04-23 06:45:00'}
    ];
    currentUsers = sampleUsers;
    displayUsers(sampleUsers);
    updateStatistics(sampleUsers);
}

function displayUsers(users) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-700 hover:bg-gray-700';
        
        const roleBadge = getRoleBadge(user.role);
        const statusBadge = getStatusBadge(user.status);
        
        row.innerHTML = `
            <td class="px-4 py-3">${user.email}</td>
            <td class="px-4 py-3">${user.name}</td>
            <td class="px-4 py-3">${roleBadge}</td>
            <td class="px-4 py-3">${statusBadge}</td>
            <td class="px-4 py-3">${user.last_login ? formatDate(user.last_login) : 'Never'}</td>
            <td class="px-4 py-3">
                <div class="flex space-x-2">
                    <button onclick="editUser(${user.user_id})" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                        Edit
                    </button>
                    <button onclick="toggleUserStatus(${user.user_id})" class="px-2 py-1 ${user.status === 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'} text-white text-xs rounded">
                        ${user.status === 'active' ? 'Deactivate' : 'Activate'}
                    </button>
                    <button onclick="deleteUser(${user.user_id})" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    document.getElementById('userCount').textContent = `Showing 1-${users.length} of ${users.length} users`;
}

function getRoleBadge(role) {
    const badges = {
        'admin': '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Admin</span>',
        'student': '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Student</span>',
        'driver': '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Driver</span>'
    };
    return badges[role] || '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Unknown</span>';
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>',
        'inactive': '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactive</span>'
    };
    return badges[status] || '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Unknown</span>';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

function updateStatistics(users) {
    document.getElementById('totalUsers').textContent = users.length;
    
    const activeToday = users.filter(user => {
        if (!user.last_login) return false;
        const lastLogin = new Date(user.last_login);
        const today = new Date();
        return lastLogin.toDateString() === today.toDateString();
    }).length;
    document.getElementById('activeUsers').textContent = activeToday;
    
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
    const newThisWeek = users.filter(user => new Date(user.created_at) > oneWeekAgo).length;
    document.getElementById('newUsers').textContent = newThisWeek;
}

function saveUser() {
    const formData = new FormData(document.getElementById('userForm'));
    const action = editingUserId ? 'update' : 'create';
    
    formData.append('action', action);
    
    fetch('../user_management_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            resetForm();
            loadUsers();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error saving user:', error);
        alert('Error saving user');
    });
}

function editUser(userId) {
    const user = currentUsers.find(u => u.user_id === userId);
    if (!user) return;
    
    editingUserId = userId;
    document.getElementById('userId').value = userId;
    document.getElementById('email').value = user.email;
    document.getElementById('name').value = user.name;
    document.getElementById('role').value = user.role;
    document.getElementById('password').value = ''; // Don't populate password
    
    // Update button text
    document.getElementById('submitBtn').innerHTML = '<i data-lucide="save" class="w-4 h-4 inline mr-2"></i>Update User';
    
    // Scroll to form
    document.getElementById('userForm').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    editingUserId = null;
    document.getElementById('submitBtn').innerHTML = '<i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>Add User';
}

function toggleUserStatus(userId) {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('user_id', userId);
    
    fetch('../user_management_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadUsers();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error toggling user status:', error);
        alert('Error toggling user status');
    });
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('user_id', userId);
    
    fetch('../user_management_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadUsers();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error deleting user:', error);
        alert('Error deleting user');
    });
}
</script>
