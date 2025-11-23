export type Role = {
  id: number;
  name: string;
  display_name: string;
};

export type User = {
  id: number;
  name: string;
  email: string;
  roles?: Role[];
};

export type AuthContextType = {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  setUser: (user: User, token: string) => void;
  logout: () => void;
};
