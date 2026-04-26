# Transport Tracking System - PHP Version

A comprehensive transport management system built with HTML, PHP, JavaScript, CSS, and MySQL database. Features beautiful 3D animations, real-time GPS tracking, driver management, vehicle fleet management, route planning, and booking system.

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
- **Real-time Updates**: Live tracking with JavaScript animations
- **Responsive Design**: Works seamlessly on all devices
- **Beautiful UI**: Modern design with gradients and animations
- **File Upload**: Support for driver photos and license documents
- **Role-based Access**: Admin, user, and driver roles
- **Performance Metrics**: On-time performance, satisfaction ratings, utilization

## Technology Stack

### Backend
- **PHP 7.4+**: Server-side logic and API
- **MySQL 5.7+**: Database management
- **PDO**: Database connectivity
- **Sessions**: User authentication
- **File Uploads**: Image and document handling

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with animations
- **JavaScript ES6**: Interactivity and 3D effects
- **Font Awesome**: Icons
- **Google Fonts**: Typography

### Database
- **MySQL**: Relational database
- **10 Tables**: Users, Drivers, Vehicles, Routes, Bookings, etc.
- **Indexes**: Optimized performance
- **Foreign Keys**: Data integrity

## Installation & Setup

### Prerequisites
- XAMPP (or similar PHP/MySQL stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### 1. Setup Database

1. **Start MySQL** from XAMPP control panel
2. **Import the database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create new database: `transport_tracking`
   - Import the `database.sql` file
   - Or run the SQL commands manually

### 2. Configure Application

1. **Update database configuration**:
   - Edit `config/database.php`
   - Verify database credentials:
     ```php
     private $host = "localhost";
     private $db_name = "transport_tracking";
     private $username = "root";
     private $password = "";
     ```

2. **Set up file permissions**:
   ```bash
   # Create upload directories
   mkdir uploads/drivers
   mkdir uploads/vehicles
   chmod 755 uploads
   ```

### 3. Access the Application

1. **Start Apache** from XAMPP control panel
2. **Open browser**: `http://localhost/TransportTracking/`
3. **Login with credentials**:
   - Admin: `admin@transport.com` / `admin123`
   - User: `user@transport.com` / `user123`

## File Structure

```
TransportTracking/
|-- config/
|   |-- database.php           # Database configuration
|-- css/
|   |-- style.css              # Main stylesheet with animations
|-- js/
|   |-- main.js                # JavaScript for 3D effects and interactivity
|-- uploads/                  # File upload directory
|   |-- drivers/              # Driver photos and licenses
|   |-- vehicles/             # Vehicle images
|-- database.sql              # Database schema and sample data
|-- index.php                 # Main dashboard
|-- login.php                 # Login page
|-- drivers.php               # Drivers management
|-- vehicles.php             # Vehicles management
|-- routes.php                # Routes management
|-- bookings.php              # Bookings management
|-- README_PHP.md             # This file
```

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'driver') DEFAULT 'user',
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Drivers Table
```sql
CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    license_expiry DATE NOT NULL,
    license_image VARCHAR(255),
    driver_photo VARCHAR(255),
    experience INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 5.00,
    current_latitude DECIMAL(10,8),
    current_longitude DECIMAL(11,8),
    is_available BOOLEAN DEFAULT TRUE,
    vehicle_assigned INT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Vehicles Table
```sql
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(20) UNIQUE NOT NULL,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    type ENUM('bus', 'truck', 'van', 'car', 'motorcycle') NOT NULL,
    capacity INT NOT NULL,
    fuel_type ENUM('petrol', 'diesel', 'electric', 'hybrid') NOT NULL,
    status ENUM('active', 'maintenance', 'inactive', 'in_transit') DEFAULT 'active',
    driver_id INT,
    insurance_policy_number VARCHAR(50),
    registration_expiry DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Routes Table
```sql
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    start_location_name VARCHAR(100) NOT NULL,
    start_latitude DECIMAL(10,8) NOT NULL,
    start_longitude DECIMAL(11,8) NOT NULL,
    end_location_name VARCHAR(100) NOT NULL,
    end_latitude DECIMAL(10,8) NOT NULL,
    end_longitude DECIMAL(11,8) NOT NULL,
    distance DECIMAL(8,2) NOT NULL,
    estimated_duration INT NOT NULL,
    fare DECIMAL(8,2) NOT NULL,
    route_type ENUM('regular', 'express', 'special') DEFAULT 'regular',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Bookings Table
```sql
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    passenger_id INT NOT NULL,
    route_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    driver_id INT,
    pickup_location_name VARCHAR(100),
    scheduled_date DATE NOT NULL,
    scheduled_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'driver_assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    total_fare DECIMAL(8,2),
    payment_method ENUM('cash', 'card', 'wallet', 'subscription') DEFAULT 'cash',
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    special_requests TEXT,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Features in Detail

### Dashboard
- **3D Animated Globe**: Interactive CSS 3D globe with rotating markers
- **Real-time Metrics**: Live statistics for bookings, drivers, vehicles
- **Performance Charts**: On-time performance, satisfaction ratings
- **Recent Activity**: Live feed of system events
- **Active Bookings**: Current bookings with progress tracking
- **Available Drivers**: List of drivers ready for assignment

