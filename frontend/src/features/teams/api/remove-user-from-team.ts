import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";

type RemoveUserFromTeamParams = {
  sqid: string;
  userId: number;
};

export const removeUserFromTeamApi = async ({
  sqid,
  userId,
}: RemoveUserFromTeamParams): Promise<void> => {
  return apiClient.delete<void>(`/api/v1/teams/${sqid}/users/${userId}`);
};

export const useRemoveUserFromTeam = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: removeUserFromTeamApi,
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({
        queryKey: ["teamUsers", variables.sqid],
      });
    },
  });
};
