const express = require('express');
const Booking = require('../models/Booking');
const Route = require('../models/Route');
const Vehicle = require('../models/Vehicle');
const Driver = require('../models/Driver');
const { auth, adminAuth } = require('../middleware/auth');
const router = express.Router();

// Get all bookings
router.get('/', auth, async (req, res) => {
  try {
    const { status, passenger, date } = req.query;
    let query = {};
    
    if (status) query.status = status;
    if (passenger) query.passenger = passenger;
    if (date) {
      const startOfDay = new Date(date);
      const endOfDay = new Date(date);
      endOfDay.setHours(23, 59, 59, 999);
      query.scheduledDate = { $gte: startOfDay, $lte: endOfDay };
    }

    // If not admin, only show user's bookings
    if (req.user.role !== 'admin') {
      query.passenger = req.user.id;
    }

    const bookings = await Booking.find(query)
      .populate('passenger', 'username email profile')
      .populate('route', 'name code startLocation endLocation')
      .populate('vehicle', 'registrationNumber make model type')
      .populate('driver', 'user')
      .populate({
        path: 'driver',
        populate: {
          path: 'user',
          select: 'username email profile'
        }
      })
      .sort({ createdAt: -1 });
    
    res.json({ success: true, bookings });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Get booking by ID
router.get('/:id', auth, async (req, res) => {
  try {
    const booking = await Booking.findById(req.params.id)
      .populate('passenger', 'username email profile')
      .populate('route', 'name code startLocation endLocation')
      .populate('vehicle', 'registrationNumber make model type')
      .populate('driver', 'user')
      .populate({
        path: 'driver',
        populate: {
          path: 'user',
          select: 'username email profile'
        }
      });
    
    if (!booking) {
      return res.status(404).json({ message: 'Booking not found' });
    }

    // Check if user is admin, passenger, or assigned driver
    if (req.user.role !== 'admin' && 
        booking.passenger._id.toString() !== req.user.id &&
        (!booking.driver || booking.driver.user._id.toString() !== req.user.id)) {
      return res.status(403).json({ message: 'Access denied' });
    }

    res.json({ success: true, booking });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Create new booking
router.post('/', auth, async (req, res) => {
  try {
    const {
      routeId,
      vehicleId,
      pickupLocation,
      dropoffLocation,
      scheduledDate,
      scheduledTime,
      passengers,
      luggage,
      specialRequests
    } = req.body;

    // Validate route
    const route = await Route.findById(routeId);
    if (!route || !route.isActive) {
      return res.status(400).json({ message: 'Invalid or inactive route' });
    }

    // Validate vehicle
    const vehicle = await Vehicle.findById(vehicleId);
    if (!vehicle || vehicle.status !== 'active') {
      return res.status(400).json({ message: 'Invalid or inactive vehicle' });
    }

    // Calculate fare (simplified - in production, this would be more complex)
    const baseFare = route.fare;
    const distanceFare = route.distance * 0.5; // $0.5 per km
    const totalFare = baseFare + distanceFare;

    // Create booking
    const booking = new Booking({
      passenger: req.user.id,
      route: routeId,
      vehicle: vehicleId,
      pickupLocation,
      dropoffLocation,
      scheduledDate: new Date(scheduledDate),
      scheduledTime,
      passengers,
      luggage,
      specialRequests,
      fare: {
        baseFare,
        distanceFare,
        totalFare
      }
    });

    await booking.save();

    const populatedBooking = await Booking.findById(booking._id)
      .populate('passenger', 'username email profile')
      .populate('route', 'name code startLocation endLocation')
      .populate('vehicle', 'registrationNumber make model type')
      .populate('driver', 'user')
      .populate({
        path: 'driver',
        populate: {
          path: 'user',
          select: 'username email profile'
        }
      });

    res.status(201).json({ success: true, booking: populatedBooking });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update booking
router.put('/:id', auth, async (req, res) => {
  try {
    const booking = await Booking.findById(req.params.id);
    
    if (!booking) {
      return res.status(404).json({ message: 'Booking not found' });
    }

    // Check permissions
    if (req.user.role !== 'admin' && booking.passenger.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Access denied' });
    }

    // Don't allow updates if booking is in progress or completed
    if (['in_progress', 'completed'].includes(booking.status)) {
      return res.status(400).json({ message: 'Cannot update booking in current status' });
    }

    const updateData = { ...req.body };

    const updatedBooking = await Booking.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, runValidators: true }
    ).populate('passenger', 'username email profile')
    .populate('route', 'name code startLocation endLocation')
    .populate('vehicle', 'registrationNumber make model type')
    .populate('driver', 'user')
    .populate({
      path: 'driver',
      populate: {
        path: 'user',
        select: 'username email profile'
      }
    });

    res.json({ success: true, booking: updatedBooking });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update booking status
router.put('/:id/status', auth, async (req, res) => {
  try {
    const { status } = req.body;
    
    const booking = await Booking.findById(req.params.id);
    
    if (!booking) {
      return res.status(404).json({ message: 'Booking not found' });
    }

    // Check permissions based on status update
    if (req.user.role === 'driver') {
      // Drivers can only update to in_progress or completed
      if (!['in_progress', 'completed'].includes(status)) {
        return res.status(403).json({ message: 'Drivers can only update to in_progress or completed status' });
      }
      // Check if driver is assigned to this booking
      if (!booking.driver || booking.driver.toString() !== req.user.id) {
        return res.status(403).json({ message: 'You are not assigned to this booking' });
      }
    } else if (req.user.role === 'user') {
      // Users can only cancel their own bookings
      if (status !== 'cancelled' || booking.passenger.toString() !== req.user.id) {
        return res.status(403).json({ message: 'Users can only cancel their own bookings' });
      }
    }

    // Update timestamps based on status
    const updateData = { status };
    if (status === 'in_progress') {
      updateData.actualPickupTime = new Date();
    } else if (status === 'completed') {
      updateData.actualDropoffTime = new Date();
    }

    const updatedBooking = await Booking.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, runValidators: true }
    ).populate('passenger', 'username email profile')
    .populate('route', 'name code startLocation endLocation')
    .populate('vehicle', 'registrationNumber make model type')
    .populate('driver', 'user')
    .populate({
      path: 'driver',
      populate: {
        path: 'user',
        select: 'username email profile'
      }
    });

    // Emit status update to connected clients
    const io = req.app.get('io');
    io.emit('booking-status-update', {
      bookingId: updatedBooking._id,
      status: updatedBooking.status
    });

    res.json({ success: true, booking: updatedBooking });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Assign driver to booking
router.put('/:id/assign-driver', adminAuth, async (req, res) => {
  try {
    const { driverId } = req.body;
    
    const booking = await Booking.findByIdAndUpdate(
      req.params.id,
      { driver: driverId, status: 'driver_assigned' },
      { new: true, runValidators: true }
    ).populate('passenger', 'username email profile')
    .populate('route', 'name code startLocation endLocation')
    .populate('vehicle', 'registrationNumber make model type')
    .populate('driver', 'user')
    .populate({
      path: 'driver',
      populate: {
        path: 'user',
        select: 'username email profile'
      }
    });

    if (!booking) {
      return res.status(404).json({ message: 'Booking not found' });
    }

    res.json({ success: true, booking });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Update booking tracking
router.put('/:id/tracking', auth, async (req, res) => {
  try {
    const { latitude, longitude, estimatedArrival } = req.body;
    
    const booking = await Booking.findById(req.params.id);
    
    if (!booking) {
      return res.status(404).json({ message: 'Booking not found' });
    }

    // Check permissions (driver or admin)
    if (req.user.role !== 'admin' && 
        (!booking.driver || booking.driver.toString() !== req.user.id)) {
      return res.status(403).json({ message: 'Access denied' });
    }

    booking.tracking = {
      currentLatitude: latitude,
      currentLongitude: longitude,
      lastUpdated: new Date(),
      estimatedArrival: estimatedArrival ? new Date(estimatedArrival) : null
    };

    await booking.save();

    // Emit tracking update to connected clients
    const io = req.app.get('io');
    io.to(`tracking-${booking._id}`).emit('tracking-update', {
      bookingId: booking._id,
      tracking: booking.tracking
    });

    res.json({ success: true, tracking: booking.tracking });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Add feedback to booking
router.put('/:id/feedback', auth, async (req, res) => {
  try {
    const { rating, comment, driverRating, vehicleRating } = req.body;
    
    const booking = await Booking.findById(req.params.id);
    
    if (!booking) {
      return res.status(404).json({ message: 'Booking not found' });
    }

    // Only passengers can add feedback to their own completed bookings
    if (booking.passenger.toString() !== req.user.id || booking.status !== 'completed') {
      return res.status(403).json({ message: 'Access denied' });
    }

    booking.feedback = {
      rating,
      comment,
      driverRating,
      vehicleRating
    };

    await booking.save();

    // Update driver's overall rating
    if (booking.driver && driverRating) {
      const driver = await Driver.findById(booking.driver);
      if (driver) {
        // Simple average - in production, you'd calculate based on all ratings
        driver.rating = (driver.rating + driverRating) / 2;
        await driver.save();
      }
    }

    res.json({ success: true, feedback: booking.feedback });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

module.exports = router;
