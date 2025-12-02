import { apiClient } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import type {
  UpdateTeamUserRequest,
  UpdateTeamUserResponse,
} from "../types/team-user";

type UpdateTeamUserParams = {
  sqid: string;
  userId: number;
  data: UpdateTeamUserRequest;
};

export const updateTeamUserApi = async ({
  sqid,
  userId,
  data,
}: UpdateTeamUserParams): Promise<UpdateTeamUserResponse> => {
  return apiClient.put<UpdateTeamUserResponse>(
    `/api/v1/teams/${sqid}/users/${userId}`,
    data
  );
};

export const useUpdateTeamUser = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: updateTeamUserApi,
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({
        queryKey: ["teamUsers", variables.sqid],
      });
    },
  });
};
