"use client";

import { useSnackbar } from "@/hooks/use-snackbar";
import DeleteIcon from "@mui/icons-material/Delete";
import EditIcon from "@mui/icons-material/Edit";
import PersonAddIcon from "@mui/icons-material/PersonAdd";
import StarIcon from "@mui/icons-material/Star";
import Alert from "@mui/material/Alert";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import Checkbox from "@mui/material/Checkbox";
import Chip from "@mui/material/Chip";
import CircularProgress from "@mui/material/CircularProgress";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import FormControlLabel from "@mui/material/FormControlLabel";
import IconButton from "@mui/material/IconButton";
import Paper from "@mui/material/Paper";
import Stack from "@mui/material/Stack";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import TextField from "@mui/material/TextField";
import Typography from "@mui/material/Typography";
import { useState } from "react";
import { useAddUserToTeam } from "../api/add-user-to-team";
import { useGetTeamUsers } from "../api/get-team-users";
import { useRemoveUserFromTeam } from "../api/remove-user-from-team";
import { useUpdateTeamUser } from "../api/update-team-user";
import type { TeamUser, TeamUserListProps } from "../types/team-user";

export const TeamUserList = ({ sqid }: TeamUserListProps) => {
  const { data, isLoading, error } = useGetTeamUsers(sqid);
  const addUserMutation = useAddUserToTeam();
  const updateUserMutation = useUpdateTeamUser();
  const removeUserMutation = useRemoveUserFromTeam();
  const { showSnackbar } = useSnackbar();

  // Add User Dialog State
  const [addDialogOpen, setAddDialogOpen] = useState(false);
  const [newUserEmail, setNewUserEmail] = useState("");
  const [isOwner, setIsOwner] = useState(false);

  // Edit User Dialog State
  const [editDialogOpen, setEditDialogOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<TeamUser | null>(null);
  const [editIsOwner, setEditIsOwner] = useState(false);

  // Delete User Dialog State
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [deletingUser, setDeletingUser] = useState<TeamUser | null>(null);

  const handleAddUser = async () => {
    if (!newUserEmail) {
      showSnackbar("メールアドレスを入力してください", "error");
      return;
    }

    try {
      await addUserMutation.mutateAsync({
        sqid,
        data: {
          email: newUserEmail,
          is_owner: isOwner,
        },
      });
      showSnackbar("ユーザーを追加しました!", "success");
      setAddDialogOpen(false);
      setNewUserEmail("");
      setIsOwner(false);
    } catch (error) {
      console.error("Failed to add user to team:", error);
      showSnackbar("ユーザーの追加に失敗しました", "error");
    }
  };

  const handleEditUser = async () => {
    if (!editingUser?.id) return;

    try {
      await updateUserMutation.mutateAsync({
        sqid,
        userId: editingUser.id,
        data: {
          is_owner: editIsOwner,
        },
      });
      showSnackbar("ユーザー情報を更新しました!", "success");
      setEditDialogOpen(false);
      setEditingUser(null);
    } catch (error) {
      console.error("Failed to update team user:", error);
      showSnackbar("ユーザー情報の更新に失敗しました", "error");
    }
  };

  const handleDeleteUser = async () => {
    if (!deletingUser?.id) return;

    try {
      await removeUserMutation.mutateAsync({
        sqid,
        userId: deletingUser.id,
      });
      showSnackbar("ユーザーを削除しました!", "success");
      setDeleteDialogOpen(false);
      setDeletingUser(null);
    } catch (error) {
      console.error("Failed to remove user from team:", error);
      showSnackbar("ユーザーの削除に失敗しました", "error");
    }
  };

  const openEditDialog = (user: TeamUser) => {
    setEditingUser(user);
    setEditIsOwner(user.is_owner ?? false);
    setEditDialogOpen(true);
  };

  const openDeleteDialog = (user: TeamUser) => {
    setDeletingUser(user);
    setDeleteDialogOpen(true);
  };

  if (isLoading) {
    return (
      <Box display="flex" justifyContent="center" py={8}>
        <CircularProgress />
      </Box>
    );
  }

  if (error) {
    return (
      <Alert severity="error">
        ユーザー一覧の読み込みに失敗しました: {error.message}
      </Alert>
    );
  }

  const users = data?.data ?? [];

  return (
    <>
      <Card elevation={1}>
        <CardContent>
          <Stack spacing={3}>
            <Box
              display="flex"
              justifyContent="space-between"
              alignItems="center"
            >
              <Typography variant="h6" fontWeight="bold">
                チームメンバー
              </Typography>
              <Button
                variant="contained"
                startIcon={<PersonAddIcon />}
                onClick={() => setAddDialogOpen(true)}
              >
                メンバーを追加
              </Button>
            </Box>

            {users.length === 0 ? (
              <Alert severity="info">まだメンバーがいません</Alert>
            ) : (
              <TableContainer component={Paper} variant="outlined">
                <Table>
                  <TableHead>
                    <TableRow>
                      <TableCell>ユーザー名</TableCell>
                      <TableCell>メールアドレス</TableCell>
                      <TableCell>ロール</TableCell>
                      <TableCell>参加日時</TableCell>
                      <TableCell />
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {users.map((user) => (
                      <TableRow key={user.id} hover>
                        <TableCell>
                          <Box display="flex" alignItems="center" gap={1}>
                            {user.name}
                            {user.is_owner && (
                              <StarIcon color="warning" fontSize="small" />
                            )}
                          </Box>
                        </TableCell>
                        <TableCell>{user.email}</TableCell>
                        <TableCell>
                          <Chip
                            label={user.is_owner ? "オーナー" : "メンバー"}
                            color={user.is_owner ? "primary" : "default"}
                            size="small"
                          />
                        </TableCell>
                        <TableCell>
                          {user.created_at
                            ? new Date(user.created_at).toLocaleString("ja-JP")
                            : "-"}
                        </TableCell>
                        <TableCell align="right">
                          <IconButton
                            size="small"
                            onClick={() => openEditDialog(user)}
                            color="primary"
                          >
                            <EditIcon fontSize="small" />
                          </IconButton>
                          <IconButton
                            size="small"
                            onClick={() => openDeleteDialog(user)}
                            color="error"
                          >
                            <DeleteIcon fontSize="small" />
                          </IconButton>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </TableContainer>
            )}
          </Stack>
        </CardContent>
      </Card>

      {/* Add User Dialog */}
      <Dialog
        open={addDialogOpen}
        onClose={() => setAddDialogOpen(false)}
        maxWidth="sm"
        fullWidth
      >
        <DialogTitle>メンバーを追加</DialogTitle>
        <DialogContent>
          <Stack spacing={2} sx={{ mt: 2 }}>
            <TextField
              label="メールアドレス"
              value={newUserEmail}
              onChange={(e) => setNewUserEmail(e.target.value)}
              type="email"
              fullWidth
              helperText="追加するユーザーのメールアドレスを入力してください"
            />
            <FormControlLabel
              control={
                <Checkbox
                  checked={isOwner}
                  onChange={(e) => setIsOwner(e.target.checked)}
                />
              }
              label="オーナー権限を付与"
            />
          </Stack>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setAddDialogOpen(false)}>キャンセル</Button>
          <Button
            onClick={handleAddUser}
            variant="contained"
            disabled={addUserMutation.isPending || !newUserEmail}
          >
            {addUserMutation.isPending ? "追加中..." : "追加"}
          </Button>
        </DialogActions>
      </Dialog>

      {/* Edit User Dialog */}
      <Dialog
        open={editDialogOpen}
        onClose={() => setEditDialogOpen(false)}
        maxWidth="sm"
        fullWidth
      >
        <DialogTitle>メンバー情報を編集</DialogTitle>
        <DialogContent>
          <Stack spacing={2} sx={{ mt: 2 }}>
            <Typography variant="body2" color="text.secondary">
              ユーザー: {editingUser?.name}
            </Typography>
            <FormControlLabel
              control={
                <Checkbox
                  checked={editIsOwner}
                  onChange={(e) => setEditIsOwner(e.target.checked)}
                />
              }
              label="オーナー権限を付与"
            />
          </Stack>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setEditDialogOpen(false)}>キャンセル</Button>
          <Button
            onClick={handleEditUser}
            variant="contained"
            disabled={updateUserMutation.isPending}
          >
            {updateUserMutation.isPending ? "更新中..." : "更新"}
          </Button>
        </DialogActions>
      </Dialog>

      {/* Delete User Dialog */}
      <Dialog
        open={deleteDialogOpen}
        onClose={() => setDeleteDialogOpen(false)}
      >
        <DialogTitle>メンバーを削除しますか?</DialogTitle>
        <DialogContent>
          <DialogContentText>
            この操作は取り消せません。本当に「{deletingUser?.name}
            」をチームから削除しますか?
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteDialogOpen(false)}>キャンセル</Button>
          <Button
            onClick={handleDeleteUser}
            color="error"
            variant="contained"
            disabled={removeUserMutation.isPending}
          >
            {removeUserMutation.isPending ? "削除中..." : "削除"}
          </Button>
        </DialogActions>
      </Dialog>
    </>
  );
};
