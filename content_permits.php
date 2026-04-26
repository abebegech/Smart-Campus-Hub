<?php
// Permits management content
session_start();
require_once 'config.php';

$userRole = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? null;

// Sample permits data (in real app, this would come from database)
$permits = [
    [
        'id' => 1,
        'transaction_id' => 'TXN2025041712345678',
        'student_name' => 'John Doe',
        'student_id' => 'STU000001',
        'permit_type' => 'Monthly Pass',
        'status' => 'approved',
        'issue_date' => '2025-04-17',
        'expiry_date' => '2025-05-17',
        'route' => 'Campus Loop',
        'email' => 'john.doe@campus.edu'
    ],
    [
        'id' => 2,
        'transaction_id' => 'TXN2025041712345679',
        'student_name' => 'Jane Smith',
        'student_id' => 'STU000002',
        'permit_type' => 'Semester Pass',
        'status' => 'pending',
        'issue_date' => '2025-04-17',
        'expiry_date' => '2025-08-17',
        'route' => 'Express Route',
        'email' => 'jane.smith@campus.edu'
    ],
    [
        'id' => 3,
        'transaction_id' => 'TXN2025041712345680',
        'student_name' => 'Mike Johnson',
        'student_id' => 'STU000003',
        'permit_type' => 'Monthly Pass',
        'status' => 'approved',
        'issue_date' => '2025-04-15',
        'expiry_date' => '2025-05-15',
        'route' => 'Night Service',
        'email' => 'mike.johnson@campus.edu'
    ],
    [
        'id' => 4,
        'transaction_id' => 'TXN2025041712345681',
        'student_name' => 'Sarah Davis',
        'student_id' => 'STU000004',
        'permit_type' => 'Weekly Pass',
        'status' => 'rejected',
        'issue_date' => '2025-04-16',
        'expiry_date' => '2025-04-23',
        'route' => 'Campus Loop',
        'email' => 'sarah.davis@campus.edu'
    ]
];

$permitStats = [
    'total' => count($permits),
    'approved' => count(array_filter($permits, fn($p) => $p['status'] === 'approved')),
    'pending' => count(array_filter($permits, fn($p) => $p['status'] === 'pending')),
    'rejected' => count(array_filter($permits, fn($p) => $p['status'] === 'rejected'))
];
?>

