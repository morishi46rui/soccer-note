"use client";

import {
  Alert,
  Box,
  Card,
  CardActionArea,
  CardContent,
  CircularProgress,
  Pagination,
  Stack,
  Typography,
} from "@mui/material";
import Link from "next/link";
import { useState } from "react";
import { useGetNotes } from "../api/get-notes";

export function NoteList() {
  const [page, setPage] = useState(1);
  const { data, isLoading, error } = useGetNotes({ page, per_page: 15 });

  if (isLoading) {
    return (
      <Box display="flex" justifyContent="center" py={8}>
        <CircularProgress />
      </Box>
    );
  }

  if (error) {
    return (
      <Alert severity="error" sx={{ my: 2 }}>
        ノートの読み込みに失敗しました: {error.message}
      </Alert>
    );
  }

  const notes = data?.data ?? [];
  const pagination = {
    currentPage: data?.current_page ?? 1,
    lastPage: data?.last_page ?? 1,
    total: data?.total ?? 0,
  };

  if (notes.length === 0) {
    return (
      <Box py={8} textAlign="center">
        <Typography color="text.secondary">まだノートがありません</Typography>
      </Box>
    );
  }

  return (
    <Stack spacing={3}>
      <Stack spacing={2}>
        {notes.map((note) => (
          <Card key={note.id} elevation={1}>
            <CardActionArea component={Link} href={`/notes/${note.sqid}`}>
              <CardContent>
                <Stack spacing={1}>
                  <Typography variant="h6" component="h3" fontWeight="medium">
                    {note.title}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    {note.date}
                  </Typography>
                  {note.content && (
                    <Typography
                      variant="body2"
                      color="text.secondary"
                      sx={{
                        overflow: "hidden",
                        textOverflow: "ellipsis",
                        display: "-webkit-box",
                        WebkitLineClamp: 2,
                        WebkitBoxOrient: "vertical",
                      }}
                    >
                      {note.content}
                    </Typography>
                  )}
                </Stack>
              </CardContent>
            </CardActionArea>
          </Card>
        ))}
      </Stack>

      {pagination.lastPage > 1 && (
        <Box display="flex" justifyContent="center" py={2}>
          <Pagination
            count={pagination.lastPage}
            page={pagination.currentPage}
            onChange={(_, newPage) => setPage(newPage)}
            color="primary"
          />
        </Box>
      )}

      <Typography variant="caption" color="text.secondary" textAlign="center">
        全{pagination.total}件
      </Typography>
    </Stack>
  );
}
