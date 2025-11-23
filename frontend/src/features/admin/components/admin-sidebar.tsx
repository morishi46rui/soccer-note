"use client";

import type { AdminNavItem } from "@/features/admin/types/navigation";
import { useAuth } from "@/hooks/use-auth";
import {
  Dashboard as DashboardIcon,
  Home as HomeIcon,
  Logout as LogoutIcon,
  People as PeopleIcon,
  Security as SecurityIcon,
  Settings as SettingsIcon,
} from "@mui/icons-material";
import {
  Box,
  Divider,
  Drawer,
  IconButton,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Stack,
  Typography,
} from "@mui/material";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useState } from "react";

const SIDEBAR_WIDTH = 240;

const navItems: AdminNavItem[] = [
  {
    label: "ダッシュボード",
    path: "/admin",
    icon: <DashboardIcon />,
  },
  {
    label: "ユーザー管理",
    path: "/admin/users",
    icon: <PeopleIcon />,
  },
  {
    label: "ロール・権限",
    path: "/admin/roles",
    icon: <SecurityIcon />,
  },
  {
    label: "システム設定",
    path: "/admin/settings",
    icon: <SettingsIcon />,
  },
];

export const AdminSidebar = () => {
  const pathname = usePathname();
  const { logout } = useAuth();
  const router = useRouter();
  const [collapsed, setCollapsed] = useState(false);

  const handleToggleCollapse = () => {
    setCollapsed(!collapsed);
  };

  const handleLogout = () => {
    logout();
    router.push("/");
  };

  return (
    <Drawer
      variant="permanent"
      sx={{
        width: collapsed ? 56 : SIDEBAR_WIDTH,
        flexShrink: 0,
        "& .MuiDrawer-paper": {
          width: collapsed ? 56 : SIDEBAR_WIDTH,
          boxSizing: "border-box",
          borderRight: collapsed ? "none" : "1px solid",
          borderColor: "divider",
          overflowX: "hidden",
          backgroundColor: collapsed ? "grey.50" : "grey.900",
          color: "white",
        },
      }}
    >
      <Box sx={{ overflow: "auto", height: "100%" }}>
        <Stack sx={{ height: "100%" }}>
          {/* 開閉ボタン */}
          <Box sx={{ p: 2 }}>
            <IconButton
              onClick={handleToggleCollapse}
              sx={{ color: "primary.main" }}
            >
              <SettingsIcon />
            </IconButton>
          </Box>

          {/* 展開時のみ表示 */}
          {!collapsed && (
            <>
              {/* タイトル */}
              <Box sx={{ p: 2 }}>
                <Typography variant="h6" fontWeight="bold">
                  システム管理
                </Typography>
                <Typography variant="caption" color="grey.400">
                  Admin Panel
                </Typography>
              </Box>

              <Divider sx={{ borderColor: "grey.700" }} />

              {/* Navigation */}
              <List sx={{ flex: 1, pt: 1 }}>
                {navItems.map((item) => (
                  <ListItem key={item.path} disablePadding>
                    <ListItemButton
                      component={Link}
                      href={item.path}
                      selected={pathname === item.path}
                      sx={{
                        mx: 1,
                        borderRadius: 1,
                        color: "grey.300",
                        "&.Mui-selected": {
                          backgroundColor: "primary.main",
                          color: "white",
                          "&:hover": {
                            backgroundColor: "primary.dark",
                          },
                        },
                        "&:hover": {
                          backgroundColor: "grey.800",
                        },
                      }}
                    >
                      <ListItemIcon sx={{ minWidth: 40, color: "inherit" }}>
                        {item.icon}
                      </ListItemIcon>
                      <ListItemText primary={item.label} />
                    </ListItemButton>
                  </ListItem>
                ))}
              </List>

              <Divider sx={{ borderColor: "grey.700" }} />

              {/* Footer */}
              <List>
                <ListItem disablePadding>
                  <ListItemButton
                    component={Link}
                    href="/dashboard"
                    sx={{
                      mx: 1,
                      borderRadius: 1,
                      color: "grey.300",
                      "&:hover": {
                        backgroundColor: "grey.800",
                      },
                    }}
                  >
                    <ListItemIcon sx={{ minWidth: 40, color: "inherit" }}>
                      <HomeIcon />
                    </ListItemIcon>
                    <ListItemText primary="ユーザー画面へ" />
                  </ListItemButton>
                </ListItem>
                <ListItem disablePadding>
                  <ListItemButton
                    onClick={handleLogout}
                    sx={{
                      mx: 1,
                      borderRadius: 1,
                      color: "grey.300",
                      "&:hover": {
                        backgroundColor: "grey.800",
                      },
                    }}
                  >
                    <ListItemIcon sx={{ minWidth: 40, color: "inherit" }}>
                      <LogoutIcon />
                    </ListItemIcon>
                    <ListItemText primary="ログアウト" />
                  </ListItemButton>
                </ListItem>
              </List>
            </>
          )}
        </Stack>
      </Box>
    </Drawer>
  );
};
