import React from 'react';
import { Box, CircularProgress, Typography } from '@mui/material';

const LoadingSpinner: React.FC = () => {
  return (
    <Box
      sx={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        height: '100vh',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      }}
    >
      <CircularProgress
        size={60}
        thickness={4}
        sx={{
          color: 'white',
          mb: 2,
        }}
      />
      <Typography variant="h6" color="white">
        Loading Transport Tracker...
      </Typography>
    </Box>
  );
};

export default LoadingSpinner;
