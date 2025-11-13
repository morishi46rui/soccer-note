"use client";

import { Menu as MenuIcon } from "@mui/icons-material";
import {
  AppBar,
  Box,
  IconButton,
  Toolbar,
  useMediaQuery,
  useTheme,
} from "@mui/material";
import type { PropsWithChildren } from "react";
import { useEffect, useState } from "react";
import { Sidebar } from "./sidebar";

export const DashboardLayout = ({ children }: PropsWithChildren) => {
  const theme = useTheme();
  const isMobileQuery = useMediaQuery(theme.breakpoints.down("md"));
  const [isMobile, setIsMobile] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);

  // Hydration Mismatch を防ぐため、クライアント側でマウント後に値を設定
  useEffect(() => {
    setIsMobile(isMobileQuery);
  }, [isMobileQuery]);

  const handleDrawerToggle = () => {
    setMobileOpen(!mobileOpen);
  };

  return (
    <Box sx={{ display: "flex", minHeight: "100vh" }}>
      {/* モバイル用のAppBar */}
      {isMobile && (
        <AppBar
          position="fixed"
          sx={{
            zIndex: theme.zIndex.drawer + 1,
            backgroundColor: "background.paper",
            color: "text.primary",
            boxShadow: 1,
          }}
        >
          <Toolbar>
            <IconButton
              color="inherit"
              aria-label="open drawer"
              edge="start"
              onClick={handleDrawerToggle}
              sx={{ mr: 2 }}
            >
              <MenuIcon />
            </IconButton>
          </Toolbar>
        </AppBar>
      )}

      <Sidebar mobileOpen={mobileOpen} onMobileClose={handleDrawerToggle} />

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          backgroundColor: "background.default",
          pt: isMobile ? 8 : 0, // モバイルの場合はAppBarの高さ分paddingを追加
        }}
      >
        {children}
      </Box>
    </Box>
  );
};
