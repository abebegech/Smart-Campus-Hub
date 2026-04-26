const express = require('express');
const multer = require('multer');
const path = require('path');
const Vehicle = require('../models/Vehicle');
const { auth, adminAuth } = require('../middleware/auth');
const router = express.Router();

// Configure multer for file uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'uploads/vehicles/');
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + '-' + Math.round(Math.random() * 1E9) + path.extname(file.originalname));
  }
});

const upload = multer({ 
  storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    
    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error('Only JPEG, JPG, PNG images are allowed'));
    }
  }
});

// Get all vehicles
router.get('/', auth, async (req, res) => {
  try {
    const { status, type } = req.query;
    let query = {};
    
    if (status) query.status = status;
    if (type) query.type = type;

    const vehicles = await Vehicle.find(query)
      .populate('driver', 'user')
      .populate({
        path: 'driver',
        populate: {
          path: 'user',
          select: 'username email profile'
        }
      });
    
    res.json({ success: true, vehicles });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get vehicle by ID
router.get('/:id', auth, async (req, res) => {
  try {
    const vehicle = await Vehicle.findById(req.params.id)
      .populate('driver', 'user')
      .populate({
        path: 'driver',
        populate: {
          path: 'user',
          select: 'username email profile'
        }
      });
    
    if (!vehicle) {
      return res.status(404).json({ message: 'Vehicle not found' });
    }

    res.json({ success: true, vehicle });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Create new vehicle
router.post('/', adminAuth, upload.array('images', 5), async (req, res) => {
  try {
    const {
      registrationNumber,
      make,
      model,
      year,
      type,
      capacity,
      fuelType,
      insurance,
      registration,
      maintenance,
      features,
      gpsDevice
    } = req.body;

    // Check if vehicle already exists
    const existingVehicle = await Vehicle.findOne({ registrationNumber });
    if (existingVehicle) {
      return res.status(400).json({ message: 'Vehicle with this registration number already exists' });
    }

    // Create vehicle
    const vehicle = new Vehicle({
      registrationNumber,
      make,
      model,
      year,
      type,
      capacity,
      fuelType,
      insurance: insurance ? JSON.parse(insurance) : {},
      registration: registration ? JSON.parse(registration) : {},
      maintenance: maintenance ? JSON.parse(maintenance) : {},
      features: features ? JSON.parse(features) : [],
      gpsDevice: gpsDevice ? JSON.parse(gpsDevice) : {},
      images: req.files ? req.files.map(file => file.filename) : []
    });

    await vehicle.save();

    const populatedVehicle = await Vehicle.findById(vehicle._id)
      .populate('driver', 'user')
      .populate({
        path: 'driver',
        populate: {
          path: 'user',
          select: 'username email profile'
        }
      });

    res.status(201).json({ success: true, vehicle: populatedVehicle });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update vehicle
router.put('/:id', adminAuth, upload.array('images', 5), async (req, res) => {
  try {
    const vehicle = await Vehicle.findById(req.params.id);
    
    if (!vehicle) {
      return res.status(404).json({ message: 'Vehicle not found' });
    }

    const updateData = { ...req.body };
    
    // Handle file uploads
    if (req.files && req.files.length > 0) {
      const newImages = req.files.map(file => file.filename);
      updateData.images = [...(vehicle.images || []), ...newImages];
    }

    // Parse JSON fields
    if (updateData.insurance) updateData.insurance = JSON.parse(updateData.insurance);
    if (updateData.registration) updateData.registration = JSON.parse(updateData.registration);
    if (updateData.maintenance) updateData.maintenance = JSON.parse(updateData.maintenance);
    if (updateData.features) updateData.features = JSON.parse(updateData.features);
    if (updateData.gpsDevice) updateData.gpsDevice = JSON.parse(updateData.gpsDevice);

    const updatedVehicle = await Vehicle.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, runValidators: true }
    ).populate('driver', 'user')
    .populate({
      path: 'driver',
      populate: {
        path: 'user',
        select: 'username email profile'
      }
    });

    res.json({ success: true, vehicle: updatedVehicle });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update vehicle status
router.put('/:id/status', adminAuth, async (req, res) => {
  try {
    const { status } = req.body;
    
    const vehicle = await Vehicle.findByIdAndUpdate(
      req.params.id,
      { status },
      { new: true, runValidators: true }
    ).populate('driver', 'user');

    if (!vehicle) {
      return res.status(404).json({ message: 'Vehicle not found' });
    }

    res.json({ success: true, vehicle });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update vehicle location
router.put('/:id/location', auth, async (req, res) => {
  try {
    const { latitude, longitude } = req.body;
    
    const vehicle = await Vehicle.findById(req.params.id);
    
    if (!vehicle) {
      return res.status(404).json({ message: 'Vehicle not found' });
    }

    vehicle.currentLocation = {
      latitude,
      longitude,
      lastUpdated: new Date()
    };

    await vehicle.save();

    // Emit location update to connected clients
    const io = req.app.get('io');
    io.emit('vehicle-location-update', {
      vehicleId: vehicle._id,
      location: vehicle.currentLocation
    });

    res.json({ success: true, location: vehicle.currentLocation });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Assign driver to vehicle
router.put('/:id/assign-driver', adminAuth, async (req, res) => {
  try {
    const { driverId } = req.body;
    
    const vehicle = await Vehicle.findByIdAndUpdate(
      req.params.id,
      { driver: driverId },
      { new: true, runValidators: true }
    ).populate('driver', 'user')
    .populate({
      path: 'driver',
      populate: {
        path: 'user',
        select: 'username email profile'
      }
    });

    if (!vehicle) {
      return res.status(404).json({ message: 'Vehicle not found' });
    }

    // Update driver's vehicle assignment
    const Driver = require('../models/Driver');
    await Driver.findByIdAndUpdate(
      driverId,
      { vehicleAssigned: vehicle._id },
      { new: true }
    );

    res.json({ success: true, vehicle });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Remove driver from vehicle
router.put('/:id/remove-driver', adminAuth, async (req, res) => {
  try {
    const vehicle = await Vehicle.findByIdAndUpdate(
      req.params.id,
      { driver: null },
      { new: true, runValidators: true }
    ).populate('driver', 'user');

    if (!vehicle) {
      return res.status(404).json({ message: 'Vehicle not found' });
    }

    // Update driver's vehicle assignment
    if (vehicle.driver) {
      const Driver = require('../models/Driver');
      await Driver.findByIdAndUpdate(
        vehicle.driver._id,
        { vehicleAssigned: null },
        { new: true }
      );
    }

    res.json({ success: true, vehicle });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get available vehicles
router.get('/available/all', auth, async (req, res) => {
  try {
    const vehicles = await Vehicle.find({ 
      status: 'active',
      driver: null 
    }).populate('driver', 'user');
    
    res.json({ success: true, vehicles });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

module.exports = router;
