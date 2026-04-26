import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:5000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add token to requests if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Auth API
export const authAPI = {
  login: (email: string, password: string) => api.post('/auth/login', { email, password }),
  register: (userData: any) => api.post('/auth/register', userData),
  getMe: () => api.get('/auth/me'),
  updateProfile: (profile: any) => api.put('/auth/profile', { profile }),
  changePassword: (currentPassword: string, newPassword: string) => 
    api.put('/auth/password', { currentPassword, newPassword }),
};

// Users API
export const usersAPI = {
  getAll: (params?: any) => api.get('/users', { params }),
  getById: (id: string) => api.get(`/users/${id}`),
  update: (id: string, userData: any) => api.put(`/users/${id}`, userData),
  updateStatus: (id: string, isActive: boolean) => api.put(`/users/${id}/status`, { isActive }),
  delete: (id: string) => api.delete(`/users/${id}`),
};

// Drivers API
export const driversAPI = {
  getAll: () => api.get('/drivers'),
  getById: (id: string) => api.get(`/drivers/${id}`),
  create: (formData: FormData) => api.post('/drivers', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  update: (id: string, formData: FormData) => api.put(`/drivers/${id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  updateLocation: (id: string, latitude: number, longitude: number) => 
    api.put(`/drivers/${id}/location`, { latitude, longitude }),
  updateAvailability: (id: string, isAvailable: boolean) => 
    api.put(`/drivers/${id}/availability`, { isAvailable }),
  getAvailable: () => api.get('/drivers/available/all'),
};

// Vehicles API
export const vehiclesAPI = {
  getAll: (params?: any) => api.get('/vehicles', { params }),
  getById: (id: string) => api.get(`/vehicles/${id}`),
  create: (formData: FormData) => api.post('/vehicles', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  update: (id: string, formData: FormData) => api.put(`/vehicles/${id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  updateStatus: (id: string, status: string) => api.put(`/vehicles/${id}/status`, { status }),
  updateLocation: (id: string, latitude: number, longitude: number) => 
    api.put(`/vehicles/${id}/location`, { latitude, longitude }),
  assignDriver: (id: string, driverId: string) => api.put(`/vehicles/${id}/assign-driver`, { driverId }),
  removeDriver: (id: string) => api.put(`/vehicles/${id}/remove-driver`),
  getAvailable: () => api.get('/vehicles/available/all'),
};

// Routes API
export const routesAPI = {
  getAll: (params?: any) => api.get('/routes', { params }),
  getById: (id: string) => api.get(`/routes/${id}`),
  create: (routeData: any) => api.post('/routes', routeData),
  update: (id: string, routeData: any) => api.put(`/routes/${id}`, routeData),
  updateStatus: (id: string, isActive: boolean) => api.put(`/routes/${id}/status`, { isActive }),
  getNearby: (latitude: number, longitude: number, radius?: number) => 
    api.get('/routes/search/nearby', { params: { latitude, longitude, radius } }),
};

// Bookings API
export const bookingsAPI = {
  getAll: (params?: any) => api.get('/bookings', { params }),
  getById: (id: string) => api.get(`/bookings/${id}`),
  create: (bookingData: any) => api.post('/bookings', bookingData),
  update: (id: string, bookingData: any) => api.put(`/bookings/${id}`, bookingData),
  updateStatus: (id: string, status: string) => api.put(`/bookings/${id}/status`, { status }),
  assignDriver: (id: string, driverId: string) => api.put(`/bookings/${id}/assign-driver`, { driverId }),
  updateTracking: (id: string, trackingData: any) => api.put(`/bookings/${id}/tracking`, trackingData),
  addFeedback: (id: string, feedbackData: any) => api.put(`/bookings/${id}/feedback`, feedbackData),
};

// GPS API
export const gpsAPI = {
  geocode: (address: string) => api.get('/gps/geocode', { params: { address } }),
  reverseGeocode: (latitude: number, longitude: number) => 
    api.get('/gps/reverse-geocode', { params: { latitude, longitude } }),
  getRoute: (startLat: number, startLng: number, endLat: number, endLng: number) =>
    api.get('/gps/route', { params: { startLat, startLng, endLat, endLng } }),
  getTraffic: (latitude: number, longitude: number, radius?: number) =>
    api.get('/gps/traffic', { params: { latitude, longitude, radius } }),
};

export default api;
