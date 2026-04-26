<?php
// Permits Page Content
?>
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">My Permits</h1>
    
    <!-- Request New Permit Button -->
    <div class="mb-6">
        <button onclick="showPermitForm()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
            Request New Permit
        </button>
    </div>
    
    <!-- Active Permits -->
    <div class="space-y-4">
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Student Parking Permit</h3>
                        <p class="text-sm text-gray-400">Permit #: STU-2024-001</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Valid From:</span>
                    <span class="text-white font-medium">Jan 15, 2024</span>
                </div>
                <div>
                    <span class="text-gray-400">Valid Until:</span>
                    <span class="text-white font-medium">May 15, 2024</span>
                </div>
                <div>
                    <span class="text-gray-400">Vehicle:</span>
                    <span class="text-white font-medium">Toyota Corolla</span>
                </div>
                <div>
                    <span class="text-gray-400">License:</span>
                    <span class="text-white font-medium">ABC1234</span>
                </div>
            </div>
            
            <div class="flex space-x-2 mt-4">
                <button onclick="downloadPermit('STU-2024-001')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                    Download PDF
                </button>
                <button onclick="viewQRCode('STU-2024-001')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="qr-code" class="w-4 h-4 inline mr-2"></i>
                    View QR Code
                </button>
            </div>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Faculty Parking Permit</h3>
                        <p class="text-sm text-gray-400">Permit #: FAC-2024-002</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Valid From:</span>
                    <span class="text-white font-medium">Jan 10, 2024</span>
                </div>
                <div>
                    <span class="text-gray-400">Valid Until:</span>
                    <span class="text-white font-medium">Jun 10, 2024</span>
                </div>
                <div>
                    <span class="text-gray-400">Vehicle:</span>
                    <span class="text-white font-medium">Honda Civic</span>
                </div>
                <div>
                    <span class="text-gray-400">License:</span>
                    <span class="text-white font-medium">XYZ5678</span>
                </div>
            </div>
            
            <div class="flex space-x-2 mt-4">
                <button onclick="downloadPermit('FAC-2024-002')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                    Download PDF
                </button>
                <button onclick="viewQRCode('FAC-2024-002')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="qr-code" class="w-4 h-4 inline mr-2"></i>
                    View QR Code
                </button>
            </div>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Visitor Pass</h3>
                        <p class="text-sm text-gray-400">Permit #: VIS-2023-003</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full">Expired</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Valid From:</span>
                    <span class="text-white font-medium">Dec 1, 2023</span>
                </div>
                <div>
                    <span class="text-gray-400">Valid Until:</span>
                    <span class="text-white font-medium">Jan 1, 2024</span>
                </div>
                <div>
                    <span class="text-gray-400">Vehicle:</span>
                    <span class="text-white font-medium">Ford Focus</span>
                </div>
                <div>
                    <span class="text-gray-400">License:</span>
                    <span class="text-white font-medium">DEF9012</span>
                </div>
            </div>
            
            <div class="flex space-x-2 mt-4">
                <button onclick="renewPermit('VIS-2023-003')" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                    Renew Permit
                </button>
                <button onclick="viewQRCode('VIS-2023-003')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i data-lucide="qr-code" class="w-4 h-4 inline mr-2"></i>
                    View QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Permit Form Modal -->
<div id="permitFormModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md mx-4 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Request New Permit</h3>
            <button onclick="hidePermitForm()" class="text-gray-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Permit Type</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option>Student Parking</option>
                    <option>Faculty Parking</option>
                    <option>Visitor Pass</option>
                    <option>Temporary Permit</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Vehicle Information</label>
                <input type="text" placeholder="Vehicle Make" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 mb-2">
                <input type="text" placeholder="Vehicle Model" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Duration</label>
                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option>1 Month</option>
                    <option>3 Months</option>
                    <option>6 Months</option>
                    <option>1 Year</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Reason</label>
                <textarea rows="3" placeholder="Enter reason for permit request" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="submitPermit()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Submit Request
                </button>
                <button type="button" onclick="hidePermitForm()" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showPermitForm() {
    document.getElementById('permitFormModal').classList.remove('hidden');
}

function hidePermitForm() {
    document.getElementById('permitFormModal').classList.add('hidden');
}

function downloadPermit(permitId) {
    window.location.href = 'generate_permit_fixed.php?permit_id=' + permitId;
}

function viewQRCode(permitId) {
    const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + permitId;
    window.open(qrUrl, '_blank');
}

function renewPermit(permitId) {
    alert('Renewing permit: ' + permitId);
}

function submitPermit() {
    alert('Permit request submitted successfully!');
    hidePermitForm();
}
</script>
