# Transport Tracking System

A comprehensive transport management system with GPS tracking, booking, driver management, and beautiful 3D animated interface.

## Features

### Core Features
- **User Authentication**: Secure login system for admin and users
- **Driver Management**: Complete driver profiles with photos, licenses, and tracking
- **Vehicle Management**: Fleet management with status tracking
- **Route Management**: Route creation with waypoints and scheduling
- **Booking System**: Complete booking workflow with real-time tracking
- **GPS Integration**: Real-time location tracking and route optimization
- **Dashboard**: Beautiful 3D animated dashboard with real-time metrics

### Advanced Features
- **3D Animations**: Interactive 3D globe and visualizations
- **Real-time Updates**: Live tracking via WebSocket connections
- **Responsive Design**: Works seamlessly on all devices
- **Beautiful UI**: Modern Material Design with gradients and animations
- **File Upload**: Support for driver photos and license documents
- **Role-based Access**: Admin, user, and driver roles
- **Performance Metrics**: On-time performance, satisfaction ratings, utilization

## Technology Stack

### Backend
- **Node.js** with Express.js
- **MongoDB** with Mongoose ODM
- **Socket.io** for real-time communication
- **JWT** for authentication
- **Multer** for file uploads
- **Bcrypt** for password hashing

### Frontend
- **React** with TypeScript
- **Material-UI** for beautiful components
- **Three.js** for 3D animations
- **Framer Motion** for smooth animations
- **React Query** for data management
- **React Router** for navigation
- **Axios** for API calls

## Installation & Setup

### Prerequisites
- Node.js (v14 or higher)
- MongoDB (v4.4 or higher)
- npm or yarn

### 1. Clone the Repository
```bash
git clone <repository-url>
cd TransportTracking
```

### 2. Install Backend Dependencies
```bash
npm install
```

### 3. Setup Environment Variables
Create a `.env` file in the root directory:
```env
PORT=5000
MONGODB_URI=mongodb://localhost:27017/transport_tracking
JWT_SECRET=your_jwt_secret_key_here_change_in_production
NODE_ENV=development
UPLOAD_PATH=uploads
GPS_API_KEY=your_gps_api_key_here
```

### 4. Setup Upload Directories
```bash
mkdir uploads
mkdir uploads/drivers
mkdir uploads/vehicles
```

### 5. Install Frontend Dependencies
```bash
cd client
npm install
```

### 6. Start MongoDB
Make sure MongoDB is running on your system:
```bash
# For Windows with XAMPP
# Start MongoDB service from XAMPP control panel

# For other systems
mongod
```

### 7. Start the Application

#### Development Mode (Recommended)
```bash
# From root directory
npm run dev
```
This will start both backend and frontend concurrently.

#### Manual Start
```bash
# Terminal 1 - Backend
npm run server

# Terminal 2 - Frontend
cd client
npm start
```

### 8. Access the Application
- Frontend: http://localhost:3000
- Backend API: http://localhost:5000
- API Health Check: http://localhost:5000/api/health

## Default Login Credentials

### Admin Account
- **Email**: admin@transport.com
- **Password**: admin123

### User Account
- **Email**: user@transport.com
- **Password**: user123

## API Documentation

