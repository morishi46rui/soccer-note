"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { ArrowBack as ArrowBackIcon } from "@mui/icons-material";
import { Box, Button, Container, Stack } from "@mui/material";
import Link from "next/link";
import { CreateTeamForm } from "./create-team-form";

export const NewTeamPageContent = () => {
  return (
    <DashboardLayout>
      <Box sx={{ p: 4 }}>
        <Container maxWidth="md">
          <Stack spacing={4}>
            {/* Back Button */}
            <Button
              component={Link}
              href="/teams"
              startIcon={<ArrowBackIcon />}
              variant="text"
              sx={{ alignSelf: "flex-start" }}
            >
              チーム一覧に戻る
            </Button>

            {/* Create Team Form */}
            <CreateTeamForm />
          </Stack>
        </Container>
      </Box>
    </DashboardLayout>
  );
};
