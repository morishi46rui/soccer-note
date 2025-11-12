export type LoginFormValues = {
  email: string;
  password: string;
  staySignedIn: boolean;
};

export type LoginFormErrors = {
  email?: string;
  password?: string;
};

export type LoginFormStatus = "idle" | "submitting" | "success" | "error";
