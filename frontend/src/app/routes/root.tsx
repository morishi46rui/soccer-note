import SportsSoccerIcon from "@mui/icons-material/SportsSoccer";
import { Box, Container, Stack, Typography } from "@mui/material";
import { CounterFeature } from "../../features/counter/counter-feature";

export const RootRoute = () => {
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

        <CounterFeature />
      </Stack>
    </Container>
  );
};
