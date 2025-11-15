import { useSnackbar } from "@/hooks/use-snackbar";
import { useRouter } from "next/navigation";
import { useState, type ChangeEvent, type FormEvent } from "react";
import { useUpdateNote } from "../api/update-note";
import type { FormErrors, Note, UpdateNoteFormValues } from "../types/note";

export const useUpdateNoteForm = (note: Note | undefined, sqid: string) => {
  const router = useRouter();
  const { mutate, isPending } = useUpdateNote();
  const { showSnackbar } = useSnackbar();

  const [values, setValues] = useState<UpdateNoteFormValues>(() => ({
    title: note?.title || "",
    date: note?.date || "",
    content: note?.content || "",
  }));

  const [errors, setErrors] = useState<FormErrors>({});

  const handleTitleChange = (e: ChangeEvent<HTMLInputElement>) => {
    setValues((prev) => ({ ...prev, title: e.target.value }));
    if (errors.title) {
      setErrors((prev) => ({ ...prev, title: undefined }));
    }
  };

  const handleDateChange = (e: ChangeEvent<HTMLInputElement>) => {
    setValues((prev) => ({ ...prev, date: e.target.value }));
    if (errors.date) {
      setErrors((prev) => ({ ...prev, date: undefined }));
    }
  };

  const handleContentChange = (e: ChangeEvent<HTMLTextAreaElement>) => {
    setValues((prev) => ({ ...prev, content: e.target.value }));
    if (errors.content) {
      setErrors((prev) => ({ ...prev, content: undefined }));
    }
  };

  const validate = (): boolean => {
    const newErrors: FormErrors = {};

    if (!values.title?.trim()) {
      newErrors.title = "タイトルは必須です";
    }

    if (!values.date) {
      newErrors.date = "日付は必須です";
    }

    if (!values.content?.trim()) {
      newErrors.content = "内容は必須です";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!validate()) {
      return;
    }

    mutate(
      { sqid, data: values },
      {
        onSuccess: () => {
          showSnackbar("ノートを更新しました!", "success");
          router.push(`/notes/${sqid}`);
        },
        onError: (error) => {
          const errorMsg =
            error instanceof Error
              ? error.message
              : "ノートの更新に失敗しました";
          showSnackbar(errorMsg, "error");
        },
      }
    );
  };

  return {
    values,
    errors,
    isSubmitting: isPending,
    isSubmitDisabled: isPending,
    handleTitleChange,
    handleDateChange,
    handleContentChange,
    handleSubmit,
  };
};
