# Frontend Coding Standards

このドキュメントは、Soccer Note フロントエンド開発における TypeScript/React のコーディング規約を定義します。

**重要**: フロントエンドのコードを書く前に、必ずこのドキュメントを読んで規約を確認してください。

## Table of Contents

- [Type Definitions](#type-definitions)
- [Function Declarations](#function-declarations)
- [File Extensions](#file-extensions)
- [Component Structure](#component-structure)

## Type Definitions

### Use `type` instead of `interface`

一貫性のため、TypeScript の `interface` 宣言ではなく `type` エイリアスを使用してください。

```typescript
// ✅ Good
export type User = {
  id: number;
  name: string;
  email: string;
};

// ❌ Bad
export interface User {
  id: number;
  name: string;
  email: string;
}
```

### Separate type files

型定義は、コンポーネントやフック内でインラインで定義せず、別の `.ts` ファイルに分離してください。

- 共有される型: `frontend/src/types/` ディレクトリ
- 機能固有の型: `frontend/src/features/{feature}/types/` ディレクトリ

```typescript
// ✅ Good: frontend/src/features/auth/types/auth.ts
export type User = {
  id: number;
  name: string;
  email: string;
};

export type LoginFormValues = {
  email: string;
  password: string;
};

// ✅ Good: frontend/src/features/auth/hooks/use-login.ts
import type { LoginFormValues } from "../types/auth";

export const useLogin = () => {
  const [values, setValues] = useState<LoginFormValues>({
    email: "",
    password: "",
  });
  // ...
};
```

```typescript
// ❌ Bad: コンポーネント内で型を定義
export const LoginForm = () => {
  type FormValues = {
    email: string;
    password: string;
  };

  const [values, setValues] = useState<FormValues>({
    email: "",
    password: "",
  });
  // ...
};
```

**重要**: この規約は、フック内のローカル型エイリアスにも適用されます。

```typescript
// ❌ Bad: フック内で型を定義
export const useCreateNoteForm = () => {
  type FormValues = CreateNoteRequest; // これもNG！

  const [values, setValues] = useState<FormValues>({...});
  // ...
};

// ✅ Good: 型を分離ファイルに定義
// frontend/src/features/notes/types/note.ts
export type CreateNoteFormValues = CreateNoteRequest;

// frontend/src/features/notes/hooks/use-create-note-form.ts
import type { CreateNoteFormValues } from "../types/note";

export const useCreateNoteForm = () => {
  const [values, setValues] = useState<CreateNoteFormValues>({...});
  // ...
};
```

## Function Declarations

### Use arrow functions

一貫性のため、`function` 宣言ではなくアロー関数構文を使用してください。

```typescript
// ✅ Good: Arrow function
export const AuthProvider = ({ children }: PropsWithChildren) => {
  // ...
};

const getInitialState = () => {
  // ...
};

// ❌ Bad: Function declaration
export function AuthProvider({ children }: PropsWithChildren) {
  // ...
}

function getInitialState() {
  // ...
}
```

### Page components

Next.js のページコンポーネントもアロー関数で定義し、名前付きエクスポートしてから default export してください。

```typescript
// ✅ Good
export const NotePage = ({ params }: PageParams) => {
  // ...
};

export default NotePage;

// ❌ Bad
export default function NotePage({ params }: PageParams) {
  // ...
}
```

## File Extensions

ファイル拡張子は、内容に応じて適切に使い分けてください。

- **`.ts`**: JSX を含まないファイル（hooks、utilities、types など）
- **`.tsx`**: JSX を含むファイル（React コンポーネント）

```
frontend/src/features/notes/
├── api/
│   ├── get-notes.ts          # ✅ .ts (JSXなし)
│   └── create-note.ts         # ✅ .ts (JSXなし)
├── components/
│   ├── note-list.tsx          # ✅ .tsx (JSXあり)
│   └── create-note-form.tsx   # ✅ .tsx (JSXあり)
├── hooks/
│   └── use-create-note.ts     # ✅ .ts (JSXなし)
└── types/
    └── note.ts                # ✅ .ts (型定義のみ)
```

## Best Practices

### Named exports for components

コンポーネントは名前付きエクスポートを使用してください（ページコンポーネントを除く）。

```typescript
// ✅ Good
export const NoteList = () => {
  // ...
};

// ❌ Bad
const NoteList = () => {
  // ...
};
export default NoteList;
```

### Prop types

コンポーネントの props 型は、コンポーネント定義の直前ではなく、型定義ファイルに分離してください。

```typescript
// ❌ Bad: コンポーネントファイル内で定義
type NoteListProps = {
  notes: Note[];
  onSelect: (note: Note) => void;
};

export const NoteList = ({ notes, onSelect }: NoteListProps) => {
  // ...
};

// ✅ Good: 型定義ファイルに分離
// types/note.ts
export type NoteListProps = {
  notes: Note[];
  onSelect: (note: Note) => void;
};

// components/note-list.tsx
import type { NoteListProps } from "../types/note";

export const NoteList = ({ notes, onSelect }: NoteListProps) => {
  // ...
};
```

### Avoid inline type definitions

引数や戻り値の型をインラインで定義せず、型定義ファイルに分離してください。

```typescript
// ❌ Bad
export const useNoteForm = (): {
  values: { title: string; content: string };
  handleSubmit: () => void;
} => {
  // ...
};

// ✅ Good
// types/note.ts
export type NoteFormReturn = {
  values: { title: string; content: string };
  handleSubmit: () => void;
};

// hooks/use-note-form.ts
import type { NoteFormReturn } from "../types/note";

export const useNoteForm = (): NoteFormReturn => {
  // ...
};
```

## Error Handling

### Use Snackbar for user notifications

ユーザーに対する通知（成功、エラー、情報）は、必ず `useSnackbar` フックを使用してください。

```typescript
// ✅ Good: Snackbar を使用した通知
import { useSnackbar } from "@/hooks/use-snackbar";

export const useDeleteNote = () => {
  const { showSnackbar } = useSnackbar();

  const handleDelete = async (id: string) => {
    try {
      await deleteNoteApi(id);
      showSnackbar("ノートを削除しました!", "success");
    } catch (error) {
      console.error("Failed to delete note:", error);
      showSnackbar("ノートの削除に失敗しました", "error");
    }
  };

  return { handleDelete };
};

// ❌ Bad: Alert や console.log での通知
const handleDelete = async (id: string) => {
  try {
    await deleteNoteApi(id);
    alert("削除しました"); // NG
  } catch (error) {
    console.log("エラーが発生しました"); // NG
  }
};
```

### Snackbar severity types

`showSnackbar` の第2引数には、以下の severity を指定できます：

- `"success"`: 操作が成功した場合（作成、更新、削除など）
- `"error"`: エラーが発生した場合
- `"warning"`: 警告メッセージ
- `"info"`: 情報メッセージ（デフォルト）

```typescript
// 成功
showSnackbar("ノートを作成しました!", "success");

// エラー
showSnackbar("ノートの作成に失敗しました", "error");

// 警告
showSnackbar("一部のフィールドが未入力です", "warning");

// 情報
showSnackbar("データを読み込んでいます...", "info");
```

### Error handling in API calls

API 呼び出しでのエラーハンドリングは、以下のパターンに従ってください：

```typescript
// ✅ Good: try-catch でエラーをキャッチし、ユーザーに通知
const handleSubmit = async () => {
  try {
    await createNoteApi(values);
    showSnackbar("ノートを作成しました!", "success");
    router.push("/notes");
  } catch (error) {
    console.error("Failed to create note:", error);
    showSnackbar("ノートの作成に失敗しました", "error");
  }
};

// ✅ Good: React Query の onError コールバックを使用
const mutation = useMutation({
  mutationFn: createNoteApi,
  onSuccess: () => {
    showSnackbar("ノートを作成しました!", "success");
    queryClient.invalidateQueries({ queryKey: ["notes"] });
  },
  onError: (error) => {
    console.error("Failed to create note:", error);
    showSnackbar("ノートの作成に失敗しました", "error");
  },
});
```

### Console logging

- エラーは必ず `console.error` を使用してコンソールに記録してください
- ユーザーには `showSnackbar` で分かりやすいメッセージを表示してください
- 開発者向けの詳細なエラー情報は console に、ユーザー向けの簡潔なメッセージは Snackbar に表示します

```typescript
// ✅ Good
catch (error) {
  console.error("Failed to delete note:", error); // 開発者向け
  showSnackbar("ノートの削除に失敗しました", "error"); // ユーザー向け
}

// ❌ Bad: ユーザーへの通知がない
catch (error) {
  console.error("Failed to delete note:", error);
}

// ❌ Bad: console.log を使用
catch (error) {
  console.log(error); // console.error を使うべき
  showSnackbar("エラーが発生しました", "error");
}
```

## Summary

- ✅ `type` を使用、`interface` は使わない
- ✅ 型定義は必ず別ファイルに分離（コンポーネント、フック内での定義は禁止）
- ✅ アロー関数を使用、`function` 宣言は使わない
- ✅ ファイル拡張子は適切に使い分け（`.ts` / `.tsx`）
- ✅ コンポーネントは名前付きエクスポート（ページ以外）
- ✅ ユーザー通知は `useSnackbar` を使用
- ✅ エラーは `console.error` で記録し、ユーザーには Snackbar で通知

これらの規約に従うことで、コードベースの一貫性と保守性が向上します。
