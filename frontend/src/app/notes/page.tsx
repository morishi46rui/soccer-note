"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { NoteList } from "@/features/notes/components/note-list";
import { Add as AddIcon } from "@mui/icons-material";
import { Box, Button, Stack, Typography } from "@mui/material";
import Link from "next/link";

export default function NotesPage() {
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
              component={Link}
              href="/notes/new"
              variant="contained"
              startIcon={<AddIcon />}
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
