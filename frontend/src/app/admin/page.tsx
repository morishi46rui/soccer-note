"use client";

import { AdminLayout } from "@/features/admin/components/admin-layout";
import {
  Groups as GroupsIcon,
  Notes as NotesIcon,
  People as PeopleIcon,
  SportsBasketball as TeamsIcon,
} from "@mui/icons-material";
import { Box, Card, CardContent, Container, Typography } from "@mui/material";

const AdminDashboard = () => {
  return (
    <AdminLayout>
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box sx={{ mb: 4 }}>
          <Typography variant="h4" fontWeight="bold" gutterBottom>
            管理者ダッシュボード
          </Typography>
          <Typography variant="body1" color="text.secondary">
            システムの概要と統計情報
          </Typography>
        </Box>

        <Box
          sx={{
            display: "grid",
            gridTemplateColumns: {
              xs: "1fr",
              sm: "repeat(2, 1fr)",
              md: "repeat(4, 1fr)",
            },
            gap: 3,
          }}
        >
          {/* ユーザー数 */}
          <Card>
            <CardContent>
              <Box sx={{ display: "flex", alignItems: "center", mb: 2 }}>
                <PeopleIcon color="primary" sx={{ mr: 1 }} />
                <Typography variant="h6">ユーザー</Typography>
              </Box>
              <Typography variant="h3" fontWeight="bold">
                0
              </Typography>
              <Typography variant="caption" color="text.secondary">
                登録ユーザー数
              </Typography>
            </CardContent>
          </Card>

          {/* チーム数 */}
          <Card>
            <CardContent>
              <Box sx={{ display: "flex", alignItems: "center", mb: 2 }}>
                <TeamsIcon color="success" sx={{ mr: 1 }} />
                <Typography variant="h6">チーム</Typography>
              </Box>
              <Typography variant="h3" fontWeight="bold">
                0
              </Typography>
              <Typography variant="caption" color="text.secondary">
                登録チーム数
              </Typography>
            </CardContent>
          </Card>

          {/* グループ数 */}
          <Card>
            <CardContent>
              <Box sx={{ display: "flex", alignItems: "center", mb: 2 }}>
                <GroupsIcon color="warning" sx={{ mr: 1 }} />
                <Typography variant="h6">グループ</Typography>
              </Box>
              <Typography variant="h3" fontWeight="bold">
                0
              </Typography>
              <Typography variant="caption" color="text.secondary">
                登録グループ数
              </Typography>
            </CardContent>
          </Card>

          {/* ノート数 */}
          <Card>
            <CardContent>
              <Box sx={{ display: "flex", alignItems: "center", mb: 2 }}>
                <NotesIcon color="info" sx={{ mr: 1 }} />
                <Typography variant="h6">ノート</Typography>
              </Box>
              <Typography variant="h3" fontWeight="bold">
                0
              </Typography>
              <Typography variant="caption" color="text.secondary">
                作成ノート数
              </Typography>
            </CardContent>
          </Card>
        </Box>

        {/* 最近の活動 */}
        <Box sx={{ mt: 4 }}>
          <Typography variant="h5" fontWeight="bold" gutterBottom>
            最近の活動
          </Typography>
          <Card>
            <CardContent>
              <Typography variant="body2" color="text.secondary">
                最近のシステム活動はありません
              </Typography>
            </CardContent>
          </Card>
        </Box>
      </Container>
    </AdminLayout>
  );
};

export default AdminDashboard;
