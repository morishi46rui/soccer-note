import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";

export const deleteNoteApi = async (sqid: string): Promise<void> => {
  return apiClient.delete(`/api/v1/notes/${sqid}`);
};

export const useDeleteNote = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (sqid: string) => deleteNoteApi(sqid),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["notes"] });
    },
  });
};
