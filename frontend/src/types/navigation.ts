export type NavItem = {
  label: string;
  path: string;
  icon: React.ReactNode;
};

export type SidebarProps = {
  mobileOpen?: boolean;
  onMobileClose?: () => void;
};
