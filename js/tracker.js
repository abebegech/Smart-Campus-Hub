// Real-Time Bus Tracker with Leaflet.js and Firebase
class BusTracker {
    constructor() {
        this.map = null;
        this.busMarkers = {};
        this.stopMarkers = {};
        this.routeLines = {};
        this.firebase = null;
        this.updateInterval = null;
        this.init();
    }

    async init() {
        // Initialize Firebase
        await this.initFirebase();
        
        // Initialize map
        this.initMap();
        
        // Start real-time updates
        this.startRealTimeUpdates();
        
        // Load initial data
        await this.loadInitialData();
    }

    async initFirebase() {
        // Load Firebase config
        try {
            const response = await fetch('config/firebase.php');
            const config = await response.json();
            
            // Initialize Firebase (using REST API for simplicity)
            this.firebase = {
                databaseURL: 'https://transport-tracking-default-rtdb.firebaseio.com',
                apiKey: 'AIzaSyDemoKeyForTransportTracking123456789'
            };
        } catch (error) {
            console.error('Firebase initialization failed:', error);
            // Fallback to mock data
            this.useMockData = true;
        }
    }

    initMap() {
        // Initialize Leaflet map centered on campus
        this.map = L.map('map').setView([9.1450, 40.4897], 13); // Ethiopia campus coordinates

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(this.map);

        // Add custom bus icon
        this.busIcon = L.divIcon({
            html: '<div style="background-color: #3498db; color: white; padding: 5px; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold;">BUS</div>',
            iconSize: [30, 30],
            className: 'bus-marker'
        });

        // Add stop icon
        this.stopIcon = L.divIcon({
            html: '<div style="background-color: #e74c3c; color: white; padding: 5px; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center;">STOP</div>',
            iconSize: [25, 25],
            className: 'stop-marker'
        });
    }

    async loadInitialData() {
        // Load bus stops
        await this.loadBusStops();
        
        // Load routes
        await this.loadRoutes();
        
        // Load current bus positions
        await this.loadBusPositions();
    }

    async loadBusStops() {
        try {
            const response = await fetch('api/get_stops.php');
            const stops = await response.json();
            
            stops.forEach(stop => {
                const marker = L.marker([stop.latitude, stop.longitude], {
                    icon: this.stopIcon
                }).addTo(this.map);
                
                marker.bindPopup(`<b>${stop.name}</b><br>Bus Stop`);
                
                this.stopMarkers[stop.id] = marker;
            });
        } catch (error) {
            console.error('Failed to load bus stops:', error);
            this.addMockStops();
        }
    }

    async loadRoutes() {
        try {
            const response = await fetch('api/get_routes.php');
            const routes = await response.json();
            
            routes.forEach(route => {
                if (route.coordinates && route.coordinates.length > 0) {
                    const polyline = L.polyline(route.coordinates, {
                        color: this.getRouteColor(route.id),
                        weight: 4,
                        opacity: 0.7
                    }).addTo(this.map);
                    
                    this.routeLines[route.id] = polyline;
                }
            });
        } catch (error) {
            console.error('Failed to load routes:', error);
            this.addMockRoutes();
        }
    }

    async loadBusPositions() {
        if (this.useMockData) {
            this.addMockBuses();
            return;
        }

        try {
            const response = await fetch(`${this.firebase.databaseURL}/buses.json`);
            const buses = await response.json();
            
            Object.keys(buses).forEach(busId => {
                const bus = buses[busId];
                if (bus.latitude && bus.longitude) {
                    this.updateBusMarker(busId, bus);
                }
            });
        } catch (error) {
            console.error('Failed to load bus positions:', error);
            this.addMockBuses();
        }
    }

    updateBusMarker(busId, busData) {
        const position = [busData.latitude, busData.longitude];
        
        if (this.busMarkers[busId]) {
            // Update existing marker position with smooth animation
            this.busMarkers[busId].setLatLng(position);
        } else {
            // Create new marker
            const marker = L.marker(position, {
                icon: this.busIcon
            }).addTo(this.map);
            
            const popupContent = `
                <b>Bus ${busId}</b><br>
                Route: ${busData.route_id || 'Unknown'}<br>
                Speed: ${busData.speed || 0} km/h<br>
                Last Update: ${new Date(busData.timestamp * 1000).toLocaleTimeString()}
            `;
            
            marker.bindPopup(popupContent);
            this.busMarkers[busId] = marker;
        }
        
        // Update popup content
        if (this.busMarkers[busId]) {
            const popupContent = `
                <b>Bus ${busId}</b><br>
                Route: ${busData.route_id || 'Unknown'}<br>
                Speed: ${busData.speed || 0} km/h<br>
                Last Update: ${new Date(busData.timestamp * 1000).toLocaleTimeString()}
            `;
            this.busMarkers[busId].setPopupContent(popupContent);
        }
    }

    startRealTimeUpdates() {
        // Update bus positions every 5 seconds
        this.updateInterval = setInterval(async () => {
            await this.updateBusPositions();
        }, 5000);
    }

