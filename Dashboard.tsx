import React, { useState, useEffect } from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  Paper,
  Avatar,
  LinearProgress,
  IconButton,
  Chip,
} from '@mui/material';
import {
  DirectionsCar,
  People,
  Route,
  Assignment,
  TrendingUp,
  LocationOn,
  Speed,
  AccessTime,
} from '@mui/icons-material';
import { motion } from 'framer-motion';
import { Canvas } from '@react-three/fiber';
import { OrbitControls, Box as ThreeBox, Sphere } from '@react-three/drei';
import { useQuery } from 'react-query';
import { bookingsAPI, driversAPI, vehiclesAPI, routesAPI } from '../services/api';
import { useAuth } from '../hooks/useAuth';

// 3D Animated Globe Component
const AnimatedGlobe: React.FC = () => {
  return (
    <div style={{ height: 300, width: '100%' }}>
      <Canvas camera={{ position: [0, 0, 5] }}>
        <ambientLight intensity={0.5} />
        <pointLight position={[10, 10, 10]} />
        <Sphere args={[2, 32, 32]} position={[0, 0, 0]}>
          <meshStandardMaterial color="#4a90e2" wireframe />
        </Sphere>
        <ThreeBox args={[0.5, 0.5, 0.5]} position={[2, 0, 0]}>
          <meshStandardMaterial color="#e74c3c" />
        </ThreeBox>
        <ThreeBox args={[0.3, 0.3, 0.3]} position={[-2, 1, 0]}>
          <meshStandardMaterial color="#2ecc71" />
        </ThreeBox>
        <ThreeBox args={[0.4, 0.4, 0.4]} position={[0, -2, 0]}>
          <meshStandardMaterial color="#f39c12" />
        </ThreeBox>
        <OrbitControls enableZoom={false} autoRotate autoRotateSpeed={1} />
      </Canvas>
    </div>
  );
};

// Stats Card Component
const StatsCard: React.FC<{ title: string; value: string; icon: React.ReactNode; color: string; trend?: number }> = ({
  title,
  value,
  icon,
  color,
  trend,
}) => {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5 }}
    >
      <Card
        sx={{
          height: '100%',
          background: `linear-gradient(135deg, ${color}22, ${color}11)`,
          border: `2px solid ${color}44`,
          borderRadius: 3,
          position: 'relative',
          overflow: 'hidden',
        }}
      >
        <CardContent>
          <Box display="flex" alignItems="center" justifyContent="space-between" mb={2}>
            <Avatar sx={{ bgcolor: color, width: 48, height: 48 }}>
              {icon}
            </Avatar>
            {trend !== undefined && (
              <Chip
                label={`${trend > 0 ? '+' : ''}${trend}%`}
                color={trend > 0 ? 'success' : 'error'}
                size="small"
              />
            )}
          </Box>
          <Typography variant="h4" component="div" fontWeight="bold" color={color}>
            {value}
          </Typography>
          <Typography variant="body2" color="text.secondary">
            {title}
          </Typography>
        </CardContent>
      </Card>
    </motion.div>
  );
};

// Real-time Tracking Component
const RealTimeTracking: React.FC = () => {
  const [activeVehicles, setActiveVehicles] = useState(3);
  
  return (
    <Card sx={{ height: '100%' }}>
      <CardContent>
        <Typography variant="h6" gutterBottom>
          Real-time Tracking
        </Typography>
        <Box sx={{ height: 200, position: 'relative', bgcolor: '#f5f5f5', borderRadius: 2, overflow: 'hidden' }}>
          {/* Simulated map with moving vehicles */}
          <motion.div
            style={{
              position: 'absolute',
              width: 20,
              height: 20,
              background: '#e74c3c',
              borderRadius: '50%',
              top: '30%',
              left: '20%',
            }}
            animate={{
              x: [0, 150, 0],
              y: [0, -50, 0],
            }}
            transition={{
              duration: 8,
              repeat: Infinity,
              ease: 'easeInOut',
            }}
          />
          <motion.div
            style={{
              position: 'absolute',
              width: 20,
              height: 20,
              background: '#2ecc71',
              borderRadius: '50%',
              top: '60%',
              left: '70%',
            }}
            animate={{
              x: [0, -100, 0],
              y: [0, 30, 0],
            }}
            transition={{
              duration: 10,
              repeat: Infinity,
              ease: 'easeInOut',
            }}
          />
          <motion.div
            style={{
              position: 'absolute',
              width: 20,
              height: 20,
              background: '#f39c12',
              borderRadius: '50%',
              top: '40%',
              left: '50%',
            }}
            animate={{
              x: [0, 80, 0],
              y: [0, 40, 0],
            }}
            transition={{
              duration: 12,
              repeat: Infinity,
              ease: 'easeInOut',
            }}
          />
          <Box
            sx={{
              position: 'absolute',
              bottom: 10,
              left: 10,
              bgcolor: 'rgba(255,255,255,0.9)',
              p: 1,
              borderRadius: 1,
            }}
          >
            <Typography variant="caption">
              {activeVehicles} vehicles active
            </Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  );
};

