import { CssBaseline, ThemeProvider, createTheme } from "@mui/material";
import type { PropsWithChildren } from "react";
import { useMemo } from "react";

export const AppProvider = ({ children }: PropsWithChildren) => {
  const theme = useMemo(() => createTheme(), []);

  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      {children}
    </ThemeProvider>
  );
};
