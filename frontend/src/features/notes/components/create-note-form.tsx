"use client";

import {
  Box,
  Button,
  Paper,
  Stack,
  TextField,
  Typography,
} from "@mui/material";
import { useCreateNoteForm } from "../hooks/use-create-note-form";

export const CreateNoteForm = () => {
  const {
    values,
    errors,
    isSubmitting,
    isSubmitDisabled,
    handleTitleChange,
    handleDateChange,
    handleContentChange,
    handleSubmit,
  } = useCreateNoteForm();

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
            ノート新規作成
          </Typography>
        </Stack>

        <TextField
          id="note-title"
          name="title"
          label="タイトル"
          type="text"
          required
          fullWidth
          value={values.title}
          onChange={handleTitleChange}
          error={Boolean(errors.title)}
          helperText={errors.title}
        />

        <TextField
          id="note-date"
          name="date"
          label="日付"
          type="date"
          required
          fullWidth
          value={values.date}
          onChange={handleDateChange}
          error={Boolean(errors.date)}
          helperText={errors.date}
          InputLabelProps={{
            shrink: true,
          }}
        />

        <TextField
          id="note-content"
          name="content"
          label="内容"
          multiline
          rows={10}
          required
          fullWidth
          value={values.content}
          onChange={handleContentChange}
          error={Boolean(errors.content)}
          helperText={errors.content}
        />

        <Box sx={{ display: "flex", gap: 2 }}>
          <Button
            type="submit"
            variant="contained"
            size="large"
            fullWidth
            disabled={isSubmitDisabled}
          >
            {isSubmitting ? "作成中..." : "作成"}
          </Button>
          <Button
            type="button"
            variant="outlined"
            size="large"
            fullWidth
            href="/notes"
          >
            キャンセル
          </Button>
        </Box>
      </Stack>
    </Paper>
  );
};
