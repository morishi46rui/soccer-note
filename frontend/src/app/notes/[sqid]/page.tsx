"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { useDeleteNote } from "@/features/notes/api/delete-note";
import { useGetNote } from "@/features/notes/api/get-note";
import type { PageParams } from "@/features/notes/types/note";
import { useSnackbar } from "@/hooks/use-snackbar";
import {
  ArrowBack as ArrowBackIcon,
  Delete as DeleteIcon,
  Edit as EditIcon,
} from "@mui/icons-material";
import {
  Alert,
  Box,
  Button,
  Card,
  CardContent,
  CircularProgress,
  Dialog,
  DialogActions,
  DialogContent,
  DialogContentText,
  DialogTitle,
  Divider,
  IconButton,
  Stack,
  Typography,
} from "@mui/material";
import { useRouter } from "next/navigation";
import { use, useState } from "react";

const NotePage = ({ params }: PageParams) => {
  const router = useRouter();
  const { sqid } = use(params);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const { data: note, isLoading, error } = useGetNote(sqid);
  const deleteNote = useDeleteNote();
  const { showSnackbar } = useSnackbar();

  const handleDelete = async () => {
    try {
      await deleteNote.mutateAsync(sqid);
      showSnackbar("ノートを削除しました!", "success");
      router.push("/notes");
    } catch (error) {
      console.error("Failed to delete note:", error);
      showSnackbar("ノートの削除に失敗しました", "error");
    }
  };

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
        <Stack spacing={3}>
          {/* Header with Back Button */}
          <Box display="flex" alignItems="center" gap={2}>
            <IconButton onClick={() => router.push("/notes")} edge="start">
              <ArrowBackIcon />
            </IconButton>
            <Typography variant="h5" component="h1" fontWeight="bold" flex={1}>
              ノート詳細
            </Typography>
            <Button
              variant="outlined"
              startIcon={<EditIcon />}
              onClick={() => router.push(`/notes/${sqid}/edit`)}
            >
              編集
            </Button>
            <Button
              variant="outlined"
              color="error"
              startIcon={<DeleteIcon />}
              onClick={() => setDeleteDialogOpen(true)}
            >
              削除
            </Button>
          </Box>

          {/* Note Content */}
          <Card elevation={1}>
            <CardContent>
              <Stack spacing={3}>
                <Box>
                  <Typography
                    variant="overline"
                    color="text.secondary"
                    display="block"
                  >
                    タイトル
                  </Typography>
                  <Typography variant="h5" fontWeight="medium">
                    {note.title}
                  </Typography>
                </Box>

                <Divider />

                <Box>
                  <Typography
                    variant="overline"
                    color="text.secondary"
                    display="block"
                  >
                    日付
                  </Typography>
                  <Typography variant="body1">{note.date}</Typography>
                </Box>

                <Divider />

                <Box>
                  <Typography
                    variant="overline"
                    color="text.secondary"
                    display="block"
                    gutterBottom
                  >
                    内容
                  </Typography>
                  {note.content ? (
                    <Typography
                      variant="body1"
                      sx={{
                        whiteSpace: "pre-wrap",
                        wordBreak: "break-word",
                      }}
                    >
                      {note.content}
                    </Typography>
                  ) : (
                    <Typography variant="body2" color="text.secondary">
                      内容が記載されていません
                    </Typography>
                  )}
                </Box>

                <Divider />

                <Box>
                  <Typography
                    variant="caption"
                    color="text.secondary"
                    display="block"
                  >
                    作成日時:{" "}
                    {note.created_at
                      ? new Date(note.created_at).toLocaleString("ja-JP")
                      : "不明"}
                  </Typography>
                  <Typography
                    variant="caption"
                    color="text.secondary"
                    display="block"
                  >
                    更新日時:{" "}
                    {note.updated_at
                      ? new Date(note.updated_at).toLocaleString("ja-JP")
                      : "不明"}
                  </Typography>
                </Box>
              </Stack>
            </CardContent>
          </Card>
        </Stack>
      </Box>

      {/* Delete Confirmation Dialog */}
      <Dialog
        open={deleteDialogOpen}
        onClose={() => setDeleteDialogOpen(false)}
      >
        <DialogTitle>ノートを削除しますか?</DialogTitle>
        <DialogContent>
          <DialogContentText>
            この操作は取り消せません。本当に「{note.title}」を削除しますか?
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteDialogOpen(false)}>キャンセル</Button>
          <Button
            onClick={handleDelete}
            color="error"
            variant="contained"
            disabled={deleteNote.isPending}
          >
            {deleteNote.isPending ? "削除中..." : "削除"}
          </Button>
        </DialogActions>
      </Dialog>
    </DashboardLayout>
  );
};

export default NotePage;
