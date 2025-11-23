"use client";

import { Box } from "@mui/material";
import dynamic from "next/dynamic";
import type { PropsWithChildren } from "react";

// SSRを無効にしてハイドレーション不一致を防ぐ
const Sidebar = dynamic(() => import("./sidebar").then((mod) => mod.Sidebar), {
  ssr: false,
});

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
