"use client";

import { AuthContext } from "@/hooks/use-auth";
import { apiClient } from "@/lib/api-client";
import type { User } from "@/types/auth";
import type { PropsWithChildren } from "react";
import { useEffect, useState } from "react";

const getInitialAuthState = () => {
  if (typeof window === "undefined") {
    return { user: null, token: null };
  }

  const authToken = localStorage.getItem("auth_token");
  const userJson = localStorage.getItem("user");

  let user: User | null = null;
  if (userJson) {
    try {
      user = JSON.parse(userJson);
    } catch (error) {
      console.error("Failed to parse user data:", error);
    }
  }

  return { user, token: authToken };
};

export const AuthProvider = ({ children }: PropsWithChildren) => {
  const [{ user, token }, setAuthState] = useState(getInitialAuthState);

  useEffect(() => {
    if (token) {
      apiClient.setAuthToken(token);
    } else {
      apiClient.clearAuthToken();
    }
  }, [token]);

  const logout = () => {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    apiClient.clearAuthToken();
    setAuthState({ user: null, token: null });
  };

  const value = {
    user,
    token,
    isAuthenticated: !!token,
    isLoading: false,
    logout,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};
