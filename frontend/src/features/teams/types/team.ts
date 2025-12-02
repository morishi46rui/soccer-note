import type { components } from "@/types/api";

export type Team = components["schemas"]["Team"];
export type GetTeamsResponse = components["schemas"]["GetTeamsResponse"];
export type CreateTeamRequest = components["schemas"]["CreateTeamRequest"];
export type UpdateTeamRequest = components["schemas"]["UpdateTeamRequest"];

export type GetTeamsParams = {
  page?: number;
  per_page?: number;
};

export type FormErrors = {
  name?: string;
  description?: string;
};

export type FormStatus = "idle" | "success" | "error";

export type PageParams = {
  params: Promise<{ sqid: string }>;
};

export type UpdateTeamFormProps = {
  team: Team | undefined;
  sqid: string;
};

export type CreateTeamFormValues = CreateTeamRequest;

export type UpdateTeamFormValues = UpdateTeamRequest;

export type TeamCardProps = {
  team: Team;
};

export type ErrorStateProps = {
  message: string;
};

export type EmptyStateProps = {
  message: string;
};

export type TeamPaginationProps = {
  currentPage: number;
  lastPage: number;
  total: number;
  onPageChange: (page: number) => void;
};
