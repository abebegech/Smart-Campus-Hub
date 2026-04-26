// Transport Tracking System - Main JavaScript
// 3D Animations and Interactive Features

// Global variables
let currentUser = null;
let socket = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Check if user is logged in
    checkAuthStatus();
    
    // Initialize 3D animations
    init3DAnimations();
    
    // Initialize real-time updates
    initRealTimeUpdates();
    
    // Setup form handlers
    setupFormHandlers();
    
    // Initialize dashboard animations
    if (document.getElementById('dashboard')) {
        initDashboard();
    }
}

// Authentication functions
function checkAuthStatus() {
    const loginPage = document.getElementById('login-page');
    const dashboardPage = document.getElementById('dashboard');
    
    // Simple session check (in production, use proper session management)
    const sessionId = sessionStorage.getItem('user_id');
    
    if (sessionId && dashboardPage) {
        showDashboard();
    } else if (!sessionId && loginPage) {
        showLogin();
    }
}

function showLogin() {
    document.getElementById('login-page').style.display = 'flex';
    document.getElementById('dashboard').style.display = 'none';
}

function showDashboard() {
    document.getElementById('login-page').style.display = 'none';
    document.getElementById('dashboard').style.display = 'block';
    loadDashboardData();
}

// Login handler - Let PHP handle the login
function handleLogin(event) {
    // Don't prevent default - let PHP handle the form submission
    // event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Basic client-side validation
    if (!email || !password) {
        showAlert('Please enter email and password', 'danger');
        event.preventDefault();
        return false;
    }
    
    // Show loading and let PHP handle the rest
    showLoading();
    
    // The form will submit to PHP normally
    return true;
}

// Logout handler
function handleLogout() {
    // Clear sessionStorage
    sessionStorage.removeItem('user_id');
    sessionStorage.removeItem('user_email');
    
    // Show message and redirect to PHP logout
    showAlert('Logging out...', 'info');
    
    // Redirect to PHP logout page to destroy server session
    window.location.href = 'logout.php';
}

// 3D Globe Animation
function init3DAnimations() {
    const globeContainer = document.getElementById('globe-container');
    if (!globeContainer) return;
    
    // Create 3D globe using Three.js
    create3DGlobe();
    
    // Create floating particles
    createFloatingParticles();
}

function create3DGlobe() {
    const container = document.getElementById('globe-container');
    if (!container) return;
    
    // Simple CSS 3D globe (fallback if Three.js not available)
    const globe = document.createElement('div');
    globe.className = 'css-globe';
    globe.innerHTML = `
        <div class="globe-sphere">
            <div class="globe-marker marker-1"></div>
            <div class="globe-marker marker-2"></div>
            <div class="globe-marker marker-3"></div>
            <div class="globe-marker marker-4"></div>
        </div>
    `;
    
    container.appendChild(globe);
    
    // Add CSS for globe
    if (!document.getElementById('globe-styles')) {
        const style = document.createElement('style');
        style.id = 'globe-styles';
        style.textContent = `
            .css-globe {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                perspective: 1000px;
            }
            
            .globe-sphere {
                width: 200px;
                height: 200px;
                position: relative;
                transform-style: preserve-3d;
                animation: rotateGlobe 20s linear infinite;
                background: radial-gradient(circle at 30% 30%, #4a90e2, #2c5aa0);
                border-radius: 50%;
                box-shadow: 0 0 50px rgba(74, 144, 226, 0.5);
            }
            
            @keyframes rotateGlobe {
                from { transform: rotateY(0deg) rotateX(10deg); }
                to { transform: rotateY(360deg) rotateX(10deg); }
            }
            
            .globe-marker {
                position: absolute;
                width: 10px;
                height: 10px;
                background: #ff6b6b;
                border-radius: 50%;
                box-shadow: 0 0 10px rgba(255, 107, 107, 0.8);
            }
            
            .marker-1 { top: 20%; left: 30%; animation: pulse 2s infinite; }
            .marker-2 { top: 60%; left: 70%; animation: pulse 2s infinite 0.5s; }
            .marker-3 { top: 40%; left: 50%; animation: pulse 2s infinite 1s; }
            .marker-4 { top: 80%; left: 20%; animation: pulse 2s infinite 1.5s; }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.5); opacity: 0.7; }
            }
        `;
        document.head.appendChild(style);
    }
}

