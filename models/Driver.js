const mongoose = require('mongoose');

const driverSchema = new mongoose.Schema({
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  licenseNumber: {
    type: String,
    required: true,
    unique: true
  },
  licenseExpiry: {
    type: Date,
    required: true
  },
  licenseImage: {
    type: String,
    required: true
  },
  driverPhoto: {
    type: String,
    required: true
  },
  experience: {
    type: Number,
    default: 0
  },
  rating: {
    type: Number,
    default: 5,
    min: 1,
    max: 5
  },
  totalTrips: {
    type: Number,
    default: 0
  },
  currentLocation: {
    latitude: Number,
    longitude: Number,
    lastUpdated: Date
  },
  isAvailable: {
    type: Boolean,
    default: true
  },
  vehicleAssigned: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Vehicle'
  },
  documents: [{
    name: String,
    file: String,
    expiryDate: Date
  }],
  emergencyContact: {
    name: String,
    phone: String,
    relation: String
  },
  createdAt: {
    type: Date,
    default: Date.now
  }
});

module.exports = mongoose.model('Driver', driverSchema);
