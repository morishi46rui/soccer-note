"use client";

import { Stack } from "@mui/material";
import { useState } from "react";
import { useGetTeams } from "../api/get-teams";
import { EmptyState } from "./empty-state";
import { ErrorState } from "./error-state";
import { LoadingState } from "./loading-state";
import { TeamCard } from "./team-card";
import { TeamPagination } from "./team-pagination";

export const TeamList = () => {
  const [page, setPage] = useState(1);
  const { data, isLoading, error } = useGetTeams({ page, per_page: 15 });

  if (isLoading) {
    return <LoadingState />;
  }

  if (error) {
    return (
      <ErrorState
        message={`チームの読み込みに失敗しました: ${error.message}`}
      />
    );
  }

  const teams = data?.data ?? [];
  const pagination = {
    currentPage: data?.current_page ?? 1,
    lastPage: data?.last_page ?? 1,
    total: data?.total ?? 0,
  };

  if (teams.length === 0) {
    return <EmptyState message="まだチームがありません" />;
  }

  return (
    <Stack spacing={3}>
      <Stack spacing={2}>
        {teams.map((team) => (
          <TeamCard key={team.sqid} team={team} />
        ))}
      </Stack>

      <TeamPagination
        currentPage={pagination.currentPage}
        lastPage={pagination.lastPage}
        total={pagination.total}
        onPageChange={setPage}
      />
    </Stack>
  );
};
