import React from 'react';
import { Routes as RouterRoutes, Route, Navigate } from 'react-router-dom';
import { Box } from '@mui/material';
import { useAuth } from './hooks/useAuth';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Drivers from './pages/Drivers';
import Vehicles from './pages/Vehicles';
import RoutesPage from './pages/Routes';
import Bookings from './pages/Bookings';
import Layout from './components/Layout';
import LoadingSpinner from './components/LoadingSpinner';

function App() {
  const { user, loading } = useAuth();

  if (loading) {
    return <LoadingSpinner />;
  }

  if (!user) {
    return (
      <Box sx={{ height: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <RouterRoutes>
          <Route path="/login" element={<Login />} />
          <Route path="*" element={<Navigate to="/login" replace />} />
        </RouterRoutes>
      </Box>
    );
  }

  return (
    <Layout>
      <RouterRoutes>
        <Route path="/" element={<Navigate to="/dashboard" replace />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/drivers" element={<Drivers />} />
        <Route path="/vehicles" element={<Vehicles />} />
        <Route path="/routes" element={<RoutesPage />} />
        <Route path="/bookings" element={<Bookings />} />
        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </RouterRoutes>
    </Layout>
  );
}

export default App;