<div class="space-y-6 animate-fade-in">
    <!-- Header Section -->
    <div class="glass rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">Permit Management</h2>
                <p class="text-dark-text-secondary">Manage and monitor all transportation permits</p>
            </div>
            <div class="flex space-x-3">
                <?php if ($userRole === 'admin'): ?>
                <button onclick="showNewPermitModal()" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>New Permit</span>
                </button>
                <?php endif; ?>
                <button onclick="exportPermits()" class="px-4 py-2 bg-primary/10 border border-primary/20 text-primary rounded-lg hover:bg-primary/20 transition-colors flex items-center space-x-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="file-text" class="w-8 h-8 text-primary"></i>
                <span class="text-xs text-primary bg-primary/20 px-2 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $permitStats['total']; ?></h3>
            <p class="text-sm text-dark-text-secondary">All Permits</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-400"></i>
                <span class="text-xs text-green-400 bg-green-400/20 px-2 py-1 rounded-full">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $permitStats['approved']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Approved</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="clock" class="w-8 h-8 text-yellow-400"></i>
                <span class="text-xs text-yellow-400 bg-yellow-400/20 px-2 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $permitStats['pending']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Pending Review</p>
        </div>

        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <i data-lucide="x-circle" class="w-8 h-8 text-red-400"></i>
                <span class="text-xs text-red-400 bg-red-400/20 px-2 py-1 rounded-full">Rejected</span>
            </div>
            <h3 class="text-2xl font-bold text-white"><?php echo $permitStats['rejected']; ?></h3>
            <p class="text-sm text-dark-text-secondary">Rejected</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="glass rounded-xl p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" id="permitSearch" placeholder="Search permits..." 
                           class="w-full px-4 py-2 pl-10 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary
                                  placeholder-dark-text-secondary focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-dark-text-secondary"></i>
                </div>
            </div>
            <div class="flex gap-2">
                <select id="statusFilter" class="px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                    <option value="">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select id="typeFilter" class="px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                    <option value="">All Types</option>
                    <option value="Monthly Pass">Monthly Pass</option>
                    <option value="Semester Pass">Semester Pass</option>
                    <option value="Weekly Pass">Weekly Pass</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Permits Table -->
    <div class="glass rounded-xl p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Transaction ID</th>
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Student</th>
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Type</th>
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Status</th>
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Issue Date</th>
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Expiry</th>
                        <th class="text-left py-3 px-4 text-dark-text-secondary font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody id="permitsTableBody">
                    <?php foreach ($permits as $permit): ?>
                    <tr class="border-b border-dark-border hover:bg-white/5 transition-colors">
                        <td class="py-3 px-4">
                            <span class="text-primary font-mono text-sm"><?php echo $permit['transaction_id']; ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <p class="text-white font-medium"><?php echo $permit['student_name']; ?></p>
                                <p class="text-xs text-dark-text-secondary"><?php echo $permit['student_id']; ?></p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs"><?php echo $permit['permit_type']; ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <?php
                            $statusColors = [
                                'approved' => 'bg-green-500/20 text-green-400',
                                'pending' => 'bg-yellow-500/20 text-yellow-400',
                                'rejected' => 'bg-red-500/20 text-red-400'
                            ];
                            $statusIcons = [
                                'approved' => 'check-circle',
                                'pending' => 'clock',
                                'rejected' => 'x-circle'
                            ];
                            ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $statusColors[$permit['status']]; ?>">
                                <i data-lucide="<?php echo $statusIcons[$permit['status']]; ?>" class="w-3 h-3 mr-1"></i>
                                <?php echo ucfirst($permit['status']); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-dark-text-secondary text-sm"><?php echo $permit['issue_date']; ?></td>
                        <td class="py-3 px-4 text-dark-text-secondary text-sm"><?php echo $permit['expiry_date']; ?></td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <button onclick="viewPermit('<?php echo $permit['transaction_id']; ?>')" 
                                        class="text-primary hover:text-primary/80 transition-colors" title="View">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button onclick="downloadPermit('<?php echo $permit['transaction_id']; ?>')" 
                                        class="text-green-400 hover:text-green-300 transition-colors" title="Download PDF">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </button>
                                <?php if ($userRole === 'admin' && $permit['status'] === 'pending'): ?>
                                <button onclick="approvePermit('<?php echo $permit['transaction_id']; ?>')" 
                                        class="text-blue-400 hover:text-blue-300 transition-colors" title="Approve">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                                <button onclick="rejectPermit('<?php echo $permit['transaction_id']; ?>')" 
                                        class="text-red-400 hover:text-red-300 transition-colors" title="Reject">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Permit Modal -->
<div id="newPermitModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-dark rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Create New Permit</h3>
            <button onclick="closeNewPermitModal()" class="text-dark-text-secondary hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form onsubmit="createNewPermit(event)" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Student Name</label>
                    <input type="text" name="student_name" required 
                           class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Student ID</label>
                    <input type="text" name="student_id" required 
                           class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Permit Type</label>
                    <select name="permit_type" required 
                            class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                        <option value="Monthly Pass">Monthly Pass</option>
                        <option value="Semester Pass">Semester Pass</option>
                        <option value="Weekly Pass">Weekly Pass</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Route</label>
                    <select name="route" required 
                            class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                        <option value="Campus Loop">Campus Loop</option>
                        <option value="Express Route">Express Route</option>
                        <option value="Night Service">Night Service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-text-secondary mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" required 
                           class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-dark-text-primary focus:outline-none focus:border-primary">
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                    Create Permit
                </button>
                <button type="button" onclick="closeNewPermitModal()" class="flex-1 px-4 py-3 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Permit Details Modal -->
<div id="permitDetailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-dark rounded-xl p-6 w-full max-w-lg mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Permit Details</h3>
            <button onclick="closePermitDetailsModal()" class="text-dark-text-secondary hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="permitDetailsContent">
            <!-- Permit details will be loaded here -->
        </div>
    </div>
</div>

<script>
// Permit management functions
function showNewPermitModal() {
    document.getElementById('newPermitModal').classList.remove('hidden');
}

function closeNewPermitModal() {
    document.getElementById('newPermitModal').classList.add('hidden');
}

