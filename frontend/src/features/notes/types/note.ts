import type { components } from "@/types/api";

export type Note = components["schemas"]["Note"];
export type GetNotesResponse = components["schemas"]["GetNotesResponse"];
export type CreateNoteRequest = components["schemas"]["CreateNoteRequest"];
export type UpdateNoteRequest = components["schemas"]["UpdateNoteRequest"];

export type GetNotesParams = {
  page?: number;
  per_page?: number;
};
