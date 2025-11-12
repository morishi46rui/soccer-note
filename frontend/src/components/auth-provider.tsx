"use client";

import { AuthContext } from "@/hooks/use-auth";
import type { User } from "@/types/auth";
import type { PropsWithChildren } from "react";
import { useEffect, useState } from "react";

export function AuthProvider({ children }: PropsWithChildren) {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const authToken = localStorage.getItem("auth_token");
    const userJson = localStorage.getItem("user");

    if (authToken) {
      setToken(authToken);
    }

    if (userJson) {
      try {
        setUser(JSON.parse(userJson));
      } catch (error) {
        console.error("Failed to parse user data:", error);
      }
    }

    setIsLoading(false);
  }, []);

  const logout = () => {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    setToken(null);
    setUser(null);
  };

  const value = {
    user,
    token,
    isAuthenticated: !!token,
    isLoading,
    logout,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}
