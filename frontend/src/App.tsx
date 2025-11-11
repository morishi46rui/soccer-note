import SportsSoccerIcon from "@mui/icons-material/SportsSoccer";
import {
  Box,
  Button,
  Container,
  Paper,
  Stack,
  Typography,
} from "@mui/material";
import { useState } from "react";

function App() {
  const [count, setCount] = useState(0);

  return (
    <Container maxWidth="sm">
      <Stack spacing={4} py={8}>
        <Stack direction="row" alignItems="center" spacing={1}>
          <SportsSoccerIcon color="primary" fontSize="large" />
          <Box>
            <Typography component="h1" variant="h4" fontWeight="bold">
              Soccer Note
            </Typography>
            <Typography color="text.secondary">
              React + MUI スタイルのスターティングポイント
            </Typography>
          </Box>
        </Stack>

        <Paper elevation={3} sx={{ p: 4 }}>
          <Stack spacing={2}>
            <Typography>
              サンプルカウンターを使ってコンポーネントのつながりを確認できるよ。
            </Typography>
            <Typography variant="h5" fontWeight="medium">
              カウント: {count}
            </Typography>
            <Button
              variant="contained"
              size="large"
              onClick={() => setCount((prev) => prev + 1)}
            >
              1増やす
            </Button>
          </Stack>
        </Paper>
      </Stack>
    </Container>
  );
}

export default App;
