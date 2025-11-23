"use client";

import { EditTeamPageContent } from "@/features/teams/components/edit-team-page-content";
import type { PageParams } from "@/features/teams/types/team";
import { use } from "react";

const EditTeamPage = ({ params }: PageParams) => {
  const { sqid } = use(params);
  return <EditTeamPageContent sqid={sqid} />;
};

export default EditTeamPage;
