"use client";

import GoogleIcon from "@mui/icons-material/Google";
import Visibility from "@mui/icons-material/Visibility";
import VisibilityOff from "@mui/icons-material/VisibilityOff";
import {
  Alert,
  Button,
  Checkbox,
  Collapse,
  FormControlLabel,
  IconButton,
  InputAdornment,
  Link,
  Paper,
  Stack,
  TextField,
  Typography,
} from "@mui/material";
import { useState } from "react";
import { useLoginForm } from "../hooks/use-login-form";

export const LoginForm = () => {
  const [showPassword, setShowPassword] = useState(false);
  const {
    values,
    errors,
    status,
    errorMessage,
    isSubmitting,
    isSubmitDisabled,
    handleEmailChange,
    handlePasswordChange,
    handleStaySignedInChange,
    handleSubmit,
    dismissStatus,
  } = useLoginForm();

  const togglePasswordVisibility = () => {
    setShowPassword((prev) => !prev);
  };

  return (
    <Paper
      component="form"
      elevation={4}
      onSubmit={handleSubmit}
      autoComplete="on"
      noValidate
      sx={{ p: 5 }}
    >
      <Stack spacing={3}>
        <Stack alignItems="center" textAlign="center">
          <Typography component="h2" variant="h5" fontWeight="bold">
            ログイン
          </Typography>
        </Stack>

        <TextField
          id="login-email"
          name="email"
          label="メールアドレス"
          type="email"
          inputMode="email"
          autoComplete="email"
          required
          fullWidth
          value={values.email}
          onChange={handleEmailChange}
          error={Boolean(errors.email)}
          helperText={errors.email}
        />

        <TextField
          id="login-password"
          name="password"
          label="パスワード"
          type={showPassword ? "text" : "password"}
          autoComplete="current-password"
          required
          fullWidth
          value={values.password}
          onChange={handlePasswordChange}
          error={Boolean(errors.password)}
          helperText={errors.password}
          slotProps={{
            input: {
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    aria-label={
                      showPassword ? "パスワードを隠す" : "パスワードを表示する"
                    }
                    onClick={togglePasswordVisibility}
                    edge="end"
                  >
                    {showPassword ? <VisibilityOff /> : <Visibility />}
                  </IconButton>
                </InputAdornment>
              ),
            },
          }}
        />

        <Stack
          direction={{ xs: "column", sm: "row" }}
          spacing={1}
          alignItems={{ xs: "flex-start", sm: "center" }}
          justifyContent="space-between"
        >
          <FormControlLabel
            control={
              <Checkbox
                checked={values.staySignedIn}
                onChange={handleStaySignedInChange}
                color="primary"
                inputProps={{ "aria-label": "次回から自動的にサインイン" }}
              />
            }
            label="次回から自動的にサインイン"
          />
          <Link
            component="button"
            type="button"
            underline="hover"
            color="primary"
            onClick={() => {
              // 実装時にルーターへ差し替える
            }}
          >
            パスワードを忘れた？
          </Link>
        </Stack>

        <Button
          type="submit"
          variant="contained"
          size="large"
          fullWidth
          disabled={isSubmitDisabled}
        >
          {isSubmitting ? "サインイン中..." : "ログイン"}
        </Button>

        <Button
          type="button"
          variant="outlined"
          fullWidth
          startIcon={<GoogleIcon />}
          sx={{ textTransform: "none" }}
        >
          Googleで続行
        </Button>

        <Collapse in={status === "success"}>
          <Alert onClose={dismissStatus} severity="success" variant="outlined">
            ログインに成功しました！
          </Alert>
        </Collapse>

        <Collapse in={status === "error"}>
          <Alert onClose={dismissStatus} severity="error" variant="outlined">
            {errorMessage || "ログインに失敗しました"}
          </Alert>
        </Collapse>
      </Stack>
    </Paper>
  );
};
