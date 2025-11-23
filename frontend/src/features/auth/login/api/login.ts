import { apiClient, ApiError } from "@/lib/api-client";
import type { components } from "@/types/api";
import { useMutation } from "@tanstack/react-query";

export type LoginRequest = components["schemas"]["LoginRequest"];
export type LoginResponse = components["schemas"]["LoginResponse"];

export async function loginApi(data: LoginRequest): Promise<LoginResponse> {
  return apiClient.post<LoginResponse>("/api/v1/auth/login", data);
}

export function useLoginMutation() {
  return useMutation<LoginResponse, Error, LoginRequest>({
    mutationFn: loginApi,
    onError: (error) => {
      console.error("Login error:", error);
      if (error instanceof ApiError) {
        console.error("API Error details:", error.data);
      }
    },
  });
}
