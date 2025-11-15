import type { components } from "@/types/api";

export type Note = components["schemas"]["Note"];
export type GetNotesResponse = components["schemas"]["GetNotesResponse"];
export type CreateNoteRequest = components["schemas"]["CreateNoteRequest"];
export type UpdateNoteRequest = components["schemas"]["UpdateNoteRequest"];

export type GetNotesParams = {
  page?: number;
  per_page?: number;
};

export type FormErrors = {
  title?: string;
  date?: string;
  content?: string;
};

export type FormStatus = "idle" | "success" | "error";

export type PageParams = {
  params: Promise<{ sqid: string }>;
};

export type UpdateNoteFormProps = {
  note: Note | undefined;
  sqid: string;
};

export type CreateNoteFormValues = CreateNoteRequest;

export type UpdateNoteFormValues = UpdateNoteRequest;
