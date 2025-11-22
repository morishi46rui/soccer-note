"use client";

import { Groups, Notes, Person, SportsSoccer } from "@mui/icons-material";
import { Box } from "@mui/material";
import { StatsCardsProps } from "../types/stats-card";
import { StatsCard } from "./stats-card";

export const StatsCards = ({ data }: StatsCardsProps) => {
  return (
    <Box
      sx={{
        display: "grid",
        gridTemplateColumns: {
          xs: "1fr",
          sm: "repeat(2, 1fr)",
          md: "repeat(4, 1fr)",
        },
        gap: 3,
      }}
    >
      <StatsCard
        icon={<Person color="primary" />}
        title="ユーザー"
        value={data?.total_users ?? 0}
        caption="登録ユーザー数"
        subText={`アクティブ: ${data?.active_users ?? 0}`}
      />
      <StatsCard
        icon={<SportsSoccer color="success" />}
        title="チーム"
        value={data?.total_teams ?? 0}
        caption="登録チーム数"
      />
      <StatsCard
        icon={<Groups color="warning" />}
        title="グループ"
        value={data?.total_groups ?? 0}
        caption="登録グループ数"
      />
      <StatsCard
        icon={<Notes color="info" />}
        title="ノート"
        value={data?.total_posts ?? 0}
        caption="作成ノート数"
      />
    </Box>
  );
};
