import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";

export const deleteTeamApi = async (sqid: string): Promise<void> => {
  return apiClient.delete(`/api/v1/teams/${sqid}`);
};

export const useDeleteTeam = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (sqid: string) => deleteTeamApi(sqid),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["teams"] });
    },
  });
};