// Recent Activity Component
const RecentActivity: React.FC = () => {
  const activities = [
    { id: 1, type: 'booking', message: 'New booking #BK12345 created', time: '2 min ago' },
    { id: 2, type: 'driver', message: 'Driver John Doe started trip', time: '5 min ago' },
    { id: 3, type: 'vehicle', message: 'Vehicle ABC-123 maintenance completed', time: '15 min ago' },
    { id: 4, type: 'route', message: 'Route R-001 updated', time: '30 min ago' },
  ];

  return (
    <Card sx={{ height: '100%' }}>
      <CardContent>
        <Typography variant="h6" gutterBottom>
          Recent Activity
        </Typography>
        <Box sx={{ maxHeight: 300, overflowY: 'auto' }}>
          {activities.map((activity, index) => (
            <motion.div
              key={activity.id}
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: index * 0.1 }}
            >
              <Box sx={{ py: 1, borderBottom: '1px solid #eee' }}>
                <Typography variant="body2" fontWeight="medium">
                  {activity.message}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  {activity.time}
                </Typography>
              </Box>
            </motion.div>
          ))}
        </Box>
      </CardContent>
    </Card>
  );
};

const Dashboard: React.FC = () => {
  const { user } = useAuth();
  
  // Fetch dashboard data
  const { data: bookings } = useQuery('bookings', () => bookingsAPI.getAll());
  const { data: drivers } = useQuery('drivers', () => driversAPI.getAll());
  const { data: vehicles } = useQuery('vehicles', () => vehiclesAPI.getAll());
  const { data: routes } = useQuery('routes', () => routesAPI.getAll());

  const stats = [
    {
      title: 'Total Bookings',
      value: bookings?.data?.bookings?.length || 0,
      icon: <Assignment />,
      color: '#e74c3c',
      trend: 12,
    },
    {
      title: 'Active Drivers',
      value: drivers?.data?.drivers?.filter((d: any) => d.isAvailable)?.length || 0,
      icon: <People />,
      color: '#2ecc71',
      trend: 8,
    },
    {
      title: 'Active Vehicles',
      value: vehicles?.data?.vehicles?.filter((v: any) => v.status === 'active')?.length || 0,
      icon: <DirectionsCar />,
      color: '#f39c12',
      trend: -3,
    },
    {
      title: 'Total Routes',
      value: routes?.data?.routes?.length || 0,
      icon: <Route />,
      color: '#9b59b6',
      trend: 5,
    },
  ];

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: -20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <Typography variant="h4" component="h1" gutterBottom fontWeight="bold">
          Dashboard
        </Typography>
        <Typography variant="body1" color="text.secondary">
          Welcome back, {user?.profile?.firstName || user?.username}!
        </Typography>
      </motion.div>

      {/* Stats Grid */}
      <Grid container spacing={3} sx={{ mt: 2 }}>
        {stats.map((stat, index) => (
          <Grid item xs={12} sm={6} md={3} key={stat.title}>
            <StatsCard {...stat} />
          </Grid>
        ))}
      </Grid>

      {/* Main Content Grid */}
      <Grid container spacing={3} sx={{ mt: 2 }}>
        {/* 3D Globe */}
        <Grid item xs={12} md={6}>
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.3, duration: 0.5 }}
          >
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  Global Tracking View
                </Typography>
                <AnimatedGlobe />
              </CardContent>
            </Card>
          </motion.div>
        </Grid>

        {/* Real-time Tracking */}
        <Grid item xs={12} md={6}>
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.4, duration: 0.5 }}
          >
            <RealTimeTracking />
          </motion.div>
        </Grid>

        {/* Recent Activity */}
        <Grid item xs={12} md={6}>
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.5, duration: 0.5 }}
          >
            <RecentActivity />
          </motion.div>
        </Grid>

        {/* Quick Stats */}
        <Grid item xs={12} md={6}>
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.6, duration: 0.5 }}
          >
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  Performance Metrics
                </Typography>
                <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                  <Box>
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                      <Typography variant="body2">On-time Performance</Typography>
                      <Typography variant="body2" fontWeight="bold">92%</Typography>
                    </Box>
                    <LinearProgress variant="determinate" value={92} sx={{ height: 8, borderRadius: 4 }} />
                  </Box>
                  <Box>
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                      <Typography variant="body2">Customer Satisfaction</Typography>
                      <Typography variant="body2" fontWeight="bold">4.8/5</Typography>
                    </Box>
                    <LinearProgress variant="determinate" value={96} sx={{ height: 8, borderRadius: 4 }} />
                  </Box>
                  <Box>
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                      <Typography variant="body2">Fleet Utilization</Typography>
                      <Typography variant="body2" fontWeight="bold">78%</Typography>
                    </Box>
                    <LinearProgress variant="determinate" value={78} sx={{ height: 8, borderRadius: 4 }} />
                  </Box>
                </Box>
              </CardContent>
            </Card>
          </motion.div>
        </Grid>
      </Grid>
    </Box>
  );
};

export default Dashboard;
