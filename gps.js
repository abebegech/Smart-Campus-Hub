const express = require('express');
const { auth } = require('../middleware/auth');
const router = express.Router();

// Get GPS coordinates for an address (geocoding)
router.get('/geocode', auth, async (req, res) => {
  try {
    const { address } = req.query;
    
    if (!address) {
      return res.status(400).json({ message: 'Address is required' });
    }

    // In production, you'd use a real geocoding service like Google Maps API
    // For demo purposes, we'll return mock coordinates
    const mockCoordinates = {
      latitude: 40.7128 + (Math.random() - 0.5) * 0.1,
      longitude: -74.0060 + (Math.random() - 0.5) * 0.1,
      address: address
    };

    res.json({ success: true, coordinates: mockCoordinates });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get address from GPS coordinates (reverse geocoding)
router.get('/reverse-geocode', auth, async (req, res) => {
  try {
    const { latitude, longitude } = req.query;
    
    if (!latitude || !longitude) {
      return res.status(400).json({ message: 'Latitude and longitude are required' });
    }

    // In production, you'd use a real reverse geocoding service
    // For demo purposes, we'll return a mock address
    const mockAddress = {
      address: `${Math.floor(Math.random() * 9999)} Main St, New York, NY 10001`,
      latitude: parseFloat(latitude),
      longitude: parseFloat(longitude)
    };

    res.json({ success: true, address: mockAddress });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Calculate route between two points
router.get('/route', auth, async (req, res) => {
  try {
    const { startLat, startLng, endLat, endLng } = req.query;
    
    if (!startLat || !startLng || !endLat || !endLng) {
      return res.status(400).json({ message: 'Start and end coordinates are required' });
    }

    // In production, you'd use a real routing service like Google Maps Directions API
    // For demo purposes, we'll return a mock route
    const distance = calculateDistance(
      parseFloat(startLat), parseFloat(startLng),
      parseFloat(endLat), parseFloat(endLng)
    );

    const mockRoute = {
      distance: distance,
      duration: Math.round(distance * 2 * 60), // 2 minutes per km
      coordinates: generateRouteCoordinates(
        parseFloat(startLat), parseFloat(startLng),
        parseFloat(endLat), parseFloat(endLng)
      ),
      steps: [
        {
          instruction: 'Head north on Main St',
          distance: distance * 0.3,
          duration: Math.round(distance * 0.3 * 2 * 60)
        },
        {
          instruction: 'Turn right onto Highway 1',
          distance: distance * 0.4,
          duration: Math.round(distance * 0.4 * 2 * 60)
        },
        {
          instruction: 'Turn left to reach destination',
          distance: distance * 0.3,
          duration: Math.round(distance * 0.3 * 2 * 60)
        }
      ]
    };

    res.json({ success: true, route: mockRoute });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get real-time traffic information
router.get('/traffic', auth, async (req, res) => {
  try {
    const { latitude, longitude, radius = 5 } = req.query;
    
    if (!latitude || !longitude) {
      return res.status(400).json({ message: 'Latitude and longitude are required' });
    }

    // In production, you'd use a real traffic API
    // For demo purposes, we'll return mock traffic data
    const mockTraffic = {
      status: 'moderate',
      incidents: [
        {
          type: 'construction',
          description: 'Road work on Main St',
          latitude: parseFloat(latitude) + 0.01,
          longitude: parseFloat(longitude) + 0.01,
          severity: 'minor'
        }
      ],
      flow: {
        current: 35, // km/h
        free: 60,   // km/h
        ratio: 0.58
      }
    };

    res.json({ success: true, traffic: mockTraffic });
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

// Helper function to generate route coordinates
function generateRouteCoordinates(startLat, startLng, endLat, endLng) {
  const coordinates = [];
  const steps = 10; // Number of intermediate points
  
  for (let i = 0; i <= steps; i++) {
    const t = i / steps;
    const lat = startLat + (endLat - startLat) * t;
    const lng = startLng + (endLng - startLng) * t;
    coordinates.push({ latitude: lat, longitude: lng });
  }
  
  return coordinates;
}

module.exports = router;
