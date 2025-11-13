"use client";

import { NoteList } from "@/features/notes/components/note-list";
import { Button, Container, Paper, Stack, Typography } from "@mui/material";
import { useRouter } from "next/navigation";

export default function NotesPage() {
  const router = useRouter();

  return (
    <Container maxWidth="lg" sx={{ py: 8 }}>
      <Stack spacing={4}>
        <Paper sx={{ p: 4 }}>
          <Stack spacing={3}>
            <Stack
              direction="row"
              justifyContent="space-between"
              alignItems="center"
            >
              <Typography variant="h4" component="h1" fontWeight="bold">
                ノート一覧
              </Typography>
              <Button
                variant="outlined"
                onClick={() => router.push("/dashboard")}
              >
                ダッシュボードに戻る
              </Button>
            </Stack>

            <NoteList />
          </Stack>
        </Paper>
      </Stack>
    </Container>
  );
}
