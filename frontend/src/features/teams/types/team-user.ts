import type { components } from "@/types/api";

export type TeamUser = components["schemas"]["TeamUserItem"];
export type GetTeamUsersResponse = components["schemas"]["GetTeamUsersResponse"];
export type AddUserToTeamRequest = components["schemas"]["AddUserToTeamRequest"];
export type AddUserToTeamResponse = components["schemas"]["AddUserToTeamResponse"];
export type UpdateTeamUserRequest = components["schemas"]["UpdateTeamUserRequest"];
export type UpdateTeamUserResponse = components["schemas"]["UpdateTeamUserResponse"];

export type AddUserToTeamFormValues = AddUserToTeamRequest;

export type TeamUserPageParams = {
  params: Promise<{ sqid: string }>;
};

export type TeamUserListProps = {
  sqid: string;
};

export type AddUserDialogProps = {
  open: boolean;
  onClose: () => void;
  sqid: string;
  onSuccess: () => void;
};

export type EditUserDialogProps = {
  open: boolean;
  onClose: () => void;
  sqid: string;
  user: TeamUser;
  onSuccess: () => void;
};

export type DeleteUserDialogProps = {
  open: boolean;
  onClose: () => void;
  sqid: string;
  userId: number;
  userName: string;
  onSuccess: () => void;
};
