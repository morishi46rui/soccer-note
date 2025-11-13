"use client";

import { useAuth } from "@/hooks/use-auth";
import type { NavItem, SidebarProps } from "@/types/navigation";
import {
  Dashboard as DashboardIcon,
  Logout as LogoutIcon,
  Notes as NotesIcon,
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
  useMediaQuery,
  useTheme,
} from "@mui/material";
import { usePathname, useRouter } from "next/navigation";
import { useState } from "react";

const SIDEBAR_WIDTH = 240;

const navItems: NavItem[] = [
  {
    label: "ダッシュボード",
    path: "/dashboard",
    icon: <DashboardIcon />,
  },
  {
    label: "ノート",
    path: "/dashboard/notes",
    icon: <NotesIcon />,
  },
];

export const Sidebar = ({ mobileOpen, onMobileClose }: SidebarProps) => {
  const router = useRouter();
  const pathname = usePathname();
  const { user, logout } = useAuth();
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down("md"));

  // デスクトップ用の折りたたみ状態(初期値は閉じた状態)
  const [collapsed, setCollapsed] = useState(false);

  const handleToggleCollapse = () => {
    setCollapsed(!collapsed);
  };

  const handleLogout = () => {
    logout();
    router.push("/");
  };

  // モバイル用のサイドバーコンテンツ
  const mobileDrawerContent = (
    <Box sx={{ overflow: "auto", height: "100%" }}>
      <Stack sx={{ height: "100%" }}>
        {/* Header */}
        <Box sx={{ p: 2, minHeight: 64 }}>
          <Typography variant="h6" fontWeight="bold">
            Soccer Note
          </Typography>
          <Typography variant="caption" color="text.secondary">
            {user?.name}
          </Typography>
        </Box>

        <Divider />

        {/* Navigation */}
        <List sx={{ flex: 1, pt: 1 }}>
          {navItems.map((item) => (
            <ListItem key={item.path} disablePadding>
              <ListItemButton
                selected={pathname === item.path}
                onClick={() => {
                  router.push(item.path);
                  onMobileClose?.();
                }}
                sx={{
                  mx: 1,
                  borderRadius: 1,
                  "&.Mui-selected": {
                    backgroundColor: "action.selected",
                    "&:hover": {
                      backgroundColor: "action.selected",
                    },
                  },
                }}
              >
                <ListItemIcon sx={{ minWidth: 40 }}>{item.icon}</ListItemIcon>
                <ListItemText primary={item.label} />
              </ListItemButton>
            </ListItem>
          ))}
        </List>

        <Divider />

        {/* Footer */}
        <List>
          <ListItem disablePadding>
            <ListItemButton
              onClick={handleLogout}
              sx={{ mx: 1, borderRadius: 1 }}
            >
              <ListItemIcon sx={{ minWidth: 40 }}>
                <LogoutIcon />
              </ListItemIcon>
              <ListItemText primary="ログアウト" />
            </ListItemButton>
          </ListItem>
        </List>
      </Stack>
    </Box>
  );

  // デスクトップ用のサイドバーコンテンツ
  const desktopDrawerContent = (
    <Box sx={{ overflow: "auto", height: "100%" }}>
      <Stack sx={{ height: "100%" }}>
        {/* 開閉ボタン(常に左上固定) */}
        <Box sx={{ p: 2 }}>
          <IconButton onClick={handleToggleCollapse}>
            <NotesIcon />
          </IconButton>
        </Box>

        {/* 展開時のみ表示 */}
        {!collapsed && (
          <>
            {/* ユーザー情報 */}
            <Box sx={{ p: 2 }}>
              <Typography variant="h6" fontWeight="bold">
                Soccer Note
              </Typography>
            </Box>

            <Divider />

            {/* Navigation */}
            <List sx={{ flex: 1, pt: 1 }}>
              {navItems.map((item) => (
                <ListItem key={item.path} disablePadding>
                  <ListItemButton
                    selected={pathname === item.path}
                    onClick={() => router.push(item.path)}
                    sx={{
                      mx: 1,
                      borderRadius: 1,
                      "&.Mui-selected": {
                        backgroundColor: "action.selected",
                        "&:hover": {
                          backgroundColor: "action.selected",
                        },
                      },
                    }}
                  >
                    <ListItemIcon sx={{ minWidth: 40 }}>
                      {item.icon}
                    </ListItemIcon>
                    <ListItemText primary={item.label} />
                  </ListItemButton>
                </ListItem>
              ))}
            </List>

            <Divider />

            {/* Footer */}
            <List>
              <ListItem disablePadding>
                <ListItemButton
                  onClick={handleLogout}
                  sx={{ mx: 1, borderRadius: 1 }}
                >
                  <ListItemIcon sx={{ minWidth: 40 }}>
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
  );

  return (
    <>
      {/* モバイル用: 一時的なDrawer */}
      {isMobile ? (
        <Drawer
          variant="temporary"
          open={mobileOpen}
          onClose={onMobileClose}
          ModalProps={{
            keepMounted: true,
          }}
          sx={{
            display: { xs: "block", md: "none" },
            "& .MuiDrawer-paper": {
              width: SIDEBAR_WIDTH,
              boxSizing: "border-box",
              borderRight: "1px solid",
              borderColor: "divider",
            },
          }}
        >
          {mobileDrawerContent}
        </Drawer>
      ) : (
        // デスクトップ用: 常時表示のDrawer
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
            },
          }}
        >
          {desktopDrawerContent}
        </Drawer>
      )}
    </>
  );
};