function createFloatingParticles() {
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles-container';
    particlesContainer.innerHTML = `
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
    `;
    
    document.body.appendChild(particlesContainer);
    
    // Add particle styles
    if (!document.getElementById('particle-styles')) {
        const style = document.createElement('style');
        style.id = 'particle-styles';
        style.textContent = `
            .particles-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: -1;
            }
            
            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                animation: floatParticle 15s infinite linear;
            }
            
            .particle-1 { top: 10%; left: 10%; animation-delay: 0s; }
            .particle-2 { top: 30%; left: 80%; animation-delay: 3s; }
            .particle-3 { top: 60%; left: 20%; animation-delay: 6s; }
            .particle-4 { top: 80%; left: 70%; animation-delay: 9s; }
            .particle-5 { top: 40%; left: 50%; animation-delay: 12s; }
            
            @keyframes floatParticle {
                0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Dashboard initialization
function initDashboard() {
    loadDashboardStats();
    initRealTimeTracking();
    initCharts();
}

function loadDashboardData() {
    loadDashboardStats();
    loadRecentActivity();
    loadActiveBookings();
}

function loadDashboardStats() {
    // Simulate API call to get stats
    const stats = {
        totalBookings: 156,
        activeDrivers: 12,
        activeVehicles: 8,
        totalRoutes: 5,
        onTimePerformance: 92,
        customerSatisfaction: 4.8,
        fleetUtilization: 78
    };
    
    updateStatCards(stats);
}

function updateStatCards(stats) {
    const elements = {
        'total-bookings': stats.totalBookings,
        'active-drivers': stats.activeDrivers,
        'active-vehicles': stats.activeVehicles,
        'total-routes': stats.totalRoutes,
        'on-time-performance': stats.onTimePerformance + '%',
        'customer-satisfaction': stats.customerSatisfaction + '/5',
        'fleet-utilization': stats.fleetUtilization + '%'
    };
    
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            animateNumber(element, 0, parseInt(elements[id]) || parseFloat(elements[id]), 2000);
        }
    });
}

function animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = start + (end - start) * progress;
        
        if (element.id.includes('rating') || element.id.includes('performance')) {
            element.textContent = current.toFixed(1);
        } else {
            element.textContent = Math.floor(current);
        }
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

function loadRecentActivity() {
    const container = document.getElementById('recent-activity');
    if (!container) return;
    
    // Simulate recent activity data
    const activities = [
        { type: 'booking', message: 'New booking #BK12345 created', time: '2 min ago', icon: 'booking' },
        { type: 'driver', message: 'Driver John Doe started trip', time: '5 min ago', icon: 'driver' },
        { type: 'vehicle', message: 'Vehicle ABC-123 maintenance completed', time: '15 min ago', icon: 'vehicle' },
        { type: 'route', message: 'Route R-001 updated', time: '30 min ago', icon: 'route' }
    ];
    
    let html = '';
    activities.forEach((activity, index) => {
        html += `
            <div class="activity-item slide-in-left" style="animation-delay: ${index * 0.1}s">
                <div class="activity-icon activity-${activity.type}"></div>
                <div class="activity-content">
                    <div class="activity-message">${activity.message}</div>
                    <div class="activity-time">${activity.time}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function loadActiveBookings() {
    const container = document.getElementById('active-bookings');
    if (!container) return;
    
    // Simulate active bookings
    const bookings = [
        { id: 'BK12345', driver: 'Mike Wilson', vehicle: 'ABC-123', status: 'in_progress', progress: 65 },
        { id: 'BK12346', driver: 'Sarah Johnson', vehicle: 'XYZ-789', status: 'confirmed', progress: 0 },
        { id: 'BK12347', driver: 'Tom Brown', vehicle: 'DEF-456', status: 'driver_assigned', progress: 25 }
    ];
    
    let html = '';
    bookings.forEach(booking => {
        html += `
            <div class="booking-item">
                <div class="booking-header">
                    <span class="booking-id">${booking.id}</span>
                    <span class="badge badge-${getStatusClass(booking.status)}">${booking.status.replace('_', ' ')}</span>
                </div>
                <div class="booking-details">
                    <div>Driver: ${booking.driver}</div>
                    <div>Vehicle: ${booking.vehicle}</div>
                </div>
                <div class="booking-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${booking.progress}%"></div>
                    </div>
                    <span class="progress-text">${booking.progress}%</span>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function getStatusClass(status) {
    const statusClasses = {
        'pending': 'warning',
        'confirmed': 'info',
        'driver_assigned': 'primary',
        'in_progress': 'success',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return statusClasses[status] || 'primary';
}

// Real-time tracking simulation
function initRealTimeTracking() {
    const trackingContainer = document.getElementById('real-time-tracking');
    if (!trackingContainer) return;
    
    // Create simulated map
    createSimulatedMap();
    
    // Start real-time updates
    setInterval(updateVehiclePositions, 3000);
}

function createSimulatedMap() {
    const container = document.getElementById('real-time-tracking');
    if (!container) return;
    
    container.innerHTML = `
        <div class="map-container">
            <div class="map-background">
                <div class="vehicle-marker vehicle-1" data-vehicle="ABC-123">
                    <div class="vehicle-icon">car</div>
                    <div class="vehicle-label">ABC-123</div>
                </div>
                <div class="vehicle-marker vehicle-2" data-vehicle="XYZ-789">
                    <div class="vehicle-icon">car</div>
                    <div class="vehicle-label">XYZ-789</div>
                </div>
                <div class="vehicle-marker vehicle-3" data-vehicle="DEF-456">
                    <div class="vehicle-icon">car</div>
                    <div class="vehicle-label">DEF-456</div>
                </div>
            </div>
            <div class="map-legend">
                <div class="legend-item">
                    <div class="legend-dot in-transit"></div>
                    <span>In Transit</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot active"></div>
                    <span>Active</span>
                </div>
            </div>
        </div>
    `;
    
    // Add map styles
    if (!document.getElementById('map-styles')) {
        const style = document.createElement('style');
        style.id = 'map-styles';
        style.textContent = `
            .map-container {
                position: relative;
                height: 300px;
                background: #f0f0f0;
                border-radius: 10px;
                overflow: hidden;
            }
            
            .map-background {
                position: relative;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, #e8f4f8 25%, transparent 25%),
                            linear-gradient(-45deg, #e8f4f8 25%, transparent 25%),
                            linear-gradient(45deg, transparent 75%, #e8f4f8 75%),
                            linear-gradient(-45deg, transparent 75%, #e8f4f8 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }
            
            .vehicle-marker {
                position: absolute;
                transition: all 2s ease-in-out;
                z-index: 10;
            }
            
            .vehicle-icon {
                width: 30px;
                height: 30px;
                background: #667eea;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 12px;
                font-weight: bold;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            }
            
            .vehicle-label {
                position: absolute;
                top: -20px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 10px;
                white-space: nowrap;
            }
            
            .vehicle-1 { top: 30%; left: 20%; }
            .vehicle-2 { top: 60%; left: 70%; }
            .vehicle-3 { top: 40%; left: 50%; }
            
            .map-legend {
                position: absolute;
                bottom: 10px;
                left: 10px;
                background: rgba(255,255,255,0.9);
                padding: 10px;
                border-radius: 5px;
                font-size: 12px;
            }
            
            .legend-item {
                display: flex;
                align-items: center;
                margin-bottom: 5px;
            }
            
            .legend-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                margin-right: 8px;
            }
            
            .legend-dot.in-transit { background: #28a745; }
            .legend-dot.active { background: #667eea; }
        `;
        document.head.appendChild(style);
    }
}

function updateVehiclePositions() {
    const vehicles = document.querySelectorAll('.vehicle-marker');
    
    vehicles.forEach(vehicle => {
        const currentTop = parseFloat(vehicle.style.top) || 30;
        const currentLeft = parseFloat(vehicle.style.left) || 20;
        
        // Random movement
        const newTop = Math.max(10, Math.min(80, currentTop + (Math.random() - 0.5) * 20));
        const newLeft = Math.max(10, Math.min(80, currentLeft + (Math.random() - 0.5) * 20));
        
        vehicle.style.top = newTop + '%';
        vehicle.style.left = newLeft + '%';
    });
}

// Form handlers
function setupFormHandlers() {
    // Login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
}

// Utility functions
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} fade-in`;
    alert.innerHTML = `
        <span>${message}</span>
        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    alertContainer.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

function showLoading() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loading-overlay';
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div>Loading...</div>
        </div>
    `;
    
    document.body.appendChild(loadingOverlay);
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

// AJAX helper functions
function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    resolve(xhr.responseText);
                }
            } else {
                reject(new Error('Request failed: ' + xhr.statusText));
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        xhr.send(data ? JSON.stringify(data) : null);
    });
}

// Real-time updates (WebSocket simulation)
function initRealTimeUpdates() {
    // Simulate WebSocket connection
    setInterval(() => {
        // Simulate receiving real-time updates
        if (Math.random() > 0.7) {
            const updates = [
                'New booking received',
                'Vehicle location updated',
                'Driver status changed',
                'Route updated'
            ];
            
            const randomUpdate = updates[Math.floor(Math.random() * updates.length)];
            console.log('Real-time update:', randomUpdate);
        }
    }, 10000);
}

// Initialize charts (simple implementation)
function initCharts() {
    // Performance metrics chart
    const chartContainer = document.getElementById('performance-chart');
    if (chartContainer) {
        createPerformanceChart(chartContainer);
    }
}

function createPerformanceChart(container) {
    // Simple CSS-based chart
    container.innerHTML = `
        <div class="chart-container">
            <h4>Performance Metrics</h4>
            <div class="metric-item">
                <div class="metric-label">On-time Performance</div>
                <div class="metric-bar">
                    <div class="metric-fill" style="width: 92%"></div>
                    <span class="metric-value">92%</span>
                </div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Customer Satisfaction</div>
                <div class="metric-bar">
                    <div class="metric-fill" style="width: 96%"></div>
                    <span class="metric-value">4.8/5</span>
                </div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Fleet Utilization</div>
                <div class="metric-bar">
                    <div class="metric-fill" style="width: 78%"></div>
                    <span class="metric-value">78%</span>
                </div>
            </div>
        </div>
    `;
    
    // Add chart styles
    if (!document.getElementById('chart-styles')) {
        const style = document.createElement('style');
        style.id = 'chart-styles';
        style.textContent = `
            .chart-container h4 {
                margin-bottom: 1rem;
                color: #333;
            }
            
            .metric-item {
                margin-bottom: 1rem;
            }
            
            .metric-label {
                font-size: 0.9rem;
                color: #666;
                margin-bottom: 0.5rem;
            }
            
            .metric-bar {
                position: relative;
                height: 20px;
                background: #f0f0f0;
                border-radius: 10px;
                overflow: hidden;
            }
            
            .metric-fill {
                height: 100%;
                background: linear-gradient(45deg, #667eea, #764ba2);
                border-radius: 10px;
                transition: width 1s ease-in-out;
            }
            
            .metric-value {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                color: white;
                font-size: 0.8rem;
                font-weight: bold;
            }
        `;
        document.head.appendChild(style);
    }
}

// Export functions for global access
window.TransportTracker = {
    showAlert,
    showLoading,
    hideLoading,
    ajaxRequest,
    handleLogout
};
