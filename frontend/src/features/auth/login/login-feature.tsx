import { Stack } from "@mui/material";
import { LoginForm } from "./components/login-form";

export const LoginFeature = () => {
  return (
    <Stack spacing={4}>
      <Stack
        direction={{ xs: "column", md: "row" }}
        spacing={4}
        alignItems="stretch"
        sx={{ width: "100%" }}
      >
        <Stack sx={{ flex: { md: 1 } }}>
          <LoginForm />
        </Stack>
      </Stack>
    </Stack>
  );
};
