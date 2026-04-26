-- Transport Tracking System Database Schema
-- Create database and tables

CREATE DATABASE IF NOT EXISTS transport_system;
USE transport_system;

-- Users table
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

-- Drivers table
CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    license_expiry DATE NOT NULL,
    license_image VARCHAR(255),
    driver_photo VARCHAR(255),
    experience INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 5.00,
    total_trips INT DEFAULT 0,
    current_latitude DECIMAL(10,8),
    current_longitude DECIMAL(11,8),
    last_location_update DATETIME,
    is_available BOOLEAN DEFAULT TRUE,
    vehicle_assigned INT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_relation VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_assigned) REFERENCES vehicles(id) ON DELETE SET NULL
);

-- Vehicles table
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
    current_latitude DECIMAL(10,8),
    current_longitude DECIMAL(11,8),
    last_location_update DATETIME,
    driver_id INT,
    insurance_policy_number VARCHAR(50),
    insurance_expiry DATE,
    insurance_provider VARCHAR(100),
    registration_expiry DATE,
    registration_authority VARCHAR(100),
    last_service_date DATE,
    next_service_date DATE,
    mileage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL
);

-- Routes table
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    start_location_name VARCHAR(100) NOT NULL,
    start_latitude DECIMAL(10,8) NOT NULL,
    start_longitude DECIMAL(11,8) NOT NULL,
    start_address TEXT,
    end_location_name VARCHAR(100) NOT NULL,
    end_latitude DECIMAL(10,8) NOT NULL,
    end_longitude DECIMAL(11,8) NOT NULL,
    end_address TEXT,
    distance DECIMAL(8,2) NOT NULL,
    estimated_duration INT NOT NULL,
    fare DECIMAL(8,2) NOT NULL,
    route_type ENUM('regular', 'express', 'special') DEFAULT 'regular',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Waypoints table (for route stops)
CREATE TABLE waypoints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    stop_order INT NOT NULL,
    estimated_time INT,
    is_mandatory BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    passenger_id INT NOT NULL,
    route_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    driver_id INT,
    pickup_location_name VARCHAR(100),
    pickup_latitude DECIMAL(10,8),
    pickup_longitude DECIMAL(11,8),
    pickup_address TEXT,
    pickup_landmark VARCHAR(100),
    dropoff_location_name VARCHAR(100),
    dropoff_latitude DECIMAL(10,8),
    dropoff_longitude DECIMAL(11,8),
    dropoff_address TEXT,
    dropoff_landmark VARCHAR(100),
    scheduled_date DATE NOT NULL,
    scheduled_time TIME NOT NULL,
    actual_pickup_time DATETIME,
    actual_dropoff_time DATETIME,
    status ENUM('pending', 'confirmed', 'driver_assigned', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    base_fare DECIMAL(8,2),
    distance_fare DECIMAL(8,2),
    time_fare DECIMAL(8,2),
    total_fare DECIMAL(8,2),
    payment_method ENUM('cash', 'card', 'wallet', 'subscription') DEFAULT 'cash',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    paid_at DATETIME,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    infants INT DEFAULT 0,
    luggage_pieces INT DEFAULT 0,
    luggage_weight DECIMAL(5,2),
    special_requests TEXT,
    current_latitude DECIMAL(10,8),
    current_longitude DECIMAL(11,8),
    last_tracking_update DATETIME,
    estimated_arrival DATETIME,
    rating INT,
    comment TEXT,
    driver_rating INT,
    vehicle_rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (passenger_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE RESTRICT,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL
);

-- Route schedules table
CREATE TABLE route_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    departure_time TIME NOT NULL,
    frequency_minutes INT DEFAULT 0,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
);

-- Vehicle images table
CREATE TABLE vehicle_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Driver documents table
CREATE TABLE driver_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT NOT NULL,
    document_name VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE
);

-- Insert sample data

-- Sample admin user
INSERT INTO users (username, email, password, role, first_name, last_name) VALUES 
('admin', 'admin@transport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin', 'User');

-- Sample regular user
INSERT INTO users (username, email, password, role, first_name, last_name, phone) VALUES 
('johnuser', 'john@transport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'John', 'Doe', '+1234567890');

-- Sample driver user
INSERT INTO users (username, email, password, role, first_name, last_name, phone) VALUES 
('mikedriver', 'mike@transport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'driver', 'Mike', 'Wilson', '+0987654321');

-- Sample driver
INSERT INTO drivers (user_id, license_number, license_expiry, experience, rating, emergency_contact_name, emergency_contact_phone, emergency_contact_relation) VALUES 
(3, 'DL123456', '2025-12-31', 5, 4.8, 'Jane Wilson', '+1122334455', 'Spouse');

-- Sample vehicles
INSERT INTO vehicles (registration_number, make, model, year, type, capacity, fuel_type, status) VALUES 
('ABC-123', 'Toyota', 'Camry', 2022, 'car', 4, 'hybrid', 'active'),
('XYZ-789', 'Ford', 'Transit', 2021, 'van', 8, 'diesel', 'active'),
('DEF-456', 'Mercedes', 'Sprinter', 2023, 'bus', 20, 'diesel', 'active');

-- Sample routes
INSERT INTO routes (name, code, start_location_name, start_latitude, start_longitude, end_location_name, end_latitude, end_longitude, distance, estimated_duration, fare, route_type) VALUES 
('Downtown to Airport', 'R001', 'Downtown Station', 40.7128, -74.0060, 'International Airport', 40.6413, -73.7781, 25.5, 45, 35.00, 'regular'),
('City Center to Mall', 'R002', 'City Center', 40.7589, -73.9851, 'Shopping Mall', 40.7831, -73.9712, 5.2, 15, 12.00, 'express'),
('University to Station', 'R003', 'University Campus', 40.8075, -73.9626, 'Central Station', 40.7527, -73.9772, 8.8, 25, 18.00, 'regular');

-- Sample bookings
INSERT INTO bookings (booking_reference, passenger_id, route_id, vehicle_id, scheduled_date, scheduled_time, pickup_location_name, dropoff_location_name, total_fare, status) VALUES 
('BK123456', 2, 1, 1, '2024-12-25', '09:00:00', 'Downtown Station', 'International Airport', 35.00, 'confirmed'),
('BK123457', 2, 2, 2, '2024-12-25', '14:30:00', 'City Center', 'Shopping Mall', 12.00, 'pending');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_drivers_user_id ON drivers(user_id);
CREATE INDEX idx_drivers_available ON drivers(is_available);
CREATE INDEX idx_vehicles_status ON vehicles(status);
CREATE INDEX idx_vehicles_driver_id ON vehicles(driver_id);
CREATE INDEX idx_routes_active ON routes(is_active);
CREATE INDEX idx_bookings_passenger ON bookings(passenger_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_date ON bookings(scheduled_date);
CREATE INDEX idx_bookings_reference ON bookings(booking_reference);
