export type SnackbarSeverity = "success" | "error" | "warning" | "info";

export type SnackbarMessage = {
  message: string;
  severity: SnackbarSeverity;
};
