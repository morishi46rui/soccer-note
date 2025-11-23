import { apiClient } from "@/lib/api-client";
import { useQuery } from "@tanstack/react-query";
import type { Team } from "../types/team";

export const getTeamApi = async (sqid: string): Promise<Team> => {
  return apiClient.get<Team>(`/api/v1/teams/${sqid}`);
};

export const useGetTeam = (sqid: string) => {
  return useQuery({
    queryKey: ["teams", sqid],
    queryFn: () => getTeamApi(sqid),
    enabled: !!sqid,
  });
};
