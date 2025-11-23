"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { Add as AddIcon } from "@mui/icons-material";
import { Box, Button, Stack, Typography } from "@mui/material";
import Link from "next/link";
import { TeamList } from "./team-list";

export const TeamsPageContent = () => {
  return (
    <DashboardLayout>
      <Box sx={{ p: 4 }}>
        <Stack spacing={4}>
          {/* Header */}
          <Stack
            direction="row"
            justifyContent="space-between"
            alignItems="center"
          >
            <Typography variant="h4" component="h1" fontWeight="bold">
              チーム一覧
            </Typography>
            <Button
              component={Link}
              href="/teams/new"
              variant="contained"
              startIcon={<AddIcon />}
            >
              新規作成
            </Button>
          </Stack>

          {/* Team List */}
          <TeamList />
        </Stack>
      </Box>
    </DashboardLayout>
  );
};
