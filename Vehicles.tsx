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
} from '@mui/material';
import {
  Add,
  Edit,
  Delete,
  DirectionsCar,
  LocalGasStation,
  People,
  Settings,
  LocationOn,
} from '@mui/icons-material';
import { motion } from 'framer-motion';
import { useQuery, useMutation, useQueryClient } from 'react-query';
import { vehiclesAPI } from '../services/api';

const Vehicles: React.FC = () => {
  const [open, setOpen] = useState(false);
  const [selectedVehicle, setSelectedVehicle] = useState<any>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');
  const queryClient = useQueryClient();

  const { data: vehicles, isLoading } = useQuery('vehicles', () => vehiclesAPI.getAll());

  const updateStatusMutation = useMutation(
    ({ id, status }: { id: string; status: string }) => 
      vehiclesAPI.updateStatus(id, status),
    {
      onSuccess: () => {
        queryClient.invalidateQueries('vehicles');
      },
    }
  );

  const handleStatusChange = (id: string, status: string) => {
    updateStatusMutation.mutate({ id, status });
  };

  const filteredVehicles = vehicles?.data?.vehicles?.filter((vehicle: any) => {
    const matchesSearch = vehicle.registrationNumber?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          vehicle.make?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          vehicle.model?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = filterStatus === 'all' || vehicle.status === filterStatus;
    return matchesSearch && matchesStatus;
  }) || [];

  const VehicleCard: React.FC<{ vehicle: any }> = ({ vehicle }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
    >
      <Card sx={{ height: '100%', position: 'relative' }}>
        <CardContent>
          <Box display="flex" justifyContent="space-between" alignItems="start" mb={2}>
            <Box display="flex" alignItems="center">
              <DirectionsCar sx={{ fontSize: 40, mr: 2, color: 'primary.main' }} />
              <Box>
                <Typography variant="h6" fontWeight="bold">
                  {vehicle.make} {vehicle.model}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {vehicle.registrationNumber}
                </Typography>
              </Box>
            </Box>
            <Chip
              label={vehicle.status}
              color={
                vehicle.status === 'active' ? 'success' :
                vehicle.status === 'maintenance' ? 'warning' :
                vehicle.status === 'in_transit' ? 'info' : 'default'
              }
              size="small"
            />
          </Box>

          <Box display="flex" flexDirection="column" gap={1}>
            <Box display="flex" alignItems="center">
              <People fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                Capacity: {vehicle.capacity} seats
              </Typography>
            </Box>
            <Box display="flex" alignItems="center">
              <LocalGasStation fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                Fuel: {vehicle.fuelType}
              </Typography>
            </Box>
            <Box display="flex" alignItems="center">
              <Settings fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                Year: {vehicle.year}
              </Typography>
            </Box>
            <Box display="flex" alignItems="center">
              <LocationOn fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                {vehicle.currentLocation ? 'Location tracked' : 'No location data'}
              </Typography>
            </Box>
          </Box>

          {vehicle.driver && (
            <Box mt={2} p={1} bgcolor="grey.100" borderRadius={1}>
              <Typography variant="body2" color="text.secondary">
                Driver: {vehicle.driver.user?.profile?.firstName} {vehicle.driver.user?.profile?.lastName}
              </Typography>
            </Box>
          )}

          <Box display="flex" gap={1} mt={2}>
            <FormControl size="small" sx={{ minWidth: 120 }}>
              <Select
                value={vehicle.status}
                onChange={(e) => handleStatusChange(vehicle._id, e.target.value)}
              >
                <MenuItem value="active">Active</MenuItem>
                <MenuItem value="maintenance">Maintenance</MenuItem>
                <MenuItem value="inactive">Inactive</MenuItem>
                <MenuItem value="in_transit">In Transit</MenuItem>
              </Select>
            </FormControl>
            <Button
              size="small"
              variant="outlined"
              startIcon={<Edit />}
              onClick={() => {
                setSelectedVehicle(vehicle);
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
          Vehicles Management
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
          Add Vehicle
        </Button>
      </Box>

      <Box display="flex" gap={2} mb={3}>
        <TextField
          placeholder="Search vehicles..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          sx={{ flex: 1 }}
        />
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Status Filter</InputLabel>
          <Select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            label="Status Filter"
          >
            <MenuItem value="all">All Status</MenuItem>
            <MenuItem value="active">Active</MenuItem>
            <MenuItem value="maintenance">Maintenance</MenuItem>
            <MenuItem value="inactive">Inactive</MenuItem>
            <MenuItem value="in_transit">In Transit</MenuItem>
          </Select>
        </FormControl>
      </Box>

      <Grid container spacing={3}>
        {filteredVehicles.map((vehicle: any) => (
          <Grid item xs={12} sm={6} md={4} key={vehicle._id}>
            <VehicleCard vehicle={vehicle} />
          </Grid>
        ))}
      </Grid>

      <Dialog open={open} onClose={() => setOpen(false)} maxWidth="md" fullWidth>
        <DialogTitle>
          {selectedVehicle ? 'Edit Vehicle' : 'Add New Vehicle'}
        </DialogTitle>
        <DialogContent>
          <Box display="flex" flexDirection="column" gap={2} mt={2}>
            <TextField label="Registration Number" fullWidth />
            <TextField label="Make" fullWidth />
            <TextField label="Model" fullWidth />
            <TextField label="Year" type="number" fullWidth />
            <FormControl fullWidth>
              <InputLabel>Type</InputLabel>
              <Select label="Type">
                <MenuItem value="bus">Bus</MenuItem>
                <MenuItem value="truck">Truck</MenuItem>
                <MenuItem value="van">Van</MenuItem>
                <MenuItem value="car">Car</MenuItem>
                <MenuItem value="motorcycle">Motorcycle</MenuItem>
              </Select>
            </FormControl>
            <TextField label="Capacity" type="number" fullWidth />
            <FormControl fullWidth>
              <InputLabel>Fuel Type</InputLabel>
              <Select label="Fuel Type">
                <MenuItem value="petrol">Petrol</MenuItem>
                <MenuItem value="diesel">Diesel</MenuItem>
                <MenuItem value="electric">Electric</MenuItem>
                <MenuItem value="hybrid">Hybrid</MenuItem>
              </Select>
            </FormControl>
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpen(false)}>Cancel</Button>
          <Button variant="contained">
            {selectedVehicle ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default Vehicles;
