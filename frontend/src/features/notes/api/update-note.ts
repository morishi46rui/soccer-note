import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import type { Note, UpdateNoteRequest } from "../types/note";

export const updateNoteApi = async (
  sqid: string,
  data: UpdateNoteRequest
): Promise<Note> => {
  return apiClient.put<Note>(`/api/v1/notes/${sqid}`, data);
};

export const useUpdateNote = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ sqid, data }: { sqid: string; data: UpdateNoteRequest }) =>
      updateNoteApi(sqid, data),
    onSuccess: (updatedNote) => {
      queryClient.invalidateQueries({ queryKey: ["notes"] });
      queryClient.setQueryData(["notes", updatedNote.sqid], updatedNote);
    },
  });
};
