"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import ArrowBackIcon from "@mui/icons-material/ArrowBack";
import Box from "@mui/material/Box";
import IconButton from "@mui/material/IconButton";
import Stack from "@mui/material/Stack";
import Typography from "@mui/material/Typography";
import { useRouter } from "next/navigation";
import { TeamUserList } from "./team-user-list";

type TeamUsersPageContentProps = {
  sqid: string;
};

export const TeamUsersPageContent = ({ sqid }: TeamUsersPageContentProps) => {
  const router = useRouter();

  return (
    <DashboardLayout>
      <Box sx={{ p: 4 }}>
        <Stack spacing={3}>
          {/* Header with Back Button */}
          <Box display="flex" alignItems="center" gap={2}>
            <IconButton onClick={() => router.push(`/teams/${sqid}`)} edge="start">
              <ArrowBackIcon />
            </IconButton>
            <Typography variant="h5" component="h1" fontWeight="bold" flex={1}>
              チームメンバー管理
            </Typography>
          </Box>

          {/* Team User List */}
          <TeamUserList sqid={sqid} />
        </Stack>
      </Box>
    </DashboardLayout>
  );
};
