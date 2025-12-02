"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import { useSnackbar } from "@/hooks/use-snackbar";
import ArrowBackIcon from "@mui/icons-material/ArrowBack";
import DeleteIcon from "@mui/icons-material/Delete";
import EditIcon from "@mui/icons-material/Edit";
import Alert from "@mui/material/Alert";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import CircularProgress from "@mui/material/CircularProgress";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import Divider from "@mui/material/Divider";
import IconButton from "@mui/material/IconButton";
import Stack from "@mui/material/Stack";
import Typography from "@mui/material/Typography";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useDeleteTeam } from "../api/delete-team";
import { useGetTeam } from "../api/get-team";
import { TeamUserList } from "./team-user-list";

type TeamDetailProps = {
  sqid: string;
};

export const TeamDetail = ({ sqid }: TeamDetailProps) => {
  const router = useRouter();
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const { data: team, isLoading, error } = useGetTeam(sqid);
  const deleteTeam = useDeleteTeam();
  const { showSnackbar } = useSnackbar();

  const handleDelete = async () => {
    try {
      await deleteTeam.mutateAsync(sqid);
      showSnackbar("チームを削除しました!", "success");
      router.push("/teams");
    } catch (error) {
      console.error("Failed to delete team:", error);
      showSnackbar("チームの削除に失敗しました", "error");
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
        <Stack spacing={3}>
          {/* Header with Back Button */}
          <Box display="flex" alignItems="center" gap={2}>
            <IconButton onClick={() => router.push("/teams")} edge="start">
              <ArrowBackIcon />
            </IconButton>
            <Typography variant="h5" component="h1" fontWeight="bold" flex={1}>
              チーム詳細
            </Typography>
            <Button
              variant="outlined"
              startIcon={<EditIcon />}
              onClick={() => router.push(`/teams/${sqid}/edit`)}
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

          {/* Team Content */}
          <Card elevation={1}>
            <CardContent>
              <Stack spacing={3}>
                <Box>
                  <Typography
                    variant="overline"
                    color="text.secondary"
                    display="block"
                  >
                    チーム名
                  </Typography>
                  <Typography variant="h5" fontWeight="medium">
                    {team.name}
                  </Typography>
                </Box>

                <Divider />

                <Box>
                  <Typography
                    variant="overline"
                    color="text.secondary"
                    display="block"
                    gutterBottom
                  >
                    説明
                  </Typography>
                  {team.description ? (
                    <Typography
                      variant="body1"
                      sx={{
                        whiteSpace: "pre-wrap",
                        wordBreak: "break-word",
                      }}
                    >
                      {team.description}
                    </Typography>
                  ) : (
                    <Typography variant="body2" color="text.secondary">
                      説明が記載されていません
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
                    {team.created_at
                      ? new Date(team.created_at).toLocaleString("ja-JP")
                      : "不明"}
                  </Typography>
                  <Typography
                    variant="caption"
                    color="text.secondary"
                    display="block"
                  >
                    更新日時:{" "}
                    {team.updated_at
                      ? new Date(team.updated_at).toLocaleString("ja-JP")
                      : "不明"}
                  </Typography>
                </Box>
              </Stack>
            </CardContent>
          </Card>

          {/* Team Members */}
          <TeamUserList sqid={sqid} />
        </Stack>
      </Box>

      {/* Delete Confirmation Dialog */}
      <Dialog
        open={deleteDialogOpen}
        onClose={() => setDeleteDialogOpen(false)}
      >
        <DialogTitle>チームを削除しますか?</DialogTitle>
        <DialogContent>
          <DialogContentText>
            この操作は取り消せません。本当に「{team.name}」を削除しますか?
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteDialogOpen(false)}>キャンセル</Button>
          <Button
            onClick={handleDelete}
            color="error"
            variant="contained"
            disabled={deleteTeam.isPending}
          >
            {deleteTeam.isPending ? "削除中..." : "削除"}
          </Button>
        </DialogActions>
      </Dialog>
    </DashboardLayout>
  );
};
