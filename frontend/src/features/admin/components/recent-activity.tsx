"use client";

import { Box, Card, CardContent, Typography } from "@mui/material";

export const RecentActivity = () => {
  return (
    <Box>
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
  );
};
