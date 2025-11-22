"use client";

import { Box, CircularProgress, Container, Typography } from "@mui/material";
import { useGetAdminStats } from "../api/get-admin-stats";
import { AdminLayout } from "./admin-layout";
import { RecentActivity } from "./recent-activity";
import { StatsCards } from "./stats-cards";

export const AdminDashboard = () => {
  const { data, isLoading, error } = useGetAdminStats();

  if (isLoading) {
    return (
      <AdminLayout>
        <Container maxWidth="lg" sx={{ py: 4 }}>
          <Box
            sx={{
              display: "flex",
              justifyContent: "center",
              alignItems: "center",
              minHeight: "50vh",
            }}
          >
            <CircularProgress />
          </Box>
        </Container>
      </AdminLayout>
    );
  }

  if (error) {
    return (
      <AdminLayout>
        <Container maxWidth="lg" sx={{ py: 4 }}>
          <Typography color="error">
            データの取得に失敗しました: {error.message}
          </Typography>
        </Container>
      </AdminLayout>
    );
  }

  return (
    <AdminLayout>
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box sx={{ display: "flex", flexDirection: "column", gap: 4 }}>
          <Box>
            <Typography variant="h4" fontWeight="bold" gutterBottom>
              管理者ダッシュボード
            </Typography>
            <Typography variant="body1" color="text.secondary">
              システムの概要と統計情報
            </Typography>
          </Box>

          <StatsCards data={data} />
          <RecentActivity />
        </Box>
      </Container>
    </AdminLayout>
  );
};
