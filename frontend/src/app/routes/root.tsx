import SportsSoccerIcon from "@mui/icons-material/SportsSoccer";
import { Container, Stack, Typography } from "@mui/material";
import { LoginFeature } from "../../features/auth/login/login-feature";

export const RootRoute = () => {
  return (
    <Container
      maxWidth="md"
      sx={{
        minHeight: "100vh",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
      }}
    >
      <Stack spacing={5}>
        <Stack spacing={1} alignItems="center" textAlign="center">
          <Typography
            component="h1"
            variant="h4"
            fontWeight="bold"
            display="inline-flex"
            alignItems="center"
            gap={1}
          >
            Soccer Note
            <SportsSoccerIcon color="primary" fontSize="large" />
          </Typography>
        </Stack>
        <LoginFeature />
      </Stack>
    </Container>
  );
};
