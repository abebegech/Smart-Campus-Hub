<?php
require_once 'auth_new.php';

// If already authenticated, redirect to dashboard
if (isAuthenticated()) {
    redirectByRole();
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (authenticate($email, $password)) {
        redirectByRole();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Campus Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="container w-full max-w-md mx-auto p-8 rounded-2xl shadow-2xl">
            <h1 class="text-3xl font-bold text-white text-center mb-8">Smart Campus Hub</h1>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-500/20 border border-red-500/50 p-4 rounded-lg text-white mb-6">
                    <h2 class="text-xl font-semibold mb-2">Login Failed</h2>
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Email Address</label>
                    <input type="email" 
                           name="email" 
                           required 
                           placeholder="Enter your email"
                           class="w-full px-4 py-3 rounded-lg bg-white/20 border border-white/30 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white">
                </div>
                
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Password</label>
                    <input type="password" 
                           name="password" 
                           required 
                           placeholder="Enter your password"
                           class="w-full px-4 py-3 rounded-lg bg-white/20 border border-white/30 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white">
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-white text-purple-700 rounded-lg font-semibold hover:bg-purple-800 transition-colors">
                    Sign In
                </button>
            </form>
            
            <div class="mt-8 text-white">
                <h3 class="text-lg font-semibold mb-4">Demo Accounts</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
                        <div>
                            <span class="font-medium">Administrator:</span>
                            <span>System Administrator</span>
                        </div>
                        <div class="text-right text-xs">
                            <div>admin@campus.com</div>
                            <div>admin123</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
                        <div>
                            <span class="font-medium">Student:</span>
                            <span>Student User</span>
                        </div>
                        <div class="text-right text-xs">
                            <div>student@campus.com</div>
                            <div>student123</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
                        <div>
                            <span class="font-medium">Driver:</span>
                            <span>Driver User</span>
                        </div>
                        <div class="text-right text-xs">
                            <div>driver@campus.com</div>
                            <div>driver123</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
