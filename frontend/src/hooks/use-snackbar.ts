import { SnackbarContext } from "@/contexts/snackbar-context";
import { useContext } from "react";

export const useSnackbar = () => {
  const context = useContext(SnackbarContext);
  if (!context) {
    throw new Error("useSnackbar must be used within SnackbarProvider");
  }
  return context;
};
