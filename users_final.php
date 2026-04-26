<?php
// User Management Page (Admin Only)
require_once '../auth_new.php';
$currentUser = getCurrentUser();
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">User Management</h1>
    
    <!-- Add New User -->
    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
        <h2 class="text-lg font-semibold mb-4 text-white">Add New User</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                <input type="email" placeholder="user@campus.com" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                <input type="text" placeholder="John Doe" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">User Role</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option>Student</option>
                    <option>Driver</option>
                    <option>Administrator</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                <input type="password" placeholder="Enter password" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
        </div>
        
        <div class="flex space-x-3 mt-4">
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                Add User
            </button>
            <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Clear Form
            </button>
        </div>
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
                <tbody>
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="px-4 py-3">admin@campus.com</td>
                        <td class="px-4 py-3">System Administrator</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Admin</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                        </td>
                        <td class="px-4 py-3">2024-04-23 08:00</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Edit</button>
                                <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Delete</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="px-4 py-3">student@campus.com</td>
                        <td class="px-4 py-3">Student User</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Student</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                        </td>
                        <td class="px-4 py-3">2024-04-23 07:30</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Edit</button>
                                <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Delete</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="px-4 py-3">driver@campus.com</td>
                        <td class="px-4 py-3">Driver User</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Driver</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                        </td>
                        <td class="px-4 py-3">2024-04-23 06:45</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Edit</button>
                                <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-gray-400">
                Showing 1-3 of 247 users
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">Previous</button>
                <button class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">Next</button>
            </div>
        </div>
    </div>
    
    <!-- User Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Users</p>
                    <p class="text-2xl font-bold text-white">247</p>
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
                    <p class="text-2xl font-bold text-white">89</p>
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
                    <p class="text-2xl font-bold text-white">12</p>
                </div>
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>
