const express = require('express');
const Route = require('../models/Route');
const { auth, adminAuth } = require('../middleware/auth');
const router = express.Router();

// Get all routes
router.get('/', auth, async (req, res) => {
  try {
    const { isActive, routeType } = req.query;
    let query = {};
    
    if (isActive !== undefined) query.isActive = isActive === 'true';
    if (routeType) query.routeType = routeType;

    const routes = await Route.find(query)
      .populate('vehicleTypes', 'registrationNumber make model type capacity');
    
    res.json({ success: true, routes });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get route by ID
router.get('/:id', auth, async (req, res) => {
  try {
    const route = await Route.findById(req.params.id)
      .populate('vehicleTypes', 'registrationNumber make model type capacity');
    
    if (!route) {
      return res.status(404).json({ message: 'Route not found' });
    }

    res.json({ success: true, route });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Create new route
router.post('/', adminAuth, async (req, res) => {
  try {
    const {
      name,
      code,
      startLocation,
      endLocation,
      waypoints,
      distance,
      estimatedDuration,
      fare,
      routeType,
      schedule,
      vehicleTypes
    } = req.body;

    // Check if route code already exists
    const existingRoute = await Route.findOne({ code });
    if (existingRoute) {
      return res.status(400).json({ message: 'Route with this code already exists' });
    }

    // Create route
    const route = new Route({
      name,
      code,
      startLocation,
      endLocation,
      waypoints,
      distance,
      estimatedDuration,
      fare,
      routeType,
      schedule,
      vehicleTypes
    });

    await route.save();

    const populatedRoute = await Route.findById(route._id)
      .populate('vehicleTypes', 'registrationNumber make model type capacity');

    res.status(201).json({ success: true, route: populatedRoute });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update route
router.put('/:id', adminAuth, async (req, res) => {
  try {
    const route = await Route.findById(req.params.id);
    
    if (!route) {
      return res.status(404).json({ message: 'Route not found' });
    }

    const updateData = { ...req.body };

    const updatedRoute = await Route.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, runValidators: true }
    ).populate('vehicleTypes', 'registrationNumber make model type capacity');

    res.json({ success: true, route: updatedRoute });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update route status
router.put('/:id/status', adminAuth, async (req, res) => {
  try {
    const { isActive } = req.body;
    
    const route = await Route.findByIdAndUpdate(
      req.params.id,
      { isActive },
      { new: true, runValidators: true }
    ).populate('vehicleTypes', 'registrationNumber make model type capacity');

    if (!route) {
      return res.status(404).json({ message: 'Route not found' });
    }

    res.json({ success: true, route });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get routes by location (nearby routes)
router.get('/search/nearby', auth, async (req, res) => {
  try {
    const { latitude, longitude, radius = 10 } = req.query;
    
    if (!latitude || !longitude) {
      return res.status(400).json({ message: 'Latitude and longitude are required' });
    }

    // Find routes with start or end locations within radius
    // This is a simplified approach - in production, you'd use geospatial queries
    const routes = await Route.find({ isActive: true })
      .populate('vehicleTypes', 'registrationNumber make model type capacity');

    // Filter routes based on distance (simplified calculation)
    const nearbyRoutes = routes.filter(route => {
      const startDistance = calculateDistance(
        parseFloat(latitude), 
        parseFloat(longitude),
        route.startLocation.latitude,
        route.startLocation.longitude
      );
      const endDistance = calculateDistance(
        parseFloat(latitude), 
        parseFloat(longitude),
        route.endLocation.latitude,
        route.endLocation.longitude
      );
      return startDistance <= radius || endDistance <= radius;
    });

    res.json({ success: true, routes: nearbyRoutes });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Helper function to calculate distance between two points
function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // Earth's radius in kilometers
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
    Math.sin(dLon/2) * Math.sin(dLon/2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  return R * c;
}

module.exports = router;
