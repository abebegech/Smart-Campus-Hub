<?php
// User Management Page with Image Upload (Admin Only)
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
        <form id="userForm" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" id="userId" name="user_id" value="">
            
            <!-- Profile Image Upload -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Profile Image</label>
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center overflow-hidden">
                        <img id="imagePreview" src="" alt="Profile Preview" class="w-full h-full object-cover hidden">
                        <i data-lucide="user" class="w-8 h-8 text-gray-400" id="defaultAvatar"></i>
                    </div>
                    <div class="flex-1">
                        <input type="file" id="profileImage" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                        <button type="button" onclick="document.getElementById('profileImage').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="upload" class="w-4 h-4 inline mr-2"></i>
                            Choose Image
                        </button>
                        <button type="button" onclick="removeImage()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors ml-2" id="removeImageBtn" style="display: none;">
                            <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                            Remove
                        </button>
                        <p class="text-xs text-gray-400 mt-2">JPEG, PNG, GIF, WebP (Max 5MB)</p>
                    </div>
                </div>
            </div>
            
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
                        <th class="px-4 py-3 text-left">Image</th>
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
let currentImageFile = null;

// Load users when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    setupImageUpload();
});

// Form submission
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    saveUser();
});

function setupImageUpload() {
    const imageInput = document.getElementById('profileImage');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file
            if (!validateImageFile(file)) {
                return;
            }
            
            // Show preview
            showImagePreview(file);
            currentImageFile = file;
        }
    });
}

function validateImageFile(file) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
        return false;
    }
    
    if (file.size > maxSize) {
        alert('File too large. Maximum size is 5MB.');
        return false;
    }
    
    return true;
}

function showImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('imagePreview');
        const defaultAvatar = document.getElementById('defaultAvatar');
        const removeBtn = document.getElementById('removeImageBtn');
        
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        defaultAvatar.classList.add('hidden');
        removeBtn.style.display = 'inline-block';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    const preview = document.getElementById('imagePreview');
    const defaultAvatar = document.getElementById('defaultAvatar');
    const removeBtn = document.getElementById('removeImageBtn');
    const imageInput = document.getElementById('profileImage');
    
    preview.src = '';
    preview.classList.add('hidden');
    defaultAvatar.classList.remove('hidden');
    removeBtn.style.display = 'none';
    imageInput.value = '';
    currentImageFile = null;
}

function loadUsers() {
    // Direct inline data - no API calls
    setTimeout(() => {
        loadSampleUsers();
    }, 500); // Simulate loading delay
}

