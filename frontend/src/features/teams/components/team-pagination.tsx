"use client";

import { Box, Pagination, Stack, Typography } from "@mui/material";
import type { TeamPaginationProps } from "../types/team";

export const TeamPagination = ({
  currentPage,
  lastPage,
  total,
  onPageChange,
}: TeamPaginationProps) => {
  if (lastPage <= 1) {
    return (
      <Typography variant="caption" color="text.secondary" textAlign="center">
        全{total}件
      </Typography>
    );
  }

  return (
    <Stack spacing={2} alignItems="center">
      <Box display="flex" justifyContent="center" py={2}>
        <Pagination
          count={lastPage}
          page={currentPage}
          onChange={(_, newPage) => onPageChange(newPage)}
          color="primary"
        />
      </Box>
      <Typography variant="caption" color="text.secondary" textAlign="center">
        全{total}件
      </Typography>
    </Stack>
  );
};
