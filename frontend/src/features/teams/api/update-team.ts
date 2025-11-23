import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import type { Team, UpdateTeamRequest } from "../types/team";

export const updateTeamApi = async (
  sqid: string,
  data: UpdateTeamRequest
): Promise<Team> => {
  return apiClient.put<Team>(`/api/v1/teams/${sqid}`, data);
};

export const useUpdateTeam = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ sqid, data }: { sqid: string; data: UpdateTeamRequest }) =>
      updateTeamApi(sqid, data),
    onSuccess: (updatedTeam) => {
      queryClient.invalidateQueries({ queryKey: ["teams"] });
      queryClient.setQueryData(["teams", updatedTeam.sqid], updatedTeam);
    },
  });
};
