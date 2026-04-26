const mongoose = require('mongoose');

const vehicleSchema = new mongoose.Schema({
  registrationNumber: {
    type: String,
    required: true,
    unique: true
  },
  make: {
    type: String,
    required: true
  },
  model: {
    type: String,
    required: true
  },
  year: {
    type: Number,
    required: true
  },
  type: {
    type: String,
    enum: ['bus', 'truck', 'van', 'car', 'motorcycle'],
    required: true
  },
  capacity: {
    type: Number,
    required: true
  },
  fuelType: {
    type: String,
    enum: ['petrol', 'diesel', 'electric', 'hybrid'],
    required: true
  },
  status: {
    type: String,
    enum: ['active', 'maintenance', 'inactive', 'in_transit'],
    default: 'active'
  },
  currentLocation: {
    latitude: Number,
    longitude: Number,
    lastUpdated: Date
  },
  driver: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Driver'
  },
  insurance: {
    policyNumber: String,
    expiryDate: Date,
    provider: String
  },
  registration: {
    expiryDate: Date,
    authority: String
  },
  maintenance: {
    lastService: Date,
    nextService: Date,
    mileage: Number
  },
  features: [{
    type: String
  }],
  images: [{
    type: String
  }],
  gpsDevice: {
    deviceId: String,
    isActive: Boolean,
    lastPing: Date
  },
  createdAt: {
    type: Date,
    default: Date.now
  }
});

module.exports = mongoose.model('Vehicle', vehicleSchema);
