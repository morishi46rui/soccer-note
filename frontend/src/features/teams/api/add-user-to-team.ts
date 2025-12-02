import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import type {
  AddUserToTeamRequest,
  AddUserToTeamResponse,
} from "../types/team-user";

type AddUserToTeamParams = {
  sqid: string;
  data: AddUserToTeamRequest;
};

export const addUserToTeamApi = async ({
  sqid,
  data,
}: AddUserToTeamParams): Promise<AddUserToTeamResponse> => {
  return apiClient.post<AddUserToTeamResponse>(
    `/api/v1/teams/${sqid}/users`,
    data
  );
};

export const useAddUserToTeam = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: addUserToTeamApi,
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({
        queryKey: ["teamUsers", variables.sqid],
      });
    },
  });
};
