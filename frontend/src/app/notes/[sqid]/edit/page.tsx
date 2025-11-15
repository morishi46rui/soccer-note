"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { useGetNote } from "@/features/notes/api/get-note";
import { UpdateNoteForm } from "@/features/notes/components/update-note-form";
import type { PageParams } from "@/features/notes/types/note";
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
import { use } from "react";

const EditNotePage = ({ params }: PageParams) => {
  const router = useRouter();
  const { sqid } = use(params);
  const { data: note, isLoading, error } = useGetNote(sqid);

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
            ノートの読み込みに失敗しました: {error.message}
          </Alert>
        </Box>
      </DashboardLayout>
    );
  }

  if (!note) {
    return (
      <DashboardLayout>
        <Box sx={{ p: 4 }}>
          <Alert severity="error">ノートが見つかりませんでした</Alert>
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
                onClick={() => router.push(`/notes/${sqid}`)}
                edge="start"
              >
                <ArrowBackIcon />
              </IconButton>
              <Typography variant="h5" component="h1" fontWeight="bold">
                ノート編集
              </Typography>
            </Box>

            {/* Edit Form */}
            <UpdateNoteForm note={note} sqid={sqid} />
          </Stack>
        </Container>
      </Box>
    </DashboardLayout>
  );
};

export default EditNotePage;
