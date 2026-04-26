import React, { useState } from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  Chip,
  Button,
  TextField,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  IconButton,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
  Divider,
} from '@mui/material';
import {
  Add,
  Edit,
  Delete,
  Route as RouteIcon,
  LocationOn,
  AccessTime,
  AttachMoney,
  Directions,
} from '@mui/icons-material';
import { motion } from 'framer-motion';
import { useQuery, useMutation, useQueryClient } from 'react-query';
import { routesAPI } from '../services/api';

const Routes: React.FC = () => {
  const [open, setOpen] = useState(false);
  const [selectedRoute, setSelectedRoute] = useState<any>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterType, setFilterType] = useState('all');
  const queryClient = useQueryClient();

  const { data: routes, isLoading } = useQuery('routes', () => routesAPI.getAll());

  const updateStatusMutation = useMutation(
    ({ id, isActive }: { id: string; isActive: boolean }) => 
      routesAPI.updateStatus(id, isActive),
    {
      onSuccess: () => {
        queryClient.invalidateQueries('routes');
      },
    }
  );

  const handleStatusToggle = (id: string, isActive: boolean) => {
    updateStatusMutation.mutate({ id, isActive });
  };

  const filteredRoutes = routes?.data?.routes?.filter((route: any) => {
    const matchesSearch = route.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          route.code?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesType = filterType === 'all' || route.routeType === filterType;
    return matchesSearch && matchesType;
  }) || [];

  const RouteCard: React.FC<{ route: any }> = ({ route }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
    >
      <Card sx={{ height: '100%' }}>
        <CardContent>
          <Box display="flex" justifyContent="space-between" alignItems="start" mb={2}>
            <Box>
              <Typography variant="h6" fontWeight="bold">
                {route.name}
              </Typography>
              <Typography variant="body2" color="text.secondary">
                Code: {route.code}
              </Typography>
            </Box>
            <Box display="flex" gap={1}>
              <Chip
                label={route.routeType}
                color="primary"
                size="small"
              />
              <Chip
                label={route.isActive ? 'Active' : 'Inactive'}
                color={route.isActive ? 'success' : 'default'}
                size="small"
              />
            </Box>
          </Box>

          <Box display="flex" flexDirection="column" gap={2}>
            <Box display="flex" alignItems="center">
              <LocationOn fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2" fontWeight="medium">
                  From: {route.startLocation?.name}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  {route.startLocation?.address}
                </Typography>
              </Box>
            </Box>

            <Box display="flex" alignItems="center">
              <Directions fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2" fontWeight="medium">
                  To: {route.endLocation?.name}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  {route.endLocation?.address}
                </Typography>
              </Box>
            </Box>

            <Divider />

            <Grid container spacing={2}>
              <Grid item xs={6}>
                <Box display="flex" alignItems="center">
                  <AccessTime fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
                  <Box>
                    <Typography variant="caption" color="text.secondary">
                      Duration
                    </Typography>
                    <Typography variant="body2">
                      {route.estimatedDuration} min
                    </Typography>
                  </Box>
                </Box>
              </Grid>
              <Grid item xs={6}>
                <Box display="flex" alignItems="center">
                  <AttachMoney fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
                  <Box>
                    <Typography variant="caption" color="text.secondary">
                      Fare
                    </Typography>
                    <Typography variant="body2">
                      ${route.fare}
                    </Typography>
                  </Box>
                </Box>
              </Grid>
            </Grid>

            <Box display="flex" alignItems="center">
              <RouteIcon fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                Distance: {route.distance} km
              </Typography>
            </Box>

            {route.waypoints && route.waypoints.length > 0 && (
              <Box>
                <Typography variant="caption" color="text.secondary">
                  Waypoints ({route.waypoints.length})
                </Typography>
                <Box display="flex" flexWrap="wrap" gap={0.5}>
                  {route.waypoints.slice(0, 3).map((waypoint: any, index: number) => (
                    <Chip
                      key={index}
                      label={waypoint.name}
                      size="small"
                      variant="outlined"
                    />
                  ))}
                  {route.waypoints.length > 3 && (
                    <Chip
                      label={`+${route.waypoints.length - 3} more`}
                      size="small"
                      variant="outlined"
                    />
                  )}
                </Box>
              </Box>
            )}
          </Box>

          <Box display="flex" gap={1} mt={2}>
            <Button
              size="small"
              variant="outlined"
              onClick={() => handleStatusToggle(route._id, !route.isActive)}
              color={route.isActive ? 'warning' : 'success'}
            >
              {route.isActive ? 'Deactivate' : 'Activate'}
            </Button>
            <Button
              size="small"
              variant="outlined"
              startIcon={<Edit />}
              onClick={() => {
                setSelectedRoute(route);
                setOpen(true);
              }}
            >
              Edit
            </Button>
          </Box>
        </CardContent>
      </Card>
    </motion.div>
  );

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4" component="h1" fontWeight="bold">
          Routes Management
        </Typography>
        <Button
          variant="contained"
          startIcon={<Add />}
          onClick={() => setOpen(true)}
          sx={{
            background: 'linear-gradient(45deg, #667eea, #764ba2)',
            '&:hover': {
              background: 'linear-gradient(45deg, #5a67d8, #6b46c1)',
            },
          }}
        >
          Add Route
        </Button>
      </Box>

      <Box display="flex" gap={2} mb={3}>
        <TextField
          placeholder="Search routes..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          sx={{ flex: 1 }}
        />
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Type Filter</InputLabel>
          <Select
            value={filterType}
            onChange={(e) => setFilterType(e.target.value)}
            label="Type Filter"
          >
            <MenuItem value="all">All Types</MenuItem>
            <MenuItem value="regular">Regular</MenuItem>
            <MenuItem value="express">Express</MenuItem>
            <MenuItem value="special">Special</MenuItem>
          </Select>
        </FormControl>
      </Box>

      <Grid container spacing={3}>
        {filteredRoutes.map((route: any) => (
          <Grid item xs={12} sm={6} md={4} key={route._id}>
            <RouteCard route={route} />
          </Grid>
        ))}
      </Grid>

      <Dialog open={open} onClose={() => setOpen(false)} maxWidth="md" fullWidth>
        <DialogTitle>
          {selectedRoute ? 'Edit Route' : 'Add New Route'}
        </DialogTitle>
        <DialogContent>
          <Box display="flex" flexDirection="column" gap={2} mt={2}>
            <TextField label="Route Name" fullWidth />
            <TextField label="Route Code" fullWidth />
            <TextField label="Start Location" fullWidth />
            <TextField label="End Location" fullWidth />
            <TextField label="Distance (km)" type="number" fullWidth />
            <TextField label="Estimated Duration (minutes)" type="number" fullWidth />
            <TextField label="Fare ($)" type="number" fullWidth />
            <FormControl fullWidth>
              <InputLabel>Route Type</InputLabel>
              <Select label="Route Type">
                <MenuItem value="regular">Regular</MenuItem>
                <MenuItem value="express">Express</MenuItem>
                <MenuItem value="special">Special</MenuItem>
              </Select>
            </FormControl>
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpen(false)}>Cancel</Button>
          <Button variant="contained">
            {selectedRoute ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default Routes;
