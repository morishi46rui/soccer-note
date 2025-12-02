import { TeamUsersPageContent } from "@/features/teams/components/team-users-page-content";
import type { TeamUserPageParams } from "@/features/teams/types/team-user";

export const TeamUsersPage = async ({ params }: TeamUserPageParams) => {
  const { sqid } = await params;

  return <TeamUsersPageContent sqid={sqid} />;
};

export default TeamUsersPage;
