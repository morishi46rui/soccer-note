import { apiClient } from "@/lib/api-client";
import { useQuery } from "@tanstack/react-query";
import type { Note } from "../types/note";

export const getNoteApi = async (sqid: string): Promise<Note> => {
  return apiClient.get<Note>(`/api/v1/notes/${sqid}`);
};

export const useGetNote = (sqid: string) => {
  return useQuery({
    queryKey: ["notes", sqid],
    queryFn: () => getNoteApi(sqid),
    enabled: !!sqid,
  });
};
