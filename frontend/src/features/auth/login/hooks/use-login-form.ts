"use client";

import { ApiError } from "@/lib/api-client";
import { useRouter } from "next/navigation";
import type { ChangeEvent, FormEvent } from "react";
import { useCallback, useMemo, useState } from "react";
import { useLoginMutation } from "../api/login";
import type {
  LoginFormErrors,
  LoginFormStatus,
  LoginFormValues,
} from "../types/login-form";

const initialValues: LoginFormValues = {
  email: "",
  password: "",
  staySignedIn: true,
};

const emailPattern =
  /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

const validateEmail = (value: string) => {
  if (!value.trim()) {
    return "メールアドレスを入力してください";
  }
  if (!emailPattern.test(value)) {
    return "メールアドレスの形式が正しくありません";
  }
  return undefined;
};

const validatePassword = (value: string) => {
  if (!value.trim()) {
    return "パスワードを入力してください";
  }
  if (value.length < 8) {
    return "8文字以上のパスワードを使用してください";
  }
  return undefined;
};

const validateAll = (values: LoginFormValues) => {
  const nextErrors: LoginFormErrors = {};
  const emailError = validateEmail(values.email);
  const passwordError = validatePassword(values.password);

  if (emailError) {
    nextErrors.email = emailError;
  }
  if (passwordError) {
    nextErrors.password = passwordError;
  }

  return nextErrors;
};

export const useLoginForm = () => {
  const [values, setValues] = useState<LoginFormValues>(initialValues);
  const [errors, setErrors] = useState<LoginFormErrors>({});
  const [status, setStatus] = useState<LoginFormStatus>("idle");
  const [errorMessage, setErrorMessage] = useState<string>("");
  const router = useRouter();

  const loginMutation = useLoginMutation();

  const updateField = useCallback(
    (field: "email" | "password", value: string) => {
      setValues((prev) => ({ ...prev, [field]: value }));
      setErrors((prev) => ({
        ...prev,
        [field]:
          field === "email" ? validateEmail(value) : validatePassword(value),
      }));
      if (status === "success" || status === "error") {
        setStatus("idle");
        setErrorMessage("");
      }
    },
    [status]
  );

  const handleEmailChange = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      updateField("email", event.target.value);
    },
    [updateField]
  );

  const handlePasswordChange = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      updateField("password", event.target.value);
    },
    [updateField]
  );

  const handleStaySignedInChange = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      const { checked } = event.target;
      setValues((prev) => ({ ...prev, staySignedIn: checked }));
    },
    []
  );

  const handleSubmit = useCallback(
    (event: FormEvent<HTMLFormElement>) => {
      event.preventDefault();
      const nextErrors = validateAll(values);
      setErrors(nextErrors);

      if (Object.keys(nextErrors).length > 0) {
        setStatus("idle");
        return;
      }

      setStatus("submitting");
      setErrorMessage("");

      loginMutation.mutate(
        {
          email: values.email,
          password: values.password,
          device_name: "Web Browser",
        },
        {
          onSuccess: () => {
            setStatus("success");
            setTimeout(() => {
              router.push("/dashboard");
            }, 1000);
          },
          onError: (error) => {
            setStatus("error");
            if (error instanceof ApiError) {
              if (error.status === 401) {
                setErrorMessage(
                  "メールアドレスまたはパスワードが正しくありません"
                );
              } else if (error.status === 422) {
                setErrorMessage("入力内容に誤りがあります");
              } else {
                setErrorMessage(
                  "ログインに失敗しました。もう一度お試しください"
                );
              }
            } else {
              setErrorMessage("ネットワークエラーが発生しました");
            }
          },
        }
      );
    },
    [values, loginMutation, router]
  );

  const isSubmitting = status === "submitting" || loginMutation.isPending;

  const isSubmitDisabled = useMemo(() => {
    if (isSubmitting) {
      return true;
    }
    const hasEmptyField =
      values.email.trim().length === 0 || values.password.trim().length === 0;
    const hasErrors = Boolean(errors.email) || Boolean(errors.password);
    return hasEmptyField || hasErrors;
  }, [
    errors.email,
    errors.password,
    isSubmitting,
    values.email,
    values.password,
  ]);

  const dismissStatus = useCallback(() => {
    setStatus("idle");
    setErrorMessage("");
  }, []);

  return {
    values,
    errors,
    status,
    errorMessage,
    isSubmitting,
    isSubmitDisabled,
    handleEmailChange,
    handlePasswordChange,
    handleStaySignedInChange,
    handleSubmit,
    dismissStatus,
  };
};
