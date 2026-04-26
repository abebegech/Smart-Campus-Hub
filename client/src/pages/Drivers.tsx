import React, { useState } from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  Avatar,
  Chip,
  Button,
  TextField,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  IconButton,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Rating,
} from '@mui/material';
import {
  Add,
  Edit,
  Delete,
  Phone,
  Email,
  LocationOn,
  Star,
  DirectionsCar,
  Person,
} from '@mui/icons-material';
import { motion } from 'framer-motion';
import { useQuery, useMutation, useQueryClient } from 'react-query';
import { driversAPI } from '../services/api';

const Drivers: React.FC = () => {
  const [open, setOpen] = useState(false);
  const [selectedDriver, setSelectedDriver] = useState<any>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const queryClient = useQueryClient();

  const { data: drivers, isLoading } = useQuery('drivers', () => driversAPI.getAll());

  const deleteMutation = useMutation(driversAPI.delete, {
    onSuccess: () => {
      queryClient.invalidateQueries('drivers');
    },
  });

  const handleEdit = (driver: any) => {
    setSelectedDriver(driver);
    setOpen(true);
  };

  const handleDelete = (id: string) => {
    if (window.confirm('Are you sure you want to delete this driver?')) {
      deleteMutation.mutate(id);
    }
  };

  const filteredDrivers = drivers?.data?.drivers?.filter((driver: any) =>
    driver.user?.username?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    driver.user?.email?.toLowerCase().includes(searchTerm.toLowerCase())
  ) || [];

  const DriverCard: React.FC<{ driver: any }> = ({ driver }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
    >
      <Card sx={{ height: '100%', position: 'relative' }}>
        <CardContent>
          <Box display="flex" alignItems="center" mb={2}>
            <Avatar
              src={driver.driverPhoto}
              sx={{ width: 64, height: 64, mr: 2 }}
            >
              <Person />
            </Avatar>
            <Box flex={1}>
              <Typography variant="h6" fontWeight="bold">
                {driver.user?.profile?.firstName} {driver.user?.profile?.lastName}
              </Typography>
              <Typography variant="body2" color="text.secondary">
                {driver.user?.email}
              </Typography>
              <Box display="flex" alignItems="center" mt={1}>
                <Rating value={driver.rating} readOnly size="small" />
                <Typography variant="caption" ml={1}>
                  ({driver.totalTrips} trips)
                </Typography>
              </Box>
            </Box>
            <Chip
              label={driver.isAvailable ? 'Available' : 'Busy'}
              color={driver.isAvailable ? 'success' : 'default'}
              size="small"
            />
          </Box>

          <Box display="flex" flexDirection="column" gap={1}>
            <Box display="flex" alignItems="center">
              <Phone fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                {driver.user?.profile?.phone || 'Not provided'}
              </Typography>
            </Box>
            <Box display="flex" alignItems="center">
              <Email fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                {driver.licenseNumber}
              </Typography>
            </Box>
            <Box display="flex" alignItems="center">
              <DirectionsCar fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                {driver.vehicleAssigned?.registrationNumber || 'No vehicle assigned'}
              </Typography>
            </Box>
            <Box display="flex" alignItems="center">
              <LocationOn fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Typography variant="body2">
                {driver.currentLocation ? 'Location tracked' : 'No location data'}
              </Typography>
            </Box>
          </Box>

          <Box display="flex" gap={1} mt={2}>
            <Button
              size="small"
              variant="outlined"
              startIcon={<Edit />}
              onClick={() => handleEdit(driver)}
            >
              Edit
            </Button>
            <Button
              size="small"
              variant="outlined"
              color="error"
              startIcon={<Delete />}
              onClick={() => handleDelete(driver._id)}
            >
              Delete
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
          Drivers Management
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
          Add Driver
        </Button>
      </Box>

      <TextField
        fullWidth
        placeholder="Search drivers..."
        value={searchTerm}
        onChange={(e) => setSearchTerm(e.target.value)}
        sx={{ mb: 3 }}
      />

      <Grid container spacing={3}>
        {filteredDrivers.map((driver: any) => (
          <Grid item xs={12} sm={6} md={4} key={driver._id}>
            <DriverCard driver={driver} />
          </Grid>
        ))}
      </Grid>

      <Dialog open={open} onClose={() => setOpen(false)} maxWidth="md" fullWidth>
        <DialogTitle>
          {selectedDriver ? 'Edit Driver' : 'Add New Driver'}
        </DialogTitle>
        <DialogContent>
          <Box display="flex" flexDirection="column" gap={2} mt={2}>
            <TextField label="First Name" fullWidth />
            <TextField label="Last Name" fullWidth />
            <TextField label="Email" type="email" fullWidth />
            <TextField label="Phone" fullWidth />
            <TextField label="License Number" fullWidth />
            <TextField label="Experience (years)" type="number" fullWidth />
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpen(false)}>Cancel</Button>
          <Button variant="contained">
            {selectedDriver ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default Drivers;
