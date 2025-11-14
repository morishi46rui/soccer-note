"use client";

import type { SnackbarMessage, SnackbarSeverity } from "@/types/snackbar";
import { Alert, Snackbar } from "@mui/material";
import { createContext, useState, type PropsWithChildren } from "react";

type SnackbarContextValue = {
  showSnackbar: (message: string, severity?: SnackbarSeverity) => void;
};

export const SnackbarContext = createContext<SnackbarContextValue | undefined>(
  undefined
);

export const SnackbarProvider = ({ children }: PropsWithChildren) => {
  const [open, setOpen] = useState(false);
  const [snackbarMessage, setSnackbarMessage] = useState<SnackbarMessage>({
    message: "",
    severity: "info",
  });

  const showSnackbar = (
    message: string,
    severity: SnackbarSeverity = "info"
  ) => {
    setSnackbarMessage({ message, severity });
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  return (
    <SnackbarContext.Provider value={{ showSnackbar }}>
      {children}
      <Snackbar
        open={open}
        autoHideDuration={6000}
        onClose={handleClose}
        anchorOrigin={{ vertical: "top", horizontal: "right" }}
      >
        <Alert
          onClose={handleClose}
          severity={snackbarMessage.severity}
          variant="filled"
          sx={{ width: "100%" }}
        >
          {snackbarMessage.message}
        </Alert>
      </Snackbar>
    </SnackbarContext.Provider>
  );
};
