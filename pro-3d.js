// Professional 3D Globe with Real-time Tracking
class TransportGlobe {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.globe = null;
        this.markers = [];
        this.routes = [];
        this.vehicles = [];
        this.init();
    }

    init() {
        // Scene setup
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x0a0a0a);

        // Camera setup
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
        this.camera.position.z = 2.5;

        // Renderer setup
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.container.appendChild(this.renderer.domElement);

        // Lighting
        const ambientLight = new THREE.AmbientLight(0x404040, 2);
        this.scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(5, 3, 5);
        this.scene.add(directionalLight);

        // Create globe
        this.createGlobe();
        
        // Add markers for locations
        this.addLocationMarkers();
        
        // Add vehicle routes
        this.addVehicleRoutes();
        
        // Add animated vehicles
        this.addAnimatedVehicles();

        // Controls
        this.addControls();

        // Start animation
        this.animate();

        // Handle resize
        window.addEventListener('resize', () => this.onWindowResize());
    }

    createGlobe() {
        // Globe geometry
        const geometry = new THREE.SphereGeometry(1, 64, 64);
        
        // Create gradient material
        const material = new THREE.MeshPhongMaterial({
            color: 0x2a4d69,
            emissive: 0x1a2f42,
            shininess: 10,
            wireframe: false
        });

        this.globe = new THREE.Mesh(geometry, material);
        this.scene.add(this.globe);

        // Add wireframe overlay
        const wireframeGeometry = new THREE.SphereGeometry(1.01, 32, 32);
        const wireframeMaterial = new THREE.MeshBasicMaterial({
            color: 0x667eea,
            wireframe: true,
            transparent: true,
            opacity: 0.3
        });
        const wireframe = new THREE.Mesh(wireframeGeometry, wireframeMaterial);
        this.globe.add(wireframe);

        // Add atmosphere glow
        const atmosphereGeometry = new THREE.SphereGeometry(1.2, 32, 32);
        const atmosphereMaterial = new THREE.MeshBasicMaterial({
            color: 0x667eea,
            transparent: true,
            opacity: 0.1,
            side: THREE.BackSide
        });
        const atmosphere = new THREE.Mesh(atmosphereGeometry, atmosphereMaterial);
        this.scene.add(atmosphere);
    }

    addLocationMarkers() {
        // Sample locations (latitude, longitude)
        const locations = [
            { name: 'New York', lat: 40.7128, lng: -74.0060, color: 0x667eea },
            { name: 'London', lat: 51.5074, lng: -0.1278, color: 0x764ba2 },
            { name: 'Tokyo', lat: 35.6762, lng: 139.6503, color: 0xf093fb },
            { name: 'Sydney', lat: -33.8688, lng: 151.2093, color: 0x13B497 },
            { name: 'Dubai', lat: 25.2048, lng: 55.2708, color: 0xF59E0B }
        ];

        locations.forEach(location => {
            const marker = this.createLocationMarker(location);
            this.markers.push(marker);
            this.scene.add(marker);
        });
    }

    createLocationMarker(location) {
        const group = new THREE.Group();

        // Convert lat/lng to 3D coordinates
        const coords = this.latLngToVector3(location.lat, location.lng, 1.05);

        // Marker sphere
        const geometry = new THREE.SphereGeometry(0.02, 16, 16);
        const material = new THREE.MeshBasicMaterial({
            color: location.color,
            emissive: location.color,
            emissiveIntensity: 0.5
        });
        const sphere = new THREE.Mesh(geometry, material);
        sphere.position.copy(coords);
        group.add(sphere);

        // Pulsing ring
        const ringGeometry = new THREE.RingGeometry(0.03, 0.05, 32);
        const ringMaterial = new THREE.MeshBasicMaterial({
            color: location.color,
            transparent: true,
            opacity: 0.5,
            side: THREE.DoubleSide
        });
        const ring = new THREE.Mesh(ringGeometry, ringMaterial);
        ring.position.copy(coords);
        ring.lookAt(new THREE.Vector3(0, 0, 0));
        group.add(ring);

        // Animate ring
        this.animateRing(ring);

        return group;
    }

    animateRing(ring) {
        const animate = () => {
            ring.scale.x += 0.01;
            ring.scale.y += 0.01;
            ring.material.opacity -= 0.01;
            
            if (ring.material.opacity > 0) {
                requestAnimationFrame(animate);
            } else {
                // Reset animation
                ring.scale.x = 1;
                ring.scale.y = 1;
                ring.material.opacity = 0.5;
                setTimeout(() => animate(), 2000);
            }
        };
        animate();
    }

    addVehicleRoutes() {
        // Sample routes between locations
        const routes = [
            { from: { lat: 40.7128, lng: -74.0060 }, to: { lat: 51.5074, lng: -0.1278 } },
            { from: { lat: 35.6762, lng: 139.6503 }, to: { lat: -33.8688, lng: 151.2093 } },
            { from: { lat: 25.2048, lng: 55.2708 }, to: { lat: 40.7128, lng: -74.0060 } }
        ];

        routes.forEach(route => {
            const curve = this.createRouteCurve(route.from, route.to);
            const routeLine = this.createRouteLine(curve);
            this.routes.push(routeLine);
            this.scene.add(routeLine);
        });
    }

    createRouteCurve(from, to) {
        const fromVec = this.latLngToVector3(from.lat, from.lng, 1);
        const toVec = this.latLngToVector3(to.lat, to.lng, 1);
        
        // Create curved path
        const midPoint = new THREE.Vector3()
            .addVectors(fromVec, toVec)
            .multiplyScalar(0.5)
            .normalize()
            .multiplyScalar(1.3); // Elevate the curve

        const curve = new THREE.QuadraticBezierCurve3(fromVec, midPoint, toVec);
        return curve;
    }

    createRouteLine(curve) {
        const points = curve.getPoints(50);
        const geometry = new THREE.BufferGeometry().setFromPoints(points);
        
        const material = new THREE.LineBasicMaterial({
            color: 0x667eea,
            transparent: true,
            opacity: 0.6,
            linewidth: 2
        });
        
        return new THREE.Line(geometry, material);
    }

    addAnimatedVehicles() {
        // Add animated vehicles along routes
        for (let i = 0; i < 3; i++) {
            const vehicle = this.createAnimatedVehicle(this.routes[i]);
            this.vehicles.push(vehicle);
            this.scene.add(vehicle);
        }
    }

    createAnimatedVehicle(route) {
        const geometry = new THREE.SphereGeometry(0.015, 16, 16);
        const material = new THREE.MeshBasicMaterial({
            color: 0xf093fb,
            emissive: 0xf093fb,
            emissiveIntensity: 0.8
        });
        const vehicle = new THREE.Mesh(geometry, material);
        
        // Add glow effect
        const glowGeometry = new THREE.SphereGeometry(0.025, 16, 16);
        const glowMaterial = new THREE.MeshBasicMaterial({
            color: 0xf093fb,
            transparent: true,
            opacity: 0.3
        });
        const glow = new THREE.Mesh(glowGeometry, glowMaterial);
        vehicle.add(glow);

        // Store route for animation
        vehicle.userData.route = route;
        vehicle.userData.progress = Math.random(); // Random starting position
        vehicle.userData.speed = 0.001 + Math.random() * 0.002;

        return vehicle;
    }

    addControls() {
        // Simple mouse controls
        let mouseX = 0;
        let mouseY = 0;
        let targetRotationX = 0;
        let targetRotationY = 0;

        this.container.addEventListener('mousemove', (event) => {
            const rect = this.container.getBoundingClientRect();
            mouseX = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouseY = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        });

        const updateRotation = () => {
            targetRotationX = mouseY * 0.5;
            targetRotationY = mouseX * 0.5;
            requestAnimationFrame(updateRotation);
        };
        updateRotation();

        // Smooth rotation
        const smoothRotation = () => {
            if (this.globe) {
                this.globe.rotation.x += (targetRotationX - this.globe.rotation.x) * 0.05;
                this.globe.rotation.y += (targetRotationY - this.globe.rotation.y) * 0.05;
                
                // Auto-rotate slowly
                this.globe.rotation.y += 0.002;
            }
            requestAnimationFrame(smoothRotation);
        };
        smoothRotation();
    }

    latLngToVector3(lat, lng, radius = 1) {
        const phi = (90 - lat) * (Math.PI / 180);
        const theta = (lng + 180) * (Math.PI / 180);

        const x = -(radius * Math.sin(phi) * Math.cos(theta));
        const y = radius * Math.cos(phi);
        const z = radius * Math.sin(phi) * Math.sin(theta);

        return new THREE.Vector3(x, y, z);
    }

    animateVehicles() {
        this.vehicles.forEach(vehicle => {
            const route = vehicle.userData.route;
            const progress = vehicle.userData.progress;
            const speed = vehicle.userData.speed;

            if (route && route.geometry) {
                const points = route.geometry.attributes.position.array;
                const totalPoints = points.length / 3;
                const currentIndex = Math.floor(progress * (totalPoints - 1)) * 3;
                
                const x = points[currentIndex];
                const y = points[currentIndex + 1];
                const z = points[currentIndex + 2];
                
                vehicle.position.set(x, y, z);
                
                // Update progress
                vehicle.userData.progress += speed;
                if (vehicle.userData.progress >= 1) {
                    vehicle.userData.progress = 0;
                }
            }
        });
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        
        // Animate vehicles
        this.animateVehicles();
        
        // Rotate markers
        this.markers.forEach(marker => {
            marker.rotation.y += 0.01;
        });
        
        // Render scene
        this.renderer.render(this.scene, this.camera);
    }

    onWindowResize() {
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        
        this.renderer.setSize(width, height);
    }

    // Public methods for real-time updates
    updateVehiclePosition(vehicleId, lat, lng) {
        // Update specific vehicle position
        const vehicle = this.vehicles[vehicleId];
        if (vehicle) {
            const position = this.latLngToVector3(lat, lng, 1);
            vehicle.position.copy(position);
        }
    }

    addNewRoute(from, to) {
        const curve = this.createRouteCurve(from, to);
        const routeLine = this.createRouteLine(curve);
        this.routes.push(routeLine);
        this.scene.add(routeLine);
        
        // Add vehicle to new route
        const vehicle = this.createAnimatedVehicle(routeLine);
        this.vehicles.push(vehicle);
        this.scene.add(vehicle);
    }
}

