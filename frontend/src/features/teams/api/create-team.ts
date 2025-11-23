import { apiClient } from "@/lib/api-client";
import type { components } from "@/types/api";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import type { CreateTeamRequest } from "../types/team";

type CreateTeamResponse = components["schemas"]["CreateTeamResponse"];

export const createTeamApi = async (
  data: CreateTeamRequest
): Promise<CreateTeamResponse> => {
  return apiClient.post<CreateTeamResponse>("/api/v1/teams", data);
};

export const useCreateTeam = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: createTeamApi,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["teams"] });
    },
  });
};
