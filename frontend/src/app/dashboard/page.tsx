"use client";

import { DashboardLayout } from "@/components/dashboard-layout";
import {
  Add as AddIcon,
  Description as DescriptionIcon,
} from "@mui/icons-material";
import { Box, Card, CardActionArea, Stack, Typography } from "@mui/material";
import Link from "next/link";

export default function DashboardPage() {

  return (
    <DashboardLayout>
      <Box sx={{ p: 4 }}>
        <Stack spacing={4}>
          {/* Header */}
          <Box>
            <Typography
              variant="h4"
              component="h1"
              fontWeight="bold"
              gutterBottom
            >
              ダッシュボード
            </Typography>
          </Box>

          {/* Quick Actions */}
          <Box>
            <Typography variant="h6" gutterBottom>
              クイックアクション
            </Typography>
            <Box
              sx={{
                display: "grid",
                gridTemplateColumns: {
                  xs: "1fr",
                  sm: "repeat(2, 1fr)",
                  md: "repeat(3, 1fr)",
                },
                gap: 2,
              }}
            >
              <Card
                elevation={0}
                sx={{
                  border: 1,
                  borderColor: "divider",
                  "&:hover": {
                    borderColor: "primary.main",
                    backgroundColor: "action.hover",
                  },
                }}
              >
                <CardActionArea component={Link} href="/notes/new" sx={{ p: 3 }}>
                  <Stack spacing={2} alignItems="center">
                    <AddIcon sx={{ fontSize: 40, color: "primary.main" }} />
                    <Typography variant="h6">新しいノート</Typography>
                    <Typography
                      variant="body2"
                      color="text.secondary"
                      textAlign="center"
                    >
                      サッカーノートを作成
                    </Typography>
                  </Stack>
                </CardActionArea>
              </Card>

              <Card
                elevation={0}
                sx={{
                  border: 1,
                  borderColor: "divider",
                  "&:hover": {
                    borderColor: "primary.main",
                    backgroundColor: "action.hover",
                  },
                }}
              >
                <CardActionArea component={Link} href="/notes" sx={{ p: 3 }}>
                  <Stack spacing={2} alignItems="center">
                    <DescriptionIcon
                      sx={{ fontSize: 40, color: "primary.main" }}
                    />
                    <Typography variant="h6">ノート一覧</Typography>
                    <Typography
                      variant="body2"
                      color="text.secondary"
                      textAlign="center"
                    >
                      すべてのノートを表示
                    </Typography>
                  </Stack>
                </CardActionArea>
              </Card>
            </Box>
          </Box>

          {/* Recent Activity */}
          <Box>
            <Typography variant="h6" gutterBottom>
              最近のアクティビティ
            </Typography>
            <Card
              elevation={0}
              sx={{
                border: 1,
                borderColor: "divider",
                p: 4,
                textAlign: "center",
              }}
            >
              <Typography color="text.secondary">
                アクティビティはまだありません
              </Typography>
            </Card>
          </Box>
        </Stack>
      </Box>
    </DashboardLayout>
  );
}
