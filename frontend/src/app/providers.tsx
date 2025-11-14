"use client";

import { AuthProvider } from "@/components/auth-provider";
import { SnackbarProvider } from "@/contexts/snackbar-context";
import { EmotionCacheProvider } from "@/lib/emotion-cache";
import { CssBaseline, ThemeProvider, createTheme } from "@mui/material";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import type { PropsWithChildren } from "react";
import { useMemo, useState } from "react";

export const Providers = ({ children }: PropsWithChildren) => {
  const [queryClient] = useState(
    () =>
      new QueryClient({
        defaultOptions: {
          queries: {
            staleTime: 60 * 1000, // 1分
            refetchOnWindowFocus: false,
          },
        },
      })
  );

  const theme = useMemo(() => createTheme(), []);

  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <EmotionCacheProvider>
          <ThemeProvider theme={theme}>
            <SnackbarProvider>
              <CssBaseline />
              {children}
            </SnackbarProvider>
          </ThemeProvider>
        </EmotionCacheProvider>
      </AuthProvider>
      <ReactQueryDevtools initialIsOpen={false} />
    </QueryClientProvider>
  );
};
