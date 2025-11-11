import { useCallback, useMemo, useState } from 'react'
import type { ChangeEvent, FormEvent } from 'react'
import type {
  LoginFormErrors,
  LoginFormStatus,
  LoginFormValues,
} from '../types/login-form'

const initialValues: LoginFormValues = {
  email: '',
  password: '',
  staySignedIn: true,
}

const emailPattern =
  /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/

const validateEmail = (value: string) => {
  if (!value.trim()) {
    return 'メールアドレスを入力してください'
  }
  if (!emailPattern.test(value)) {
    return 'メールアドレスの形式が正しくありません'
  }
  return undefined
}

const validatePassword = (value: string) => {
  if (!value.trim()) {
    return 'パスワードを入力してください'
  }
  if (value.length < 8) {
    return '8文字以上のパスワードを使用してください'
  }
  return undefined
}

const validateAll = (values: LoginFormValues) => {
  const nextErrors: LoginFormErrors = {}
  const emailError = validateEmail(values.email)
  const passwordError = validatePassword(values.password)

  if (emailError) {
    nextErrors.email = emailError
  }
  if (passwordError) {
    nextErrors.password = passwordError
  }

  return nextErrors
}

export const useLoginForm = () => {
  const [values, setValues] = useState<LoginFormValues>(initialValues)
  const [errors, setErrors] = useState<LoginFormErrors>({})
  const [status, setStatus] = useState<LoginFormStatus>('idle')

  const updateField = useCallback(
    (field: 'email' | 'password', value: string) => {
      setValues((prev) => ({ ...prev, [field]: value }))
      setErrors((prev) => ({
        ...prev,
        [field]: field === 'email' ? validateEmail(value) : validatePassword(value),
      }))
      if (status === 'success') {
        setStatus('idle')
      }
    },
    [status],
  )

  const handleEmailChange = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      updateField('email', event.target.value)
    },
    [updateField],
  )

  const handlePasswordChange = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      updateField('password', event.target.value)
    },
    [updateField],
  )

  const handleStaySignedInChange = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      const { checked } = event.target
      setValues((prev) => ({ ...prev, staySignedIn: checked }))
    },
    [],
  )

  const handleSubmit = useCallback(
    (event: FormEvent<HTMLFormElement>) => {
      event.preventDefault()
      const nextErrors = validateAll(values)
      setErrors(nextErrors)

      if (Object.keys(nextErrors).length > 0) {
        setStatus('idle')
        return
      }

      setStatus('submitting')
      window.setTimeout(() => {
        setStatus('success')
      }, 600)
    },
    [values],
  )

  const isSubmitting = status === 'submitting'

  const isSubmitDisabled = useMemo(() => {
    if (isSubmitting) {
      return true
    }
    const hasEmptyField =
      values.email.trim().length === 0 || values.password.trim().length === 0
    const hasErrors = Boolean(errors.email) || Boolean(errors.password)
    return hasEmptyField || hasErrors
  }, [errors.email, errors.password, isSubmitting, values.email, values.password])

  const dismissStatus = useCallback(() => {
    setStatus('idle')
  }, [])

  return {
    values,
    errors,
    status,
    isSubmitting,
    isSubmitDisabled,
    handleEmailChange,
    handlePasswordChange,
    handleStaySignedInChange,
    handleSubmit,
    dismissStatus,
  }
}
