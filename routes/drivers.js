const express = require('express');
const multer = require('multer');
const path = require('path');
const Driver = require('../models/Driver');
const User = require('../models/User');
const { auth, adminAuth } = require('../middleware/auth');
const router = express.Router();

// Configure multer for file uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'uploads/drivers/');
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + '-' + Math.round(Math.random() * 1E9) + path.extname(file.originalname));
  }
});

const upload = multer({ 
  storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|pdf/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    
    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error('Only JPEG, JPG, PNG images and PDF files are allowed'));
    }
  }
});

// Get all drivers
router.get('/', adminAuth, async (req, res) => {
  try {
    const drivers = await Driver.find()
      .populate('user', 'username email profile')
      .populate('vehicleAssigned', 'registrationNumber make model');
    
    res.json({ success: true, drivers });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get driver by ID
router.get('/:id', auth, async (req, res) => {
  try {
    const driver = await Driver.findById(req.params.id)
      .populate('user', 'username email profile')
      .populate('vehicleAssigned', 'registrationNumber make model');
    
    if (!driver) {
      return res.status(404).json({ message: 'Driver not found' });
    }

    // Check if user is admin or the driver themselves
    if (req.user.role !== 'admin' && driver.user._id.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Access denied' });
    }

    res.json({ success: true, driver });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Create new driver
router.post('/', adminAuth, upload.fields([
  { name: 'driverPhoto', maxCount: 1 },
  { name: 'licenseImage', maxCount: 1 }
]), async (req, res) => {
  try {
    const {
      userId,
      licenseNumber,
      licenseExpiry,
      experience,
      emergencyContact
    } = req.body;

    // Check if user exists
    const user = await User.findById(userId);
    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    // Update user role to driver if not already
    if (user.role !== 'driver') {
      user.role = 'driver';
      await user.save();
    }

    // Check if driver already exists for this user
    const existingDriver = await Driver.findOne({ user: userId });
    if (existingDriver) {
      return res.status(400).json({ message: 'Driver profile already exists for this user' });
    }

    // Create driver
    const driver = new Driver({
      user: userId,
      licenseNumber,
      licenseExpiry,
      licenseImage: req.files.licenseImage ? req.files.licenseImage[0].filename : null,
      driverPhoto: req.files.driverPhoto ? req.files.driverPhoto[0].filename : null,
      experience: experience || 0,
      emergencyContact: emergencyContact ? JSON.parse(emergencyContact) : {}
    });

    await driver.save();

    const populatedDriver = await Driver.findById(driver._id)
      .populate('user', 'username email profile');

    res.status(201).json({ success: true, driver: populatedDriver });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update driver
router.put('/:id', auth, upload.fields([
  { name: 'driverPhoto', maxCount: 1 },
  { name: 'licenseImage', maxCount: 1 }
]), async (req, res) => {
  try {
    const driver = await Driver.findById(req.params.id);
    
    if (!driver) {
      return res.status(404).json({ message: 'Driver not found' });
    }

    // Check permissions
    if (req.user.role !== 'admin' && driver.user.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Access denied' });
    }

    const updateData = { ...req.body };
    
    // Handle file uploads
    if (req.files.driverPhoto) {
      updateData.driverPhoto = req.files.driverPhoto[0].filename;
    }
    if (req.files.licenseImage) {
      updateData.licenseImage = req.files.licenseImage[0].filename;
    }

    // Parse JSON fields
    if (updateData.emergencyContact) {
      updateData.emergencyContact = JSON.parse(updateData.emergencyContact);
    }

    const updatedDriver = await Driver.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, runValidators: true }
    ).populate('user', 'username email profile');

    res.json({ success: true, driver: updatedDriver });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update driver location
router.put('/:id/location', auth, async (req, res) => {
  try {
    const { latitude, longitude } = req.body;
    
    const driver = await Driver.findById(req.params.id);
    
    if (!driver) {
      return res.status(404).json({ message: 'Driver not found' });
    }

    // Check permissions
    if (req.user.role !== 'admin' && driver.user.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Access denied' });
    }

    driver.currentLocation = {
      latitude,
      longitude,
      lastUpdated: new Date()
    };

    await driver.save();

    // Emit location update to connected clients
    const io = req.app.get('io');
    io.emit('driver-location-update', {
      driverId: driver._id,
      location: driver.currentLocation
    });

    res.json({ success: true, location: driver.currentLocation });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update driver availability
router.put('/:id/availability', auth, async (req, res) => {
  try {
    const { isAvailable } = req.body;
    
    const driver = await Driver.findById(req.params.id);
    
    if (!driver) {
      return res.status(404).json({ message: 'Driver not found' });
    }

    // Check permissions
    if (req.user.role !== 'admin' && driver.user.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Access denied' });
    }

    driver.isAvailable = isAvailable;
    await driver.save();

    res.json({ success: true, isAvailable: driver.isAvailable });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get available drivers
router.get('/available/all', auth, async (req, res) => {
  try {
    const drivers = await Driver.find({ isAvailable: true })
      .populate('user', 'username email profile')
      .populate('vehicleAssigned', 'registrationNumber make model');
    
    res.json({ success: true, drivers });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

module.exports = router;
