<?php
// Permits Page
require_once '../auth.php';
$currentUser = getCurrentUser();
$userRole = $currentUser['role'];
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">My Permits</h3>
            <button onclick="loadPermitForm()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                Request New Permit
            </button>
        </div>
    </div>

    <!-- Permits List -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Active Permits</h3>
        
        <?php
        // Sample permit data - in real app, this would come from database
        $permits = [
            [
                'id' => 'PERM-001',
                'type' => 'Student Parking',
                'status' => 'active',
                'valid_from' => '2024-01-15',
                'valid_until' => '2024-05-15',
                'vehicle' => 'Toyota Corolla',
                'permit_number' => 'STU-2024-001',
                'qr_code' => 'PERM-001-QR'
            ],
            [
                'id' => 'PERM-002',
                'type' => 'Faculty Parking',
                'status' => 'active',
                'valid_from' => '2024-01-10',
                'valid_until' => '2024-06-10',
                'vehicle' => 'Honda Civic',
                'permit_number' => 'FAC-2024-002',
                'qr_code' => 'PERM-002-QR'
            ],
            [
                'id' => 'PERM-003',
                'type' => 'Visitor Pass',
                'status' => 'expired',
                'valid_from' => '2023-12-01',
                'valid_until' => '2024-01-01',
                'vehicle' => 'Ford Focus',
                'permit_number' => 'VIS-2023-003',
                'qr_code' => 'PERM-003-QR'
            ]
        ];
        
        foreach ($permits as $permit):
        ?>
            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                        <?php
                        $statusColor = $permit['status'] === 'active' ? 'text-green-400' : 
                                     ($permit['status'] === 'expired' ? 'text-red-400' : 'text-yellow-400');
                        ?>
                        <i data-lucide="credit-card" class="w-5 h-5 <?php echo $statusColor; ?>"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium"><?php echo $permit['type']; ?></p>
                        <p class="text-gray-400 text-sm">Permit #: <?php echo $permit['permit_number']; ?></p>
                        <p class="text-gray-500 text-xs">Vehicle: <?php echo $permit['vehicle']; ?></p>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="space-y-1">
                        <span class="text-xs text-gray-400">
                            <?php echo date('M j, Y', strtotime($permit['valid_from'])); ?> - 
                            <?php echo date('M j, Y', strtotime($permit['valid_until'])); ?>
                        </span>
                        <span class="text-xs <?php echo $statusColor; ?>">
                            <?php echo ucfirst($permit['status']); ?>
                        </span>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button onclick="downloadPermit('<?php echo $permit['id']; ?>')" 
                                class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                            <i data-lucide="download" class="w-3 h-3"></i>
                            PDF
                        </button>
                        
                        <button onclick="viewQRCode('<?php echo $permit['qr_code']; ?>')" 
                                class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                            <i data-lucide="qr-code" class="w-3 h-3"></i>
                            QR
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
</div>

<!-- Permit Form Modal -->
<div id="permitFormModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md mx-4 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Request New Permit</h3>
            <button onclick="closePermitForm()" class="text-gray-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form onsubmit="submitPermitRequest(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Permit Type</label>
                <select name="permit_type" required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Select permit type</option>
                    <option value="student_parking">Student Parking</option>
                    <option value="faculty_parking">Faculty Parking</option>
                    <option value="visitor_pass">Visitor Pass</option>
                    <option value="temporary">Temporary Permit</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Vehicle Information</label>
                <input type="text" name="vehicle_make" placeholder="Vehicle Make" required
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 mb-2">
                <input type="text" name="vehicle_model" placeholder="Vehicle Model" required
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Duration</label>
                <select name="duration" required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Select duration</option>
                    <option value="1_month">1 Month</option>
                    <option value="3_months">3 Months</option>
                    <option value="6_months">6 Months</option>
                    <option value="1_year">1 Year</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Reason</label>
                <textarea name="reason" rows="3" placeholder="Enter reason for permit request" required
                          class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Submit Request
                </button>
                <button type="button" onclick="closePermitForm()" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 max-w-sm mx-4 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Permit QR Code</h3>
            <button onclick="closeQRModal()" class="text-gray-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="text-center">
            <div id="qrCodeDisplay" class="bg-white p-4 rounded-lg mb-4">
                <!-- QR code will be displayed here -->
            </div>
            <p class="text-gray-400 text-sm mb-4">Scan this code to verify permit</p>
            <button onclick="closeQRModal()" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function loadPermitForm() {
    document.getElementById('permitFormModal').classList.remove('hidden');
}

function closePermitForm() {
    document.getElementById('permitFormModal').classList.add('hidden');
}

function viewQRCode(qrCode) {
    // Generate QR code image
    const qrContainer = document.getElementById('qrCodeDisplay');
    qrContainer.innerHTML = `
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrCode}" 
             alt="QR Code for ${qrCode}" 
             class="mx-auto">
    `;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

function downloadPermit(permitId) {
    // In real implementation, this would generate and download PDF
    window.open('generate_permit.php?id=' + permitId, '_blank');
}

function submitPermitRequest(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('create_permit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permit request submitted successfully!');
            closePermitForm();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting your request');
    });
}
</script>