function loadSampleUsers() {
    const sampleUsers = [
        {user_id: 1, email: 'admin@campus.com', name: 'System Administrator', role: 'admin', status: 'active', created_at: '2024-01-01 00:00:00', last_login: '2024-04-23 08:00:00', profile_image: null},
        {user_id: 2, email: 'student@campus.com', name: 'Student User', role: 'student', status: 'active', created_at: '2024-02-15 00:00:00', last_login: '2024-04-23 07:30:00', profile_image: null},
        {user_id: 3, email: 'driver@campus.com', name: 'Driver User', role: 'driver', status: 'active', created_at: '2024-03-10 00:00:00', last_login: '2024-04-23 06:45:00', profile_image: null}
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
        const userImage = getUserImage(user);
        
        row.innerHTML = `
            <td class="px-4 py-3">${userImage}</td>
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

function getUserImage(user) {
    if (user.profile_image) {
        // For simulated images, show a special indicator
        return `<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative">
            <i data-lucide="image" class="w-5 h-5 text-white"></i>
            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-gray-800"></div>
        </div>`;
    } else {
        return `<div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
        </div>`;
    }
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
    
    // Handle image upload if there's a new image
    if (currentImageFile) {
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i>Uploading...';
        submitBtn.disabled = true;
        
        uploadUserImage(editingUserId || 'new', currentImageFile)
            .then((result) => {
                console.log('Image uploaded:', result);
                // Save user data with image
                saveUserDataWithImage(result.filename);
            })
            .catch(error => {
                console.error('Error uploading image:', error);
                alert('Error uploading image: ' + error.message);
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    } else {
        // Save user data without image
        saveUserDataWithImage(null);
    }
}

function uploadUserImage(userId, file) {
    return new Promise((resolve, reject) => {
        // Simulate successful upload for now
        setTimeout(() => {
            const filename = 'user_' + Date.now() + '_' + Math.floor(Math.random() * 1000) + '.jpg';
            resolve({
                success: true,
                message: 'Image uploaded successfully',
                filename: filename,
                image_url: 'uploads/user_images/' + filename
            });
        }, 1000); // Simulate upload delay
    });
}

function saveUserDataWithImage(imageFilename) {
    // Direct inline solution - no API calls
    setTimeout(() => {
        // Get form data directly
        const email = document.getElementById('email').value;
        const name = document.getElementById('name').value;
        const role = document.getElementById('role').value;
        
        console.log('Saving user:', { email, name, role, imageFilename });
        
        if (editingUserId) {
            // Update existing user
            const user = currentUsers.find(u => u.user_id === editingUserId);
            if (user) {
                user.email = email;
                user.name = name;
                user.role = role;
                if (imageFilename) {
                    user.profile_image = imageFilename;
                }
                user.updated_at = new Date().toISOString();
            }
        } else {
            // Create new user
            const newUser = {
                user_id: Math.max(...currentUsers.map(u => u.user_id)) + 1,
                email: email,
                name: name,
                role: role,
                status: 'active',
                created_at: new Date().toISOString(),
                last_login: null,
                profile_image: imageFilename
            };
            currentUsers.push(newUser);
        }
        
        // Restore button state
        const submitBtn = document.getElementById('submitBtn');
        const originalText = editingUserId ? 
            '<i data-lucide="save" class="w-4 h-4 inline mr-2"></i>Update User' : 
            '<i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>Add User';
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        const action = editingUserId ? 'updated' : 'created';
        alert(`User ${action} successfully!`);
        
        // Update display and reset
        displayUsers(currentUsers);
        updateStatistics(currentUsers);
        resetForm();
    }, 1000); // Simulate processing time
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
    
    // Load user image if exists
    if (user.profile_image) {
        const preview = document.getElementById('imagePreview');
        const defaultAvatar = document.getElementById('defaultAvatar');
        const removeBtn = document.getElementById('removeImageBtn');
        
        // For simulated images, show a placeholder instead of trying to load actual file
        preview.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iMjAiIGZpbGw9InVybCgjZ3JhZGllbnQwXzEpIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9ImdyYWRpZW50MF8xIiB4MT0iMCIgeTE9IjAiIHgyPSI0MCIgeTI9IjQwIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMzQjgyRjYiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjOEQ0NkRGIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPHN2ZyB4PSIxMCIgeT0iMTAiIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0yMSAxOVY1SDNWMThIMjFWMTlaTTkgMTdINzYxN0g5VjE3Wk0xMiA3SDEwVjEySDEyVjdaTTEzIDdIMTJWMTJIMTNWN1oiLz4KPC9zdmc+';
        preview.classList.remove('hidden');
        defaultAvatar.classList.add('hidden');
        removeBtn.style.display = 'inline-block';
    }
    
    // Update button text
    document.getElementById('submitBtn').innerHTML = '<i data-lucide="save" class="w-4 h-4 inline mr-2"></i>Update User';
    
    // Scroll to form
    document.getElementById('userForm').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    editingUserId = null;
    currentImageFile = null;
    
    // Reset image preview
    const preview = document.getElementById('imagePreview');
    const defaultAvatar = document.getElementById('defaultAvatar');
    const removeBtn = document.getElementById('removeImageBtn');
    
    preview.src = '';
    preview.classList.add('hidden');
    defaultAvatar.classList.remove('hidden');
    removeBtn.style.display = 'none';
    
    document.getElementById('submitBtn').innerHTML = '<i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>Add User';
}

function toggleUserStatus(userId) {
    // Direct inline solution
    const user = currentUsers.find(u => u.user_id === userId);
    if (!user) return;
    
    // Toggle status
    const newStatus = user.status === 'active' ? 'inactive' : 'active';
    user.status = newStatus;
    user.updated_at = new Date().toISOString();
    
    // Update display
    displayUsers(currentUsers);
    
    // Show message
    alert(`User status updated to ${newStatus}`);
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    // Direct inline solution
    const userIndex = currentUsers.findIndex(u => u.user_id === userId);
    if (userIndex === -1) return;
    
    // Don't allow deletion of admin users
    if (currentUsers[userIndex].role === 'admin') {
        alert('Cannot delete admin users');
        return;
    }
    
    // Remove user
    currentUsers.splice(userIndex, 1);
    
    // Update display
    displayUsers(currentUsers);
    updateStatistics(currentUsers);
    
    // Show message
    alert('User deleted successfully');
}
</script>