// Initialize Three.js library (simplified version)
const THREE = {
    Scene: class {
        constructor() {
            this.children = [];
        }
        add(object) {
            this.children.push(object);
        }
    },
    
    PerspectiveCamera: class {
        constructor(fov, aspect, near, far) {
            this.fov = fov;
            this.aspect = aspect;
            this.near = near;
            this.far = far;
            this.position = { x: 0, y: 0, z: 0 };
        }
    },
    
    WebGLRenderer: class {
        constructor(options) {
            this.domElement = document.createElement('canvas');
        }
        setSize(width, height) {
            this.domElement.width = width;
            this.domElement.height = height;
        }
        setPixelRatio(ratio) {
            this.devicePixelRatio = ratio;
        }
        render(scene, camera) {
            // Simplified rendering
            const ctx = this.domElement.getContext('2d');
            ctx.fillStyle = '#0a0a0a';
            ctx.fillRect(0, 0, this.domElement.width, this.domElement.height);
            
            // Draw globe outline
            ctx.strokeStyle = '#667eea';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(this.domElement.width / 2, this.domElement.height / 2, 150, 0, Math.PI * 2);
            ctx.stroke();
            
            // Draw animated markers
            const time = Date.now() * 0.001;
            for (let i = 0; i < 5; i++) {
                const angle = (i / 5) * Math.PI * 2 + time;
                const x = this.domElement.width / 2 + Math.cos(angle) * 150;
                const y = this.domElement.height / 2 + Math.sin(angle) * 150;
                
                ctx.fillStyle = '#667eea';
                ctx.beginPath();
                ctx.arc(x, y, 5, 0, Math.PI * 2);
                ctx.fill();
                
                // Pulsing effect
                ctx.strokeStyle = '#667eea';
                ctx.globalAlpha = 0.5 - (time % 1) * 0.5;
                ctx.beginPath();
                ctx.arc(x, y, 10 + (time % 1) * 10, 0, Math.PI * 2);
                ctx.stroke();
                ctx.globalAlpha = 1;
            }
        }
    },
    
    SphereGeometry: class {
        constructor(radius, widthSegments, heightSegments) {
            this.radius = radius;
            this.widthSegments = widthSegments;
            this.heightSegments = heightSegments;
        }
    },
    
    MeshPhongMaterial: class {
        constructor(options) {
            Object.assign(this, options);
        }
    },
    
    MeshBasicMaterial: class {
        constructor(options) {
            Object.assign(this, options);
        }
    },
    
    LineBasicMaterial: class {
        constructor(options) {
            Object.assign(this, options);
        }
    },
    
    Mesh: class {
        constructor(geometry, material) {
            this.geometry = geometry;
            this.material = material;
            this.position = { x: 0, y: 0, z: 0 };
            this.rotation = { x: 0, y: 0, z: 0 };
            this.scale = { x: 1, y: 1, z: 1 };
            this.add = (child) => { this.children = this.children || []; this.children.push(child); };
        }
    },
    
    Line: class {
        constructor(geometry, material) {
            this.geometry = geometry;
            this.material = material;
            this.userData = {};
        }
    },
    
    Group: class {
        constructor() {
            this.position = { x: 0, y: 0, z: 0 };
            this.rotation = { x: 0, y: 0, z: 0 };
            this.scale = { x: 1, y: 1, z: 1 };
            this.add = (child) => { this.children = this.children || []; this.children.push(child); };
        }
    },
    
    AmbientLight: class {
        constructor(color, intensity) {
            this.color = color;
            this.intensity = intensity;
        }
    },
    
    DirectionalLight: class {
        constructor(color, intensity) {
            this.color = color;
            this.intensity = intensity;
            this.position = { x: 0, y: 0, z: 0 };
        }
    },
    
    BufferGeometry: class {
        constructor() {
            this.attributes = {};
        }
        setFromPoints(points) {
            this.attributes.position = {
                array: points.flatMap(p => [p.x, p.y, p.z]),
                itemSize: 3
            };
        }
    },
    
    QuadraticBezierCurve3: class {
        constructor(startPoint, controlPoint, endPoint) {
            this.startPoint = startPoint;
            this.controlPoint = controlPoint;
            this.endPoint = endPoint;
        }
        getPoints(divisions) {
            const points = [];
            for (let i = 0; i <= divisions; i++) {
                const t = i / divisions;
                const point = new THREE.Vector3();
                point.x = (1 - t) * (1 - t) * this.startPoint.x + 2 * (1 - t) * t * this.controlPoint.x + t * t * this.endPoint.x;
                point.y = (1 - t) * (1 - t) * this.startPoint.y + 2 * (1 - t) * t * this.controlPoint.y + t * t * this.endPoint.y;
                point.z = (1 - t) * (1 - t) * this.startPoint.z + 2 * (1 - t) * t * this.controlPoint.z + t * t * this.endPoint.z;
                points.push(point);
            }
            return points;
        }
    },
    
    Vector3: class {
        constructor(x = 0, y = 0, z = 0) {
            this.x = x;
            this.y = y;
            this.z = z;
        }
        add(vector) {
            return new THREE.Vector3(this.x + vector.x, this.y + vector.y, this.z + vector.z);
        }
        multiplyScalar(scalar) {
            this.x *= scalar;
            this.y *= scalar;
            this.z *= scalar;
            return this;
        }
        normalize() {
            const length = Math.sqrt(this.x * this.x + this.y * this.y + this.z * this.z);
            this.x /= length;
            this.y /= length;
            this.z /= length;
            return this;
        }
        copy(vector) {
            this.x = vector.x;
            this.y = vector.y;
            this.z = vector.z;
            return this;
        }
        set(x, y, z) {
            this.x = x;
            this.y = y;
            this.z = z;
            return this;
        }
    },
    
    Color: class {
        constructor(color) {
            this.color = color;
        }
    },
    
    RingGeometry: class {
        constructor(innerRadius, outerRadius, segments) {
            this.innerRadius = innerRadius;
            this.outerRadius = outerRadius;
            this.segments = segments;
        }
    }
};

// Export for use in other files
window.TransportGlobe = TransportGlobe;
window.THREE = THREE;
