@echo off
echo Transport Tracking System - Setup Script
echo ========================================
echo.

echo Checking Node.js installation...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Node.js is not installed or not in PATH
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)

echo Node.js installed successfully
echo.

echo Checking MongoDB installation...
mongod --version >nul 2>&1
if %errorlevel% neq 0 (
    echo WARNING: MongoDB is not installed or not in PATH
    echo Please install MongoDB or ensure it's running (XAMPP users: start MongoDB from XAMPP control panel)
    echo.
)

echo Installing backend dependencies...
npm install
if %errorlevel% neq 0 (
    echo ERROR: Failed to install backend dependencies
    pause
    exit /b 1
)

echo Backend dependencies installed successfully
echo.

echo Installing frontend dependencies...
cd client
npm install
if %errorlevel% neq 0 (
    echo ERROR: Failed to install frontend dependencies
    pause
    exit /b 1
)

echo Frontend dependencies installed successfully
cd ..

echo.
echo Creating upload directories...
if not exist "uploads" mkdir uploads
if not exist "uploads\drivers" mkdir "uploads\drivers"
if not exist "uploads\vehicles" mkdir "uploads\vehicles"

echo Upload directories created
echo.

echo Setup completed successfully!
echo.
echo To start the application:
echo 1. Make sure MongoDB is running
echo 2. Run: npm run dev
echo.
echo Default login credentials:
echo Admin: admin@transport.com / admin123
echo User: user@transport.com / user123
echo.
echo Access the application at: http://localhost:3000
echo.
pause