### Authentication Endpoints
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/auth/me` - Get current user
- `PUT /api/auth/profile` - Update profile
- `PUT /api/auth/password` - Change password

### Drivers Endpoints
- `GET /api/drivers` - Get all drivers
- `GET /api/drivers/:id` - Get driver by ID
- `POST /api/drivers` - Create new driver
- `PUT /api/drivers/:id` - Update driver
- `PUT /api/drivers/:id/location` - Update driver location
- `PUT /api/drivers/:id/availability` - Update availability

### Vehicles Endpoints
- `GET /api/vehicles` - Get all vehicles
- `GET /api/vehicles/:id` - Get vehicle by ID
- `POST /api/vehicles` - Create new vehicle
- `PUT /api/vehicles/:id` - Update vehicle
- `PUT /api/vehicles/:id/status` - Update vehicle status
- `PUT /api/vehicles/:id/assign-driver` - Assign driver

### Routes Endpoints
- `GET /api/routes` - Get all routes
- `GET /api/routes/:id` - Get route by ID
- `POST /api/routes` - Create new route
- `PUT /api/routes/:id` - Update route
- `GET /api/routes/search/nearby` - Find nearby routes

### Bookings Endpoints
- `GET /api/bookings` - Get all bookings
- `GET /api/bookings/:id` - Get booking by ID
- `POST /api/bookings` - Create new booking
- `PUT /api/bookings/:id` - Update booking
- `PUT /api/bookings/:id/status` - Update booking status
- `PUT /api/bookings/:id/tracking` - Update tracking

### GPS Endpoints
- `GET /api/gps/geocode` - Convert address to coordinates
- `GET /api/gps/reverse-geocode` - Convert coordinates to address
- `GET /api/gps/route` - Get route between points
- `GET /api/gps/traffic` - Get traffic information

## Database Schema

### Users Collection
```javascript
{
  username: String,
  email: String,
  password: String (hashed),
  role: String ('admin', 'user', 'driver'),
  profile: {
    firstName: String,
    lastName: String,
    phone: String,
    address: String,
    profileImage: String
  },
  isActive: Boolean,
  lastLogin: Date
}
```

### Drivers Collection
```javascript
{
  user: ObjectId (ref: 'User'),
  licenseNumber: String,
  licenseExpiry: Date,
  licenseImage: String,
  driverPhoto: String,
  experience: Number,
  rating: Number,
  currentLocation: {
    latitude: Number,
    longitude: Number,
    lastUpdated: Date
  },
  isAvailable: Boolean,
  vehicleAssigned: ObjectId (ref: 'Vehicle')
}
```

### Vehicles Collection
```javascript
{
  registrationNumber: String,
  make: String,
  model: String,
  year: Number,
  type: String ('bus', 'truck', 'van', 'car', 'motorcycle'),
  capacity: Number,
  fuelType: String,
  status: String ('active', 'maintenance', 'inactive', 'in_transit'),
  currentLocation: {
    latitude: Number,
    longitude: Number,
    lastUpdated: Date
  },
  driver: ObjectId (ref: 'Driver')
}
```

### Routes Collection
```javascript
{
  name: String,
  code: String,
  startLocation: {
    name: String,
    latitude: Number,
    longitude: Number,
    address: String
  },
  endLocation: {
    name: String,
    latitude: Number,
    longitude: Number,
    address: String
  },
  waypoints: Array,
  distance: Number,
  estimatedDuration: Number,
  fare: Number,
  routeType: String ('regular', 'express', 'special'),
  isActive: Boolean
}
```

### Bookings Collection
```javascript
{
  bookingReference: String,
  passenger: ObjectId (ref: 'User'),
  route: ObjectId (ref: 'Route'),
  vehicle: ObjectId (ref: 'Vehicle'),
  driver: ObjectId (ref: 'Driver'),
  pickupLocation: Object,
  dropoffLocation: Object,
  scheduledDate: Date,
  scheduledTime: String,
  status: String ('pending', 'confirmed', 'driver_assigned', 'in_progress', 'completed', 'cancelled'),
  fare: Object,
  tracking: Object,
  feedback: Object
}
```

## Features in Detail

### Dashboard
- **3D Animated Globe**: Interactive visualization of global tracking
- **Real-time Metrics**: Live statistics for bookings, drivers, vehicles
- **Performance Charts**: On-time performance, satisfaction ratings
- **Recent Activity**: Live feed of system events
- **Quick Actions**: Easy access to common tasks

### Driver Management
- **Complete Profiles**: Photos, licenses, contact information
- **Availability Tracking**: Real-time availability status
- **Location Tracking**: GPS-based location updates
- **Performance Metrics**: Ratings, trip history, experience
- **Document Management**: License uploads and expiry tracking

### Vehicle Management
- **Fleet Overview**: Complete vehicle inventory
- **Status Tracking**: Active, maintenance, inactive status
- **Driver Assignment**: Easy driver-vehicle pairing
- **Location Tracking**: Real-time vehicle positions
- **Maintenance Scheduling**: Service reminders and tracking

### Route Management
- **Route Creation**: Start/end points, waypoints, distances
- **Scheduling**: Departure times and frequency
- **Fare Management**: Pricing structure and calculations
- **Route Types**: Regular, express, special routes
- **Active/Inactive**: Easy route activation

### Booking System
- **Complete Workflow**: From booking to completion
- **Real-time Tracking**: Live position updates
- **Status Management**: Pending to completed workflow
- **Payment Integration**: Fare calculation and tracking
- **Feedback System**: Ratings and reviews

## Security Features

- **JWT Authentication**: Secure token-based authentication
- **Password Hashing**: Bcrypt for secure password storage
- **Role-based Access**: Admin, user, and driver permissions
- **Input Validation**: Comprehensive input sanitization
- **Rate Limiting**: API endpoint protection
- **File Upload Security**: Safe file handling and validation

## Performance Optimizations

- **Database Indexing**: Optimized query performance
- **Caching**: React Query for efficient data management
- **Lazy Loading**: Component and route-based code splitting
- **Image Optimization**: Compressed images and lazy loading
- **API Optimization**: Efficient data fetching and updates

## Troubleshooting

### Common Issues

#### MongoDB Connection Error
```bash
# Check if MongoDB is running
mongod --version

# Start MongoDB service
# For XAMPP: Start MongoDB from control panel
# For other systems: sudo systemctl start mongod
```

#### Port Already in Use
```bash
# Find process using port 5000
netstat -ano | findstr :5000

# Kill process (Windows)
taskkill /PID <PID> /F

# Change port in .env file
PORT=5001
```

#### Node Modules Issues
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and package-lock.json
rm -rf node_modules package-lock.json

# Reinstall dependencies
npm install
```

#### Frontend Build Issues
```bash
cd client
npm run build

# Check for TypeScript errors
npx tsc --noEmit
```

### Development Tips

1. **Hot Reload**: Both frontend and backend support hot reload in development
2. **API Testing**: Use Postman or similar tools to test API endpoints
3. **Database Viewing**: Use MongoDB Compass to view database contents
4. **Console Logging**: Check browser console for frontend errors
5. **Server Logs**: Check terminal for backend error messages

## Production Deployment

### Environment Setup
```env
NODE_ENV=production
PORT=5000
MONGODB_URI=mongodb://your-production-db-url
JWT_SECRET=your-secure-production-secret
UPLOAD_PATH=uploads
GPS_API_KEY=your-production-gps-api-key
```

### Build Commands
```bash
# Build frontend
cd client
npm run build

# Start production server
cd ..
npm start
```

### Deployment Considerations
- Use HTTPS in production
- Configure proper CORS settings
- Set up reverse proxy (nginx/Apache)
- Configure SSL certificates
- Set up monitoring and logging
- Implement backup strategies

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team or create an issue in the repository.

---

**Transport Tracking System** - Advanced Transport Management Solution
