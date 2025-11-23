import { useSnackbar } from "@/hooks/use-snackbar";
import { useRouter } from "next/navigation";
import { useState, type ChangeEvent, type FormEvent } from "react";
import { useCreateTeam } from "../api/create-team";
import type { CreateTeamFormValues, FormErrors } from "../types/team";

export const useCreateTeamForm = () => {
  const router = useRouter();
  const { mutate, isPending } = useCreateTeam();
  const { showSnackbar } = useSnackbar();

  const [values, setValues] = useState<CreateTeamFormValues>({
    name: "",
    description: "",
  });

  const [errors, setErrors] = useState<FormErrors>({});

  const handleNameChange = (e: ChangeEvent<HTMLInputElement>) => {
    setValues((prev) => ({ ...prev, name: e.target.value }));
    if (errors.name) {
      setErrors((prev) => ({ ...prev, name: undefined }));
    }
  };

  const handleDescriptionChange = (e: ChangeEvent<HTMLTextAreaElement>) => {
    setValues((prev) => ({ ...prev, description: e.target.value }));
    if (errors.description) {
      setErrors((prev) => ({ ...prev, description: undefined }));
    }
  };

  const validate = (): boolean => {
    const newErrors: FormErrors = {};

    if (!values.name?.trim()) {
      newErrors.name = "チーム名は必須です";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!validate()) {
      return;
    }

    mutate(values, {
      onSuccess: () => {
        showSnackbar("チームを作成しました!", "success");
        router.push("/teams");
      },
      onError: (error) => {
        const errorMsg =
          error instanceof Error ? error.message : "チームの作成に失敗しました";
        showSnackbar(errorMsg, "error");
      },
    });
  };

  return {
    values,
    errors,
    isSubmitting: isPending,
    isSubmitDisabled: isPending,
    handleNameChange,
    handleDescriptionChange,
    handleSubmit,
  };
};
