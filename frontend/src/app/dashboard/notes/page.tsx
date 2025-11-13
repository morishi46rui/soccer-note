"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { NoteList } from "@/features/notes/components/note-list";
import { Add as AddIcon } from "@mui/icons-material";
import { Box, Button, Stack, Typography } from "@mui/material";
import { useRouter } from "next/navigation";

export default function NotesPage() {
  const router = useRouter();

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
              ノート一覧
            </Typography>
            <Button
              variant="contained"
              startIcon={<AddIcon />}
              onClick={() => router.push("/dashboard/notes/new")}
            >
              新規作成
            </Button>
          </Stack>

          {/* Note List */}
          <NoteList />
        </Stack>
      </Box>
    </DashboardLayout>
  );
}
