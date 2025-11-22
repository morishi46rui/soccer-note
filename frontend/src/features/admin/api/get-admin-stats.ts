import { apiClient } from "@/lib/api-client";
import type { components } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

export type AdminStatsResponse = components["schemas"]["AdminStatsResponse"];

export async function getAdminStats(): Promise<AdminStatsResponse> {
  return apiClient.get<AdminStatsResponse>("/api/v1/admin/stats");
}

export function useGetAdminStats() {
  return useQuery<AdminStatsResponse, Error>({
    queryKey: ["admin", "stats"],
    queryFn: getAdminStats,
  });
}
