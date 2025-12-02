"use client";

import { Alert } from "@mui/material";
import type { ErrorStateProps } from "../types/team";

export const ErrorState = ({ message }: ErrorStateProps) => {
  return (
    <Alert severity="error" sx={{ my: 2 }}>
      {message}
    </Alert>
  );
};
