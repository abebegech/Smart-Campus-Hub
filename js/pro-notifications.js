// Professional Notification System with Real-time Updates
class ProNotificationSystem {
    constructor() {
        this.notifications = [];
        this.container = null;
        this.soundEnabled = true;
        this.maxNotifications = 5;
        this.init();
    }

    init() {
        this.createNotificationContainer();
        this.setupEventListeners();
        this.startRealTimeMonitoring();
        this.loadStoredNotifications();
    }

    createNotificationContainer() {
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
    }

    setupEventListeners() {
        // Listen for custom notification events
        document.addEventListener('showNotification', (e) => {
            this.showNotification(e.detail.type, e.detail.title, e.detail.message, e.detail.options);
        });

        // Listen for real-time updates
        document.addEventListener('realTimeUpdate', (e) => {
            this.handleRealTimeUpdate(e.detail);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'N') {
                e.preventDefault();
                this.showNotification('info', 'Notifications', `${this.notifications.length} active notifications`);
            }
        });
    }

    showNotification(type, title, message, options = {}) {
        const notification = this.createNotificationElement(type, title, message, options);
        
        // Add to container
        this.container.appendChild(notification);
        
        // Enable pointer events for this notification
        notification.style.pointerEvents = 'auto';
        
        // Add to notifications array
        const notificationData = {
            id: Date.now(),
            type,
            title,
            message,
            timestamp: new Date(),
            element: notification
        };
        this.notifications.unshift(notificationData);
        
        // Limit notifications
        if (this.notifications.length > this.maxNotifications) {
            const removed = this.notifications.pop();
            if (removed.element && removed.element.parentNode) {
                removed.element.remove();
            }
        }
        
        // Play sound if enabled
        if (this.soundEnabled && options.sound !== false) {
            this.playNotificationSound(type);
        }
        
        // Auto-remove after duration
        const duration = options.duration || this.getDurationForType(type);
        setTimeout(() => {
            this.removeNotification(notificationData.id);
        }, duration);
        
        // Store notification
        this.storeNotification(notificationData);
        
        return notificationData.id;
    }

    createNotificationElement(type, title, message, options) {
        const notification = document.createElement('div');
        notification.className = `pro-notification pro-notification-${type}`;
        notification.style.cssText = `
            background: ${this.getNotificationBackground(type)};
            border: 1px solid ${this.getNotificationBorder(type)};
            border-radius: 12px;
            padding: 16px;
            min-width: 320px;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            animation: slideInRight 0.3s ease-out;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        `;

        // Add shimmer effect
        const shimmer = document.createElement('div');
        shimmer.style.cssText = `
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        `;
        notification.appendChild(shimmer);

        // Content
        const content = document.createElement('div');
        content.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="color: ${this.getNotificationColor(type)}; font-size: 20px; margin-top: 2px;">
                    ${this.getNotificationIcon(type)}
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: white; margin-bottom: 4px; font-size: 14px;">
                        ${title}
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 13px; line-height: 1.4;">
                        ${message}
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.5); font-size: 11px; margin-top: 6px;">
                        ${this.formatTime(new Date())}
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="
                    background: none;
                    border: none;
                    color: rgba(255, 255, 255, 0.5);
                    cursor: pointer;
                    padding: 4px;
                    border-radius: 4px;
                    transition: all 0.2s ease;
                " onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        notification.appendChild(content);

        // Progress bar
        if (options.showProgress !== false) {
            const progressBar = document.createElement('div');
            progressBar.style.cssText = `
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                background: ${this.getNotificationColor(type)};
                border-radius: 0 0 12px 12px;
                animation: progressOut ${options.duration || this.getDurationForType(type)}ms linear;
            `;
            notification.appendChild(progressBar);
        }

        // Click handler
        notification.addEventListener('click', () => {
            this.handleNotificationClick(notificationData, options);
        });

        return notification;
    }

    getNotificationBackground(type) {
        const backgrounds = {
            success: 'linear-gradient(135deg, rgba(19, 180, 151, 0.9), rgba(14, 165, 233, 0.9))',
            error: 'linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(245, 158, 11, 0.9))',
            warning: 'linear-gradient(135deg, rgba(245, 158, 11, 0.9), rgba(239, 68, 68, 0.9))',
            info: 'linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9))',
            default: 'linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9))'
        };
        return backgrounds[type] || backgrounds.default;
    }

    getNotificationBorder(type) {
        const borders = {
            success: 'rgba(19, 180, 151, 0.5)',
            error: 'rgba(239, 68, 68, 0.5)',
            warning: 'rgba(245, 158, 11, 0.5)',
            info: 'rgba(102, 126, 234, 0.5)',
            default: 'rgba(102, 126, 234, 0.5)'
        };
        return borders[type] || borders.default;
    }

    getNotificationColor(type) {
        const colors = {
            success: '#13B497',
            error: '#ef4444',
            warning: '#F59E0B',
            info: '#667eea',
            default: '#667eea'
        };
        return colors[type] || colors.default;
    }

    getNotificationIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-exclamation-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>',
            default: '<i class="fas fa-bell"></i>'
        };
        return icons[type] || icons.default;
    }

    getDurationForType(type) {
        const durations = {
            success: 4000,
            error: 6000,
            warning: 5000,
            info: 4000,
            default: 4000
        };
        return durations[type] || durations.default;
    }

    removeNotification(id) {
        const index = this.notifications.findIndex(n => n.id === id);
        if (index !== -1) {
            const notification = this.notifications[index];
            if (notification.element) {
                notification.element.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.element.parentNode) {
                        notification.element.remove();
                    }
                }, 300);
            }
            this.notifications.splice(index, 1);
        }
    }

    handleNotificationClick(notification, options) {
        if (options.onClick) {
            options.onClick(notification);
        } else {
            // Default click behavior - remove notification
            this.removeNotification(notification.id);
        }
    }

    playNotificationSound(type) {
        // Create audio context for notification sounds
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        // Different frequencies for different notification types
        const frequencies = {
            success: 800,
            error: 400,
            warning: 600,
            info: 1000,
            default: 800
        };
        
        oscillator.frequency.value = frequencies[type] || frequencies.default;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    }

    startRealTimeMonitoring() {
        // Monitor for system events
        setInterval(() => {
            this.checkSystemEvents();
        }, 10000); // Check every 10 seconds
    }

    checkSystemEvents() {
        // Simulate real-time events
        const events = [
            { type: 'info', title: 'System Update', message: 'All systems operational', weight: 0.1 },
            { type: 'success', title: 'New Booking', message: 'Booking #BK123456 confirmed', weight: 0.3 },
            { type: 'warning', title: 'Driver Alert', message: 'Driver John is running late', weight: 0.2 },
            { type: 'info', title: 'Vehicle Update', message: 'Vehicle #V123 is now available', weight: 0.2 },
            { type: 'success', title: 'Payment Received', message: 'Payment of $45.00 processed', weight: 0.2 }
        ];

        // Randomly show events based on weight
        events.forEach(event => {
            if (Math.random() < event.weight) {
                this.showNotification(event.type, event.title, event.message);
            }
        });
    }

    handleRealTimeUpdate(data) {
        const { type, entity, action, details } = data;
        
        const messages = {
            booking: {
                created: { type: 'success', title: 'New Booking', message: `Booking ${details.reference} created` },
                updated: { type: 'info', title: 'Booking Updated', message: `Booking ${details.reference} status changed` },
                cancelled: { type: 'warning', title: 'Booking Cancelled', message: `Booking ${details.reference} was cancelled` }
            },
            driver: {
                assigned: { type: 'info', title: 'Driver Assigned', message: `${details.name} assigned to route ${details.route}` },
                available: { type: 'success', title: 'Driver Available', message: `${details.name} is now available` },
                offline: { type: 'warning', title: 'Driver Offline', message: `${details.name} went offline` }
            },
            vehicle: {
                active: { type: 'success', title: 'Vehicle Active', message: `Vehicle ${details.plate} is now active` },
                maintenance: { type: 'warning', title: 'Vehicle Maintenance', message: `Vehicle ${details.plate} needs maintenance` },
                assigned: { type: 'info', title: 'Vehicle Assigned', message: `Vehicle ${details.plate} assigned to driver` }
            }
        };

        const messageConfig = messages[entity]?.[action];
        if (messageConfig) {
            this.showNotification(
                messageConfig.type,
                messageConfig.title,
                messageConfig.message,
                { sound: true, duration: 5000 }
            );
        }
    }

    storeNotification(notification) {
        // Store in localStorage for persistence
        const stored = localStorage.getItem('pro_notifications');
        const notifications = stored ? JSON.parse(stored) : [];
        notifications.unshift(notification);
        
        // Keep only last 50 notifications
        const limited = notifications.slice(0, 50);
        localStorage.setItem('pro_notifications', JSON.stringify(limited));
    }

    loadStoredNotifications() {
        const stored = localStorage.getItem('pro_notifications');
        if (stored) {
            const notifications = JSON.parse(stored);
            // Show recent notifications (last 5 minutes)
            const recent = notifications.filter(n => 
                new Date(n.timestamp) > new Date(Date.now() - 5 * 60 * 1000)
            );
            recent.forEach(n => {
                if (new Date(n.timestamp) > new Date(Date.now() - 30 * 1000)) {
                    // Only show notifications from last 30 seconds
                    this.showNotification(n.type, n.title, n.message, { 
                        sound: false, 
                        duration: 3000 
                    });
                }
            });
        }
    }

    formatTime(date) {
        return date.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Public API methods
    success(title, message, options = {}) {
        return this.showNotification('success', title, message, options);
    }

    error(title, message, options = {}) {
        return this.showNotification('error', title, message, options);
    }

    warning(title, message, options = {}) {
        return this.showNotification('warning', title, message, options);
    }

    info(title, message, options = {}) {
        return this.showNotification('info', title, message, options);
    }

    clear() {
        this.notifications.forEach(n => {
            if (n.element && n.element.parentNode) {
                n.element.remove();
            }
        });
        this.notifications = [];
    }

    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        this.info('Sound Settings', `Notifications sounds ${this.soundEnabled ? 'enabled' : 'disabled'}`);
    }

    getCount() {
        return this.notifications.length;
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    @keyframes progressOut {
        from { width: 100%; }
        to { width: 0%; }
    }
    
    .pro-notification {
        transform-origin: top right;
        transition: all 0.3s ease;
    }
    
    .pro-notification:hover {
        transform: scale(1.02);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }
`;
document.head.appendChild(style);

// Initialize notification system
window.proNotifications = new ProNotificationSystem();

// Global helper functions
window.showNotification = function(type, title, message, options) {
    return window.proNotifications.showNotification(type, title, message, options);
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProNotificationSystem;
}
