"use client";

import { Box } from "@mui/material";
import type { PropsWithChildren } from "react";
import { Sidebar } from "./sidebar";

export const DashboardLayout = ({ children }: PropsWithChildren) => {
  return (
    <Box sx={{ display: "flex", minHeight: "100vh" }}>
      <Sidebar />

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          backgroundColor: "background.default",
        }}
      >
        {children}
      </Box>
    </Box>
  );
};
