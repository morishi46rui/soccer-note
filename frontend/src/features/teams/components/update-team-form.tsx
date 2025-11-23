"use client";

import {
  Box,
  Button,
  Paper,
  Stack,
  TextField,
  Typography,
} from "@mui/material";
import { useUpdateTeamForm } from "../hooks/use-update-team-form";
import type { UpdateTeamFormProps } from "../types/team";

export const UpdateTeamForm = ({ team, sqid }: UpdateTeamFormProps) => {
  const {
    values,
    errors,
    isSubmitting,
    isSubmitDisabled,
    handleNameChange,
    handleDescriptionChange,
    handleSubmit,
  } = useUpdateTeamForm(team, sqid);

  return (
    <Paper
      component="form"
      elevation={4}
      onSubmit={handleSubmit}
      noValidate
      sx={{ p: 5 }}
    >
      <Stack spacing={3}>
        <Stack alignItems="center" textAlign="center">
          <Typography component="h2" variant="h5" fontWeight="bold">
            チーム編集
          </Typography>
        </Stack>

        <TextField
          id="team-name"
          name="name"
          label="チーム名"
          type="text"
          required
          fullWidth
          value={values.name}
          onChange={handleNameChange}
          error={Boolean(errors.name)}
          helperText={errors.name}
        />

        <TextField
          id="team-description"
          name="description"
          label="説明"
          multiline
          rows={4}
          fullWidth
          value={values.description}
          onChange={handleDescriptionChange}
          error={Boolean(errors.description)}
          helperText={errors.description}
        />

        <Box sx={{ display: "flex", gap: 2 }}>
          <Button
            type="submit"
            variant="contained"
            size="large"
            fullWidth
            disabled={isSubmitDisabled}
          >
            {isSubmitting ? "更新中..." : "更新"}
          </Button>
          <Button
            type="button"
            variant="outlined"
            size="large"
            fullWidth
            href={`/teams/${sqid}`}
          >
            キャンセル
          </Button>
        </Box>
      </Stack>
    </Paper>
  );
};
