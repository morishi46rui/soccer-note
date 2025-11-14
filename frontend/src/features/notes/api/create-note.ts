import { apiClient } from "@/lib/api-client";
import type { components } from "@/types/api";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import type { CreateNoteRequest } from "../types/note";

type CreateNoteResponse = components["schemas"]["CreateNoteResponse"];

export const createNoteApi = async (
  data: CreateNoteRequest
): Promise<CreateNoteResponse> => {
  return apiClient.post<CreateNoteResponse>("/api/v1/notes", data);
};

export const useCreateNote = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: createNoteApi,
    onSuccess: () => {
      // ノート一覧のキャッシュを無効化して再取得
      queryClient.invalidateQueries({ queryKey: ["notes"] });
    },
  });
};
