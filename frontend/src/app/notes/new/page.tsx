"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { CreateNoteForm } from "@/features/notes/components/create-note-form";
import { ArrowBack as ArrowBackIcon } from "@mui/icons-material";
import { Box, Button, Container, Stack } from "@mui/material";
import Link from "next/link";

export default function NewNotePage() {
  return (
    <DashboardLayout>
      <Box sx={{ p: 4 }}>
        <Container maxWidth="md">
          <Stack spacing={4}>
            {/* Back Button */}
            <Button
              component={Link}
              href="/notes"
              startIcon={<ArrowBackIcon />}
              variant="text"
              sx={{ alignSelf: "flex-start" }}
            >
              ノート一覧に戻る
            </Button>

            {/* Create Note Form */}
            <CreateNoteForm />
          </Stack>
        </Container>
      </Box>
    </DashboardLayout>
  );
}
