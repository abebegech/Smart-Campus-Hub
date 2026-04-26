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
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  LinearProgress,
  Avatar,
} from '@mui/material';
import {
  Add,
  Edit,
  Delete,
  Assignment,
  Person,
  DirectionsCar,
  Route,
  Schedule,
  LocationOn,
  AttachMoney,
  Star,
} from '@mui/icons-material';
import { motion } from 'framer-motion';
import { useQuery, useMutation, useQueryClient } from 'react-query';
import { bookingsAPI } from '../services/api';
import { format } from 'date-fns';

const Bookings: React.FC = () => {
  const [open, setOpen] = useState(false);
  const [selectedBooking, setSelectedBooking] = useState<any>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');
  const queryClient = useQueryClient();

  const { data: bookings, isLoading } = useQuery('bookings', () => bookingsAPI.getAll());

  const updateStatusMutation = useMutation(
    ({ id, status }: { id: string; status: string }) => 
      bookingsAPI.updateStatus(id, status),
    {
      onSuccess: () => {
        queryClient.invalidateQueries('bookings');
      },
    }
  );

  const handleStatusChange = (id: string, status: string) => {
    updateStatusMutation.mutate({ id, status });
  };

  const filteredBookings = bookings?.data?.bookings?.filter((booking: any) => {
    const matchesSearch = booking.bookingReference?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          booking.passenger?.username?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = filterStatus === 'all' || booking.status === filterStatus;
    return matchesSearch && matchesStatus;
  }) || [];

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending': return 'warning';
      case 'confirmed': return 'info';
      case 'driver_assigned': return 'primary';
      case 'in_progress': return 'secondary';
      case 'completed': return 'success';
      case 'cancelled': return 'error';
      case 'no_show': return 'default';
      default: return 'default';
    }
  };

  const BookingCard: React.FC<{ booking: any }> = ({ booking }) => (
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
                {booking.bookingReference}
              </Typography>
              <Typography variant="body2" color="text.secondary">
                Passenger: {booking.passenger?.profile?.firstName} {booking.passenger?.profile?.lastName}
              </Typography>
            </Box>
            <Chip
              label={booking.status.replace('_', ' ').toUpperCase()}
              color={getStatusColor(booking.status) as any}
              size="small"
            />
          </Box>

          <Box display="flex" flexDirection="column" gap={2}>
            <Box display="flex" alignItems="center">
              <Route fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2" fontWeight="medium">
                  {booking.route?.name}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  {booking.route?.code}
                </Typography>
              </Box>
            </Box>

            <Box display="flex" alignItems="center">
              <DirectionsCar fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2">
                  {booking.vehicle?.make} {booking.vehicle?.model}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  {booking.vehicle?.registrationNumber}
                </Typography>
              </Box>
            </Box>

            {booking.driver && (
              <Box display="flex" alignItems="center">
                <Person fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
                <Box flex={1}>
                  <Typography variant="body2">
                    {booking.driver.user?.profile?.firstName} {booking.driver.user?.profile?.lastName}
                  </Typography>
                  <Typography variant="caption" color="text.secondary">
                    Driver
                  </Typography>
                </Box>
              </Box>
            )}

            <Box display="flex" alignItems="center">
              <Schedule fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2">
                  {format(new Date(booking.scheduledDate), 'MMM dd, yyyy')} at {booking.scheduledTime}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  Scheduled
                </Typography>
              </Box>
            </Box>

            <Box display="flex" alignItems="center">
              <LocationOn fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2">
                  From: {booking.pickupLocation?.name}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  To: {booking.dropoffLocation?.name}
                </Typography>
              </Box>
            </Box>

            <Box display="flex" alignItems="center">
              <AttachMoney fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
              <Box flex={1}>
                <Typography variant="body2" fontWeight="bold">
                  ${booking.fare?.totalFare || 0}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  Total Fare
                </Typography>
              </Box>
            </Box>

            {booking.feedback && (
              <Box display="flex" alignItems="center">
                <Star fontSize="small" sx={{ mr: 1, color: 'text.secondary' }} />
                <Box flex={1}>
                  <Typography variant="body2">
                    Rating: {booking.feedback.rating}/5
                  </Typography>
                  {booking.feedback.comment && (
                    <Typography variant="caption" color="text.secondary">
                      "{booking.feedback.comment}"
                    </Typography>
                  )}
                </Box>
              </Box>
            )}
          </Box>

          <Box display="flex" gap={1} mt={2}>
            <FormControl size="small" sx={{ minWidth: 120 }}>
              <Select
                value={booking.status}
                onChange={(e) => handleStatusChange(booking._id, e.target.value)}
              >
                <MenuItem value="pending">Pending</MenuItem>
                <MenuItem value="confirmed">Confirmed</MenuItem>
                <MenuItem value="driver_assigned">Driver Assigned</MenuItem>
                <MenuItem value="in_progress">In Progress</MenuItem>
                <MenuItem value="completed">Completed</MenuItem>
                <MenuItem value="cancelled">Cancelled</MenuItem>
                <MenuItem value="no_show">No Show</MenuItem>
              </Select>
            </FormControl>
            <Button
              size="small"
              variant="outlined"
              startIcon={<Edit />}
              onClick={() => {
                setSelectedBooking(booking);
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
          Bookings Management
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
          New Booking
        </Button>
      </Box>

      <Box display="flex" gap={2} mb={3}>
        <TextField
          placeholder="Search bookings..."
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
            <MenuItem value="pending">Pending</MenuItem>
            <MenuItem value="confirmed">Confirmed</MenuItem>
            <MenuItem value="driver_assigned">Driver Assigned</MenuItem>
            <MenuItem value="in_progress">In Progress</MenuItem>
            <MenuItem value="completed">Completed</MenuItem>
            <MenuItem value="cancelled">Cancelled</MenuItem>
            <MenuItem value="no_show">No Show</MenuItem>
          </Select>
        </FormControl>
      </Box>

      <Grid container spacing={3}>
        {filteredBookings.map((booking: any) => (
          <Grid item xs={12} sm={6} md={4} key={booking._id}>
            <BookingCard booking={booking} />
          </Grid>
        ))}
      </Grid>

      <Dialog open={open} onClose={() => setOpen(false)} maxWidth="md" fullWidth>
        <DialogTitle>
          {selectedBooking ? 'Edit Booking' : 'Create New Booking'}
        </DialogTitle>
        <DialogContent>
          <Box display="flex" flexDirection="column" gap={2} mt={2}>
            <TextField label="Passenger" fullWidth />
            <FormControl fullWidth>
              <InputLabel>Route</InputLabel>
              <Select label="Route">
                <MenuItem value="">Select a route</MenuItem>
              </Select>
            </FormControl>
            <FormControl fullWidth>
              <InputLabel>Vehicle</InputLabel>
              <Select label="Vehicle">
                <MenuItem value="">Select a vehicle</MenuItem>
              </Select>
            </FormControl>
            <TextField label="Pickup Location" fullWidth />
            <TextField label="Dropoff Location" fullWidth />
            <TextField
              label="Scheduled Date"
              type="date"
              fullWidth
              InputLabelProps={{ shrink: true }}
            />
            <TextField
              label="Scheduled Time"
              type="time"
              fullWidth
              InputLabelProps={{ shrink: true }}
            />
            <TextField label="Number of Passengers" type="number" fullWidth />
            <TextField label="Special Requests" multiline rows={3} fullWidth />
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpen(false)}>Cancel</Button>
          <Button variant="contained">
            {selectedBooking ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default Bookings;
