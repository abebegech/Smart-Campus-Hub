const mongoose = require('mongoose');

const bookingSchema = new mongoose.Schema({
  bookingReference: {
    type: String,
    required: true,
    unique: true
  },
  passenger: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  route: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Route',
    required: true
  },
  vehicle: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Vehicle',
    required: true
  },
  driver: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Driver'
  },
  pickupLocation: {
    name: String,
    latitude: Number,
    longitude: Number,
    address: String,
    landmark: String
  },
  dropoffLocation: {
    name: String,
    latitude: Number,
    longitude: Number,
    address: String,
    landmark: String
  },
  scheduledDate: {
    type: Date,
    required: true
  },
  scheduledTime: {
    type: String,
    required: true
  },
  actualPickupTime: Date,
  actualDropoffTime: Date,
  status: {
    type: String,
    enum: ['pending', 'confirmed', 'driver_assigned', 'in_progress', 'completed', 'cancelled', 'no_show'],
    default: 'pending'
  },
  fare: {
    baseFare: Number,
    distanceFare: Number,
    timeFare: Number,
    totalFare: Number,
    currency: {
      type: String,
      default: 'USD'
    }
  },
  payment: {
    method: {
      type: String,
      enum: ['cash', 'card', 'wallet', 'subscription'],
      default: 'cash'
    },
    status: {
      type: String,
      enum: ['pending', 'paid', 'refunded'],
      default: 'pending'
    },
    transactionId: String,
    paidAt: Date
  },
  passengers: {
    adults: {
      type: Number,
      default: 1
    },
    children: {
      type: Number,
      default: 0
    },
    infants: {
      type: Number,
      default: 0
    }
  },
  luggage: {
    pieces: Number,
    weight: Number
  },
  specialRequests: [{
    type: String
  }],
  tracking: {
    currentLatitude: Number,
    currentLongitude: Number,
    lastUpdated: Date,
    estimatedArrival: Date
  },
  feedback: {
    rating: {
      type: Number,
      min: 1,
      max: 5
    },
    comment: String,
    driverRating: {
      type: Number,
      min: 1,
      max: 5
    },
    vehicleRating: {
      type: Number,
      min: 1,
      max: 5
    }
  },
  createdAt: {
    type: Date,
    default: Date.now
  }
});

// Generate booking reference before saving
bookingSchema.pre('save', function(next) {
  if (this.isNew && !this.bookingReference) {
    this.bookingReference = 'BK' + Date.now() + Math.random().toString(36).substr(2, 5).toUpperCase();
  }
  next();
});

module.exports = mongoose.model('Booking', bookingSchema);
