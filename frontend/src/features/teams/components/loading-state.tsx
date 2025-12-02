"use client";

import { Box, CircularProgress } from "@mui/material";

export const LoadingState = () => {
  return (
    <Box display="flex" justifyContent="center" py={8}>
      <CircularProgress />
    </Box>
  );
};