    async updateBusPositions() {
        if (this.useMockData) {
            this.simulateBusMovement();
            return;
        }

        try {
            const response = await fetch(`${this.firebase.databaseURL}/buses.json`);
            const buses = await response.json();
            
            Object.keys(buses).forEach(busId => {
                const bus = buses[busId];
                if (bus.latitude && bus.longitude) {
                    this.updateBusMarker(busId, bus);
                }
            });
        } catch (error) {
            console.error('Failed to update bus positions:', error);
        }
    }

    // Mock data for demonstration
    addMockStops() {
        const mockStops = [
            { id: 1, name: 'Main Campus Gate', latitude: 9.1450, longitude: 40.4897 },
            { id: 2, name: 'Library', latitude: 9.1460, longitude: 40.4907 },
            { id: 3, name: 'Student Center', latitude: 9.1440, longitude: 40.4887 },
            { id: 4, name: 'Science Building', latitude: 9.1470, longitude: 40.4917 },
            { id: 5, name: 'Dormitory A', latitude: 9.1430, longitude: 40.4877 }
        ];

        mockStops.forEach(stop => {
            const marker = L.marker([stop.latitude, stop.longitude], {
                icon: this.stopIcon
            }).addTo(this.map);
            
            marker.bindPopup(`<b>${stop.name}</b><br>Bus Stop`);
            
            this.stopMarkers[stop.id] = marker;
        });
    }

    addMockRoutes() {
        const mockRoute = [
            [9.1450, 40.4897],
            [9.1460, 40.4907],
            [9.1470, 40.4917],
            [9.1480, 40.4927]
        ];

        const polyline = L.polyline(mockRoute, {
            color: '#3498db',
            weight: 4,
            opacity: 0.7
        }).addTo(this.map);
        
        this.routeLines[1] = polyline;
    }

    addMockBuses() {
        const mockBuses = [
            { id: 'BUS001', latitude: 9.1450, longitude: 40.4897, route_id: 1, speed: 25 },
            { id: 'BUS002', latitude: 9.1460, longitude: 40.4907, route_id: 1, speed: 30 }
        ];

        mockBuses.forEach(bus => {
            this.updateBusMarker(bus.id, bus);
        });

        // Simulate movement
        this.simulateBusMovement();
    }

    simulateBusMovement() {
        Object.keys(this.busMarkers).forEach(busId => {
            const marker = this.busMarkers[busId];
            const currentPos = marker.getLatLng();
            
            // Simulate small movement
            const newLat = currentPos.lat + (Math.random() - 0.5) * 0.001;
            const newLng = currentPos.lng + (Math.random() - 0.5) * 0.001;
            
            marker.setLatLng([newLat, newLng]);
            
            // Update popup
            const popupContent = `
                <b>${busId}</b><br>
                Route: 1<br>
                Speed: ${Math.floor(Math.random() * 20 + 20)} km/h<br>
                Last Update: ${new Date().toLocaleTimeString()}
            `;
            marker.setPopupContent(popupContent);
        });
    }

    getRouteColor(routeId) {
        const colors = ['#3498db', '#e74c3c', '#27ae60', '#f39c12', '#9b59b6'];
        return colors[routeId % colors.length];
    }

    // Calculate ETA using Haversine formula
    calculateETA(busLat, busLng, stopLat, stopLng, avgSpeed = 30) {
        const R = 6371; // Earth's radius in km
        const dLat = this.toRad(stopLat - busLat);
        const dLng = this.toRad(stopLng - busLng);
        
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(this.toRad(busLat)) * Math.cos(this.toRad(stopLat)) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c; // Distance in km
        
        const timeHours = distance / avgSpeed;
        const timeMinutes = Math.round(timeHours * 60);
        
        return timeMinutes;
    }

    toRad(deg) {
        return deg * (Math.PI/180);
    }

    // Get ETA for all buses to a specific stop
    getETAForStop(stopId) {
        const stop = this.stopMarkers[stopId];
        if (!stop) return [];

        const stopPos = stop.getLatLng();
        const etaData = [];

        Object.keys(this.busMarkers).forEach(busId => {
            const busMarker = this.busMarkers[busId];
            const busPos = busMarker.getLatLng();
            
            const eta = this.calculateETA(
                busPos.lat, busPos.lng,
                stopPos.lat, stopPos.lng
            );
            
            etaData.push({
                busId: busId,
                eta: eta,
                distance: this.calculateDistance(busPos.lat, busPos.lng, stopPos.lat, stopPos.lng)
            });
        });

        return etaData.sort((a, b) => a.eta - b.eta);
    }

    calculateDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // Earth's radius in km
        const dLat = this.toRad(lat2 - lat1);
        const dLng = this.toRad(lng2 - lng1);
        
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(this.toRad(lat1)) * Math.cos(this.toRad(lat2)) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Cleanup
    destroy() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
        
        // Remove all markers and layers
        Object.values(this.busMarkers).forEach(marker => this.map.removeLayer(marker));
        Object.values(this.stopMarkers).forEach(marker => this.map.removeLayer(marker));
        Object.values(this.routeLines).forEach(line => this.map.removeLayer(line));
    }
}

// Initialize tracker when page loads
document.addEventListener('DOMContentLoaded', function() {
    window.busTracker = new BusTracker();
});

// Export for use in other files
window.BusTracker = BusTracker;