function createNewPermit(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Creating...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Generate transaction ID
        const txnId = 'TXN' + new Date().toISOString().slice(0,10).replace(/-/g, '') + 
                     Math.floor(Math.random() * 1000000).toString().padStart(7, '0');
        
        // Show success message
        const successDiv = document.createElement('div');
        successDiv.className = 'mt-4 p-3 bg-green-500/20 border border-green-500/50 rounded-lg text-green-300';
        successDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Permit created successfully! Transaction ID: ${txnId}</span>
            </div>
        `;
        
        event.target.appendChild(successDiv);
        lucide.createIcons();
        
        // Reset form
        event.target.reset();
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Close modal after 2 seconds
        setTimeout(() => {
            closeNewPermitModal();
            location.reload(); // Refresh to show new permit
        }, 2000);
    }, 1500);
}

function viewPermit(txnId) {
    const modal = document.getElementById('permitDetailsModal');
    const content = document.getElementById('permitDetailsContent');
    
    // Show loading
    content.innerHTML = '<div class="text-center py-8"><i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto mb-2"></i><p>Loading permit details...</p></div>';
    modal.classList.remove('hidden');
    lucide.createIcons();
    
    // Simulate loading permit details
    setTimeout(() => {
        content.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center">
                        <i data-lucide="ticket" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">${txnId}</h4>
                        <p class="text-sm text-dark-text-secondary">Transaction ID</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-dark-text-secondary">Student Name</p>
                        <p class="text-white">John Doe</p>
                    </div>
                    <div>
                        <p class="text-sm text-dark-text-secondary">Student ID</p>
                        <p class="text-white">STU000001</p>
                    </div>
                    <div>
                        <p class="text-sm text-dark-text-secondary">Permit Type</p>
                        <p class="text-white">Monthly Pass</p>
                    </div>
                    <div>
                        <p class="text-sm text-dark-text-secondary">Status</p>
                        <span class="inline-flex items-center px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                            Approved
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-dark-text-secondary">Issue Date</p>
                        <p class="text-white">2025-04-17</p>
                    </div>
                    <div>
                        <p class="text-sm text-dark-text-secondary">Expiry Date</p>
                        <p class="text-white">2025-05-17</p>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button onclick="downloadPermit('${txnId}')" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Download PDF
                    </button>
                    <button onclick="closePermitDetailsModal()" class="flex-1 px-4 py-2 bg-dark-border text-dark-text-primary rounded-lg hover:bg-white/10 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        `;
        lucide.createIcons();
    }, 1000);
}

function closePermitDetailsModal() {
    document.getElementById('permitDetailsModal').classList.add('hidden');
}

function downloadPermit(txnId) {
    window.open(`generate_permit.php?txn=${txnId}`, '_blank');
}

function approvePermit(txnId) {
    if (confirm('Are you sure you want to approve this permit?')) {
        // Show loading
        event.target.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
        
        setTimeout(() => {
            // Show success
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg z-50';
            successDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Permit approved successfully!';
            document.body.appendChild(successDiv);
            lucide.createIcons();
            
            // Remove message after 3 seconds
            setTimeout(() => successDiv.remove(), 3000);
            
            // Refresh table
            location.reload();
        }, 1000);
    }
}

function rejectPermit(txnId) {
    if (confirm('Are you sure you want to reject this permit?')) {
        // Show loading
        event.target.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
        
        setTimeout(() => {
            // Show success
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg z-50';
            successDiv.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>Permit rejected successfully!';
            document.body.appendChild(successDiv);
            lucide.createIcons();
            
            // Remove message after 3 seconds
            setTimeout(() => successDiv.remove(), 3000);
            
            // Refresh table
            location.reload();
        }, 1000);
    }
}

function exportPermits() {
    window.open('generate_permit.php?export=all', '_blank');
}

// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('permitSearch');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterPermits);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterPermits);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterPermits);
    }
});

function filterPermits() {
    const searchTerm = document.getElementById('permitSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const rows = document.querySelectorAll('#permitsTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const status = row.querySelector('td:nth-child(4) span')?.textContent.toLowerCase() || '';
        const type = row.querySelector('td:nth-child(3) span')?.textContent.toLowerCase() || '';
        
        const matchesSearch = text.includes(searchTerm);
        const matchesStatus = !statusFilter || status.includes(statusFilter.toLowerCase());
        const matchesType = !typeFilter || type.includes(typeFilter.toLowerCase());
        
        row.style.display = matchesSearch && matchesStatus && matchesType ? '' : 'none';
    });
}
</script>