### Driver Management
- **Complete Profiles**: Photos, licenses, contact information
- **Availability Tracking**: Real-time availability status
- **Location Tracking**: GPS-based location updates
- **Performance Metrics**: Ratings, trip history, experience
- **Document Management**: License uploads and expiry tracking
- **Vehicle Assignment**: Easy driver-vehicle pairing

### Vehicle Management
- **Fleet Overview**: Complete vehicle inventory
- **Status Tracking**: Active, maintenance, inactive status
- **Driver Assignment**: Easy driver-vehicle pairing
- **Location Tracking**: Real-time vehicle positions
- **Maintenance Scheduling**: Service reminders and tracking
- **Insurance Management**: Policy tracking and expiry alerts

### Route Management
- **Route Creation**: Start/end points with coordinates
- **Distance Calculation**: Automatic distance and duration
- **Fare Management**: Pricing structure and calculations
- **Route Types**: Regular, express, special routes
- **Active/Inactive**: Easy route activation management
- **Visual Path Display**: Start to end visualization

### Booking System
- **Complete Workflow**: From booking to completion
- **Real-time Tracking**: Live position updates
- **Status Management**: Pending to completed workflow
- **Payment Integration**: Fare calculation and tracking
- **Driver Assignment**: Easy driver assignment to bookings
- **Feedback System**: Ratings and reviews

## Security Features

- **Password Hashing**: Bcrypt for secure password storage
- **Session Management**: Secure PHP sessions
- **Input Validation**: SQL injection prevention with PDO
- **Role-based Access**: Admin, user, and driver permissions
- **File Upload Security**: Safe file handling and validation
- **XSS Prevention**: Output escaping and sanitization

## 3D Animations & Visual Effects

### CSS 3D Globe
- Rotating sphere with markers
- Animated location points
- Gradient backgrounds
- Smooth transitions

### JavaScript Animations
- Floating particles
- Number counters
- Progress bars
- Hover effects
- Modal animations

### Visual Effects
- Gradient backgrounds
- Glass morphism effects
- Smooth transitions
- Loading animations
- Interactive hover states

## API Endpoints

### Authentication
- `POST /login.php` - User login
- Session management for authentication

### Drivers
- `GET /drivers.php` - List drivers
- `POST /drivers.php` - Add/Update drivers
- Driver availability management

### Vehicles
- `GET /vehicles.php` - List vehicles
- `POST /vehicles.php` - Add/Update vehicles
- Status and driver assignment

### Routes
- `GET /routes.php` - List routes
- `POST /routes.php` - Add/Update routes
- Active/inactive status management

### Bookings
- `GET /bookings.php` - List bookings
- `POST /bookings.php` - Create/Update bookings
- Status updates and driver assignment

## Performance Optimizations

- **Database Indexes**: Optimized query performance
- **Efficient Queries**: Prepared statements with PDO
- **Lazy Loading**: On-demand data loading
- **Caching**: Session-based caching
- **Optimized CSS**: Efficient animations and transitions
- **Minified Assets**: Compressed CSS and JavaScript

## Troubleshooting

### Common Issues

#### Database Connection Error
```bash
# Check MySQL is running
# In XAMPP: Start MySQL service

# Check database exists
# In phpMyAdmin: Verify 'transport_tracking' database
```

#### File Upload Issues
```bash
# Check permissions
chmod 755 uploads/
chmod 755 uploads/drivers/
chmod 755 uploads/vehicles/
```

#### Session Issues
```bash
# Check PHP session path
# In php.ini: session.save_path = "/tmp"
# Restart Apache after changes
```

#### CSS/JS Not Loading
```bash
# Check file paths in HTML
# Verify Apache .htaccess configuration
# Check file permissions
```

### Development Tips

1. **Enable Error Reporting**: Add to PHP files for debugging
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

2. **Check Logs**: Apache error logs in XAMPP
3. **Use Browser DevTools**: Network tab for AJAX requests
4. **Database Testing**: Use phpMyAdmin for query testing

## Production Deployment

### Server Requirements
- PHP 7.4+ with extensions: PDO, MySQL, GD, Fileinfo
- MySQL 5.7+ or MariaDB 10.2+
- Web server: Apache 2.4+ or Nginx 1.18+
- SSL certificate (HTTPS)

### Security Checklist
- Change default passwords
- Update database credentials
- Enable HTTPS
- Set proper file permissions
- Disable error display in production
- Use environment variables for sensitive data

### Performance Optimization
- Enable PHP OPcache
- Use Redis for session storage
- Implement database query caching
- Optimize images and assets
- Use CDN for static files

## Customization

### Adding New Features
1. Create new PHP page in root directory
2. Add corresponding database table if needed
3. Update navigation menu in all pages
4. Add CSS styles as needed
5. Implement JavaScript functionality

### Modifying Design
- Edit `css/style.css` for visual changes
- Update color schemes and animations
- Modify layout grids and responsive design
- Add new animations and transitions

### Database Extensions
- Add new tables to `database.sql`
- Update `config/database.php` if needed
- Create corresponding PHP models
- Implement CRUD operations

## Support

For support and questions:
1. Check this documentation
2. Review error logs
3. Test with sample data
4. Verify database connections
5. Check file permissions

---

**Transport Tracking System** - Complete PHP/MySQL Solution with 3D Animations
