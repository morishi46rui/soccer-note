import { Button, Paper, Stack, Typography } from "@mui/material";
import type { CounterCardProps } from "../types/props";

export const CounterCard = ({ count, onIncrement }: CounterCardProps) => {
  return (
    <Paper elevation={3} sx={{ p: 4 }}>
      <Stack spacing={2}>
        <Typography variant="h5" fontWeight="medium">
          カウント: {count}
        </Typography>
        <Button variant="contained" size="large" onClick={onIncrement}>
          1増やす
        </Button>
      </Stack>
    </Paper>
  );
};
