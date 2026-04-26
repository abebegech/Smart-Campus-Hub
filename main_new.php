<?php
require_once 'auth_new.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="flex h-screen">
        <!-- Fixed Sidebar -->
        <div class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col">
            <div class="p-4">
                <h2 class="text-xl font-bold text-white">Smart Campus Hub</h2>
            </div>
            
            <!-- User Info -->
            <div class="px-4 pb-4">
                <div class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold"><?php echo $_SESSION['name']; ?></p>
                        <p class="text-sm text-gray-400"><?php echo ucfirst($_SESSION['role']); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 pb-4 overflow-y-auto">
                <?php
                $menus = getRoleBasedMenu();
                foreach ($menus as $menu):
                ?>
                <a href="main_new.php?page=<?php echo $menu['page']; ?>" 
                   class="flex items-center space-x-3 p-3 rounded-lg mb-2 transition-colors
                          <?php echo ($_GET['page'] ?? 'dashboard') === $menu['page'] ? 'bg-blue-600' : 'hover:bg-gray-700'; ?>">
                    <i data-lucide="<?php echo $menu['icon']; ?>" class="w-5 h-5"></i>
                    <span><?php echo $menu['label']; ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
            
            <!-- Bus Capacity Bars -->
            <div class="px-4 pb-4 border-t border-gray-700 pt-4">
                <h3 class="text-sm font-semibold mb-3 text-gray-300">Bus Capacity</h3>
                <?php
                $buses = getBusCapacityData();
                foreach ($buses as $bus):
                $percentage = getCapacityPercentage($bus['current'], $bus['capacity']);
                $color = getCapacityColor($bus['current'], $bus['capacity']);
                ?>
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400"><?php echo $bus['name']; ?></span>
                        <span class="text-gray-400"><?php echo $bus['current']; ?>/<?php echo $bus['capacity']; ?></span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="<?php echo $color; ?> h-2 rounded-full transition-all duration-300" 
                             style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Logout -->
            <div class="px-4 pb-4">
                <a href="logout_new.php" class="w-full flex items-center justify-center space-x-2 p-3 bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-6">
                <?php
                $page = $_GET['page'] ?? 'dashboard';
                
                switch ($page) {
                    case 'dashboard':
                        include 'pages/dashboard_new.php';
                        break;
                    case 'tracking':
                        include 'pages/tracking_new.php';
                        break;
                    case 'permits':
                        include 'pages/permits_new.php';
                        break;
                    case 'gps_broadcast':
                        include 'pages/gps_broadcast_new.php';
                        break;
                    case 'system':
                        include 'pages/system_working.php';
                        break;
                    case 'users':
                        include 'pages/users_with_images.php';
                        break;
                    case 'reports':
                        include 'pages/reports.php';
                        break;
                    default:
                        include 'pages/dashboard_new.php';
                        break;
                }
                ?>
            </div>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
