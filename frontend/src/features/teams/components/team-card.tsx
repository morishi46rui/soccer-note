"use client";

import { Card, CardActionArea, CardContent, Stack, Typography } from "@mui/material";
import Link from "next/link";
import type { TeamCardProps } from "../types/team";

export const TeamCard = ({ team }: TeamCardProps) => {
  return (
    <Card elevation={1}>
      <CardActionArea component={Link} href={`/teams/${team.sqid}`}>
        <CardContent>
          <Stack spacing={1}>
            <Typography variant="h6" component="h3" fontWeight="medium">
              {team.name}
            </Typography>
            {team.description && (
              <Typography
                variant="body2"
                color="text.secondary"
                sx={{
                  overflow: "hidden",
                  textOverflow: "ellipsis",
                  display: "-webkit-box",
                  WebkitLineClamp: 2,
                  WebkitBoxOrient: "vertical",
                }}
              >
                {team.description}
              </Typography>
            )}
          </Stack>
        </CardContent>
      </CardActionArea>
    </Card>
  );
};
