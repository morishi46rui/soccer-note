"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { ArrowBack as ArrowBackIcon } from "@mui/icons-material";
import {
  Alert,
  Box,
  CircularProgress,
  Container,
  IconButton,
  Stack,
  Typography,
} from "@mui/material";
import { useRouter } from "next/navigation";
import { useGetTeam } from "../api/get-team";
import { UpdateTeamForm } from "./update-team-form";

type EditTeamPageContentProps = {
  sqid: string;
};

export const EditTeamPageContent = ({ sqid }: EditTeamPageContentProps) => {
  const router = useRouter();
  const { data: team, isLoading, error } = useGetTeam(sqid);

  if (isLoading) {
    return (
      <DashboardLayout>
        <Box display="flex" justifyContent="center" py={8}>
          <CircularProgress />
        </Box>
      </DashboardLayout>
    );
  }

  if (error) {
    return (
      <DashboardLayout>
        <Box sx={{ p: 4 }}>
          <Alert severity="error">
            チームの読み込みに失敗しました: {error.message}
          </Alert>
        </Box>
      </DashboardLayout>
    );
  }

  if (!team) {
    return (
      <DashboardLayout>
        <Box sx={{ p: 4 }}>
          <Alert severity="error">チームが見つかりませんでした</Alert>
        </Box>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout>
      <Box sx={{ p: 4 }}>
        <Container maxWidth="md">
          <Stack spacing={4}>
            {/* Header with Back Button */}
            <Box display="flex" alignItems="center" gap={2}>
              <IconButton
                onClick={() => router.push(`/teams/${sqid}`)}
                edge="start"
              >
                <ArrowBackIcon />
              </IconButton>
              <Typography variant="h5" component="h1" fontWeight="bold">
                チーム編集
              </Typography>
            </Box>

            {/* Edit Form */}
            <UpdateTeamForm team={team} sqid={sqid} />
          </Stack>
        </Container>
      </Box>
    </DashboardLayout>
  );
};
