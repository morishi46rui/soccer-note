import type { ReactNode } from "react";
import { AdminStatsResponse } from "../api/get-admin-stats";

export type StatsCardProps = {
  icon: ReactNode;
  title: string;
  value: number | string;
  caption: string;
  subText?: string;
};

export type StatsCardsProps = {
  data: AdminStatsResponse | undefined;
};
