"use client";

import { TeamDetail } from "@/features/teams/components/team-detail";
import type { PageParams } from "@/features/teams/types/team";
import { use } from "react";

const TeamPage = ({ params }: PageParams) => {
  const { sqid } = use(params);
  return <TeamDetail sqid={sqid} />;
};

export default TeamPage;
