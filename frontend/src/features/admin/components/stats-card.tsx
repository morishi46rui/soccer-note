"use client";

import { Box, Card, CardContent, Typography } from "@mui/material";
import type { StatsCardProps } from "../types/stats-card";

export const StatsCard = ({
  icon,
  title,
  value,
  caption,
  subText,
}: StatsCardProps) => {
  return (
    <Card>
      <CardContent>
        <Box sx={{ display: "flex", alignItems: "center", gap: 1, pb: 2 }}>
          {icon}
          <Typography variant="h6">{title}</Typography>
        </Box>
        <Typography variant="h3" fontWeight="bold">
          {value}
        </Typography>
        <Typography variant="caption" color="text.secondary">
          {caption}
        </Typography>
        {subText && (
          <Typography variant="body2" color="text.secondary" sx={{ mt: 1 }}>
            {subText}
          </Typography>
        )}
      </CardContent>
    </Card>
  );
};
