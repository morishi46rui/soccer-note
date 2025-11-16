"use client";

import { Box } from "@mui/material";
import type { PropsWithChildren } from "react";
import { AdminSidebar } from "./admin-sidebar";

export const AdminLayout = ({ children }: PropsWithChildren) => {
  return (
    <Box sx={{ display: "flex", minHeight: "100vh" }}>
      <AdminSidebar />

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          backgroundColor: "grey.50",
          minHeight: "100vh",
        }}
      >
        {children}
      </Box>
    </Box>
  );
};
