"use client";

import { useAuth } from "@/hooks/use-auth";
import type { NavItem } from "@/types/navigation";
import {
  AdminPanelSettings as AdminIcon,
  Dashboard as DashboardIcon,
  Groups as GroupsIcon,
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
} from "@mui/material";
import type { LinkProps as NextLinkProps } from "next/link";
import NextLink from "next/link";
import { usePathname, useRouter } from "next/navigation";
import type { AnchorHTMLAttributes } from "react";
import { forwardRef, useMemo, useState } from "react";

// MUIとNext.js Linkの互換性のためのラッパーコンポーネント
// MUI v6 + Next.js App Router用
type LinkProps = Omit<AnchorHTMLAttributes<HTMLAnchorElement>, "href"> &
  Omit<NextLinkProps, "as"> & {
    href: NextLinkProps["href"];
  };

const Link = forwardRef<HTMLAnchorElement, LinkProps>(
  ({ href, ...props }, ref) => <NextLink ref={ref} href={href} {...props} />
);
Link.displayName = "Link";

const SIDEBAR_WIDTH = 240;

const baseNavItems: NavItem[] = [
  {
    label: "ダッシュボード",
    path: "/dashboard",
    icon: <DashboardIcon />,
  },
  {
    label: "ノート",
    path: "/notes",
    icon: <NotesIcon />,
  },
  {
    label: "チーム",
    path: "/teams",
    icon: <GroupsIcon />,
  },
];

export const Sidebar = () => {
  const pathname = usePathname();
  const { user, logout } = useAuth();
  const router = useRouter();

  // デスクトップ用の折りたたみ状態(初期値は閉じた状態)
  const [collapsed, setCollapsed] = useState(false);

  // ユーザーがadminロールを持っているかチェック
  const isAdmin = useMemo(() => {
    return user?.roles?.some((role) => role.name === "admin") ?? false;
  }, [user]);

  // adminユーザーの場合は管理画面へのリンクを追加
  const navItems = useMemo(() => {
    return baseNavItems;
  }, []);

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
        },
      }}
    >
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
                      component={Link}
                      href={item.path}
                      selected={pathname === item.path}
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
                <ListItem
                  disablePadding
                  sx={{ display: isAdmin ? "block" : "none" }}
                >
                  <ListItemButton
                    component={Link}
                    href="/admin"
                    sx={{ mx: 1, borderRadius: 1 }}
                  >
                    <ListItemIcon sx={{ minWidth: 40 }}>
                      <AdminIcon />
                    </ListItemIcon>
                    <ListItemText primary="管理画面" />
                  </ListItemButton>
                </ListItem>
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
    </Drawer>
  );
};
