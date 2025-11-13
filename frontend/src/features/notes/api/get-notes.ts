import { apiClient } from "@/lib/api-client";
import { useQuery } from "@tanstack/react-query";
import type { GetNotesParams, GetNotesResponse } from "../types/note";

export const getNotesApi = async (
  params: GetNotesParams = {}
): Promise<GetNotesResponse> => {
  const { page = 1, per_page = 15 } = params;
  return apiClient.get<GetNotesResponse>(
    `/api/v1/notes?page=${page}&per_page=${per_page}`
  );
};

export const useGetNotes = (params: GetNotesParams = {}) => {
  return useQuery({
    queryKey: ["notes", params],
    queryFn: () => getNotesApi(params),
  });
};
