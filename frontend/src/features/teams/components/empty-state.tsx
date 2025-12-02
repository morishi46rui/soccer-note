"use client";

import { Box, Typography } from "@mui/material";
import type { EmptyStateProps } from "../types/team";

export const EmptyState = ({ message }: EmptyStateProps) => {
  return (
    <Box py={8} textAlign="center">
      <Typography color="text.secondary">{message}</Typography>
    </Box>
  );
};
