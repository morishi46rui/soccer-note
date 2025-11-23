import { apiClient } from "@/lib/api-client";
import { useQuery } from "@tanstack/react-query";
import type { GetTeamsParams, GetTeamsResponse } from "../types/team";

export const getTeamsApi = async (
  params: GetTeamsParams = {}
): Promise<GetTeamsResponse> => {
  const { page = 1, per_page = 15 } = params;
  return apiClient.get<GetTeamsResponse>(
    `/api/v1/teams?page=${page}&per_page=${per_page}`
  );
};

export const useGetTeams = (params: GetTeamsParams = {}) => {
  return useQuery({
    queryKey: ["teams", params],
    queryFn: () => getTeamsApi(params),
  });
};
