"use client";

import { useAuth } from "@/hooks/use-auth";
import { Button, Container, Paper, Stack, Typography } from "@mui/material";
import { useRouter } from "next/navigation";

export default function DashboardPage() {
  const router = useRouter();
  const { user, logout } = useAuth();

  const handleLogout = () => {
    logout();
    router.push("/");
  };

  return (
    <Container maxWidth="lg" sx={{ py: 8 }}>
      <Stack spacing={4}>
        <Paper sx={{ p: 4 }}>
          <Stack spacing={3}>
            <Typography variant="h4" component="h1" fontWeight="bold">
              ダッシュボード
            </Typography>

            {user && (
              <Stack spacing={1}>
                <Typography variant="h6">
                  ようこそ、{user.name}さん！
                </Typography>
                <Typography color="text.secondary">
                  メール: {user.email}
                </Typography>
              </Stack>
            )}

            <Button
              variant="outlined"
              onClick={handleLogout}
              sx={{ alignSelf: "flex-start" }}
            >
              ログアウト
            </Button>
          </Stack>
        </Paper>

        <Paper sx={{ p: 4 }}>
          <Typography variant="h6" gutterBottom>
            今後の実装予定
          </Typography>
          <Typography color="text.secondary">
            ここにサッカーノートの機能が追加されます
          </Typography>
        </Paper>
      </Stack>
    </Container>
  );
}
