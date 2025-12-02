import { apiClient } from "@/lib/api-client";
import { useQuery } from "@tanstack/react-query";
import type { GetTeamUsersResponse } from "../types/team-user";

export const getTeamUsersApi = async (
  sqid: string
): Promise<GetTeamUsersResponse> => {
  return apiClient.get<GetTeamUsersResponse>(`/api/v1/teams/${sqid}/users`);
};

export const useGetTeamUsers = (sqid: string) => {
  return useQuery({
    queryKey: ["teamUsers", sqid],
    queryFn: () => getTeamUsersApi(sqid),
  });
};
