const mongoose = require('mongoose');

const routeSchema = new mongoose.Schema({
  name: {
    type: String,
    required: true
  },
  code: {
    type: String,
    required: true,
    unique: true
  },
  startLocation: {
    name: String,
    latitude: Number,
    longitude: Number,
    address: String
  },
  endLocation: {
    name: String,
    latitude: Number,
    longitude: Number,
    address: String
  },
  waypoints: [{
    name: String,
    latitude: Number,
    longitude: Number,
    stopOrder: Number,
    estimatedTime: Number,
    isMandatory: Boolean
  }],
  distance: {
    type: Number,
    required: true
  },
  estimatedDuration: {
    type: Number,
    required: true
  },
  fare: {
    type: Number,
    required: true
  },
  routeType: {
    type: String,
    enum: ['regular', 'express', 'special'],
    default: 'regular'
  },
  isActive: {
    type: Boolean,
    default: true
  },
  schedule: [{
    dayOfWeek: {
      type: String,
      enum: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
    },
    departureTimes: [String],
    frequency: Number
  }],
  vehicleTypes: [{
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Vehicle'
  }],
  createdAt: {
    type: Date,
    default: Date.now
  }
});

module.exports = mongoose.model('Route', routeSchema);
