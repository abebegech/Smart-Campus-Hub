<?php
// QR Code Verification Script
require_once 'config.php';

// Get transaction ID from QR code data
$transactionId = $_GET['txn'] ?? '';

if (empty($transactionId)) {
    die('<h1>Invalid QR Code</h1><p>This QR code does not contain valid transaction data.</p>');
}

// Sample permit data (in production, this would come from database)
$permitData = [
    'transaction_id' => $transactionId,
    'student_name' => 'John Doe',
    'student_id' => 'STU000001',
    'email' => 'john.doe@campus.edu',
    'permit_type' => 'Monthly Pass',
    'status' => 'APPROVED',
    'issue_date' => date('Y-m-d'),
    'expiry_date' => date('Y-m-d', strtotime('+1 month')),
    'route' => 'Campus Loop'
];

// Check if permit is valid
$isValid = true;
$expiryDate = new DateTime($permitData['expiry_date']);
$today = new DateTime();

if ($today > $expiryDate) {
    $isValid = false;
}

// Display verification result
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permit Verification - Smart Campus Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <div class="text-center mb-6">
            <?php if ($isValid): ?>
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-8 h-8 text-green-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-green-600 mb-2">Permit Verified</h1>
                <p class="text-gray-600">This permit is valid and active</p>
            <?php else: ?>
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="x-circle" class="w-8 h-8 text-red-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-red-600 mb-2">Permit Invalid</h1>
                <p class="text-gray-600">This permit has expired</p>
            <?php endif; ?>
        </div>
        
        <div class="space-y-3 mb-6">
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-600">Transaction ID:</span>
                <span class="font-medium"><?php echo $transactionId; ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-600">Student Name:</span>
                <span class="font-medium"><?php echo $permitData['student_name']; ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-600">Student ID:</span>
                <span class="font-medium"><?php echo $permitData['student_id']; ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-600">Permit Type:</span>
                <span class="font-medium"><?php echo $permitData['permit_type']; ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-600">Route:</span>
                <span class="font-medium"><?php echo $permitData['route']; ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-600">Status:</span>
                <span class="font-medium <?php echo $isValid ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $isValid ? 'APPROVED' : 'EXPIRED'; ?>
                </span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600">Expiry Date:</span>
                <span class="font-medium"><?php echo $permitData['expiry_date']; ?></span>
            </div>
        </div>
        
        <div class="text-center">
            <button onclick="window.close()" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                Close
            </button>
        </div>
        
        <div class="mt-4 text-center text-xs text-gray-500">
            <p>Verified on: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>Smart Campus Hub Transport System</p>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .verification-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1a5490 0%, #2c3e50 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 20px 0;
        }
        .valid {
            background: #27ae60;
            color: white;
        }
        .expired {
            background: #e74c3c;
            color: white;
        }
        .permit-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .field {
            display: flex;
            margin: 10px 0;
        }
        .field-label {
            font-weight: bold;
            width: 150px;
            color: #2c3e50;
        }
        .field-value {
            color: #333;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #ecf0f1;
            color: #7f8c8d;
            font-size: 12px;
        }
        .timestamp {
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header">
            <h1>Permit Verification</h1>
            <p>Campus Transport Management System</p>
        </div>
        
        <div class="content">
            <div style="text-align: center;">
                <div class="status-badge <?php echo strtolower($status); ?>">
                    <?php echo $status; ?>
                </div>
            </div>
            
            <div class="permit-info">
                <h3>Permit Details</h3>
                <div class="field">
                    <span class="field-label">Transaction ID:</span>
                    <span class="field-value"><?php echo htmlspecialchars($permit['transaction_id']); ?></span>
                </div>
                <div class="field">
                    <span class="field-label">Student Name:</span>
                    <span class="field-value"><?php echo htmlspecialchars($permit['first_name'] . ' ' . $permit['last_name']); ?></span>
                </div>
                <div class="field">
                    <span class="field-label">Student ID:</span>
                    <span class="field-value">STU<?php echo str_pad($permit['user_id'], 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="field">
                    <span class="field-label">Permit Type:</span>
                    <span class="field-value"><?php echo ucfirst($permit['permit_type']); ?> Pass</span>
                </div>
                <div class="field">
                    <span class="field-label">Valid From:</span>
                    <span class="field-value"><?php echo date('M d, Y', strtotime($permit['valid_from'])); ?></span>
                </div>
                <div class="field">
                    <span class="field-label">Valid Until:</span>
                    <span class="field-value"><?php echo date('M d, Y', strtotime($permit['valid_until'])); ?></span>
                </div>
            </div>
            
            <?php if ($isValid): ?>
                <div style="text-align: center; color: #27ae60;">
                    <p><strong>This permit is VALID and can be used for campus transport services.</strong></p>
                    <p>Please verify the student's ID matches the information above.</p>
                </div>
            <?php else: ?>
                <div style="text-align: center; color: #e74c3c;">
                    <p><strong>This permit has EXPIRED and cannot be used for transport services.</strong></p>
                    <p>The student should renew their permit to continue using campus transport.</p>
                </div>
            <?php endif; ?>
            
            <div class="timestamp">
                Verified on: <?php echo date('M d, Y H:i:s'); ?>
            </div>
        </div>
        
        <div class="footer">
            <p>Transport Tracker - Campus Transport Management System</p>
            <p>This verification page is for official use by campus security and transport staff.</p>
        </div>
    </div>
</body>
</html>
