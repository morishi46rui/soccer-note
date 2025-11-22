# 管理機能 API 設計書

## 概要

このドキュメントは、Soccer Note アプリケーションの管理者向け API 設計を定義します。
管理者は以下の機能を利用できます:

-   ダッシュボード統計情報の取得
-   ユーザー管理（CRUD 操作）
-   ロール・権限管理（CRUD 操作）
-   システム設定
-   活動ログの閲覧

## 認証・認可

### 認証方式

-   Laravel Sanctum によるトークン認証
-   すべての管理 API エンドポイントは認証必須

### 認可

-   システム管理者（system_admin）ロールを持つユーザーのみアクセス可能
-   一般ユーザー（チーム管理者/コーチ/選手）は管理 API にアクセス不可

## API エンドポイント設計

### 1. ダッシュボード統計 API

#### 1.1 統計情報取得

**エンドポイント**: `GET /api/v1/admin/dashboard/stats`

**概要**: システム全体の統計情報を取得

**レスポンス**:

```json
{
    "users": {
        "total": 150,
        "active": 120,
        "new_this_month": 15
    },
    "teams": {
        "total": 25,
        "active": 20,
        "new_this_month": 3
    },
    "groups": {
        "total": 80,
        "new_this_month": 8
    },
    "notes": {
        "total": 1200,
        "new_this_month": 180,
        "new_this_week": 45
    }
}
```

#### 1.2 最近の活動取得

**エンドポイント**: `GET /api/v1/admin/dashboard/activities`

**概要**: システム全体の最近の活動履歴を取得

**クエリパラメータ**:

-   `limit`: 取得件数（デフォルト: 20, 最大: 100）
-   `offset`: オフセット（デフォルト: 0）

**レスポンス**:

```json
{
    "data": [
        {
            "id": 1,
            "type": "user_created",
            "description": "新規ユーザーが登録されました: 山田太郎",
            "user_id": 123,
            "user_name": "山田太郎",
            "metadata": {
                "email": "yamada@example.com"
            },
            "created_at": "2025-01-15T10:30:00Z"
        },
        {
            "id": 2,
            "type": "team_created",
            "description": "新規チームが作成されました: FCトーキョー",
            "user_id": 45,
            "user_name": "佐藤花子",
            "metadata": {
                "team_id": 10,
                "team_name": "FCトーキョー"
            },
            "created_at": "2025-01-15T09:15:00Z"
        }
    ],
    "meta": {
        "total": 500,
        "limit": 20,
        "offset": 0
    }
}
```

**活動タイプ**:

-   `user_created`: ユーザー登録
-   `user_updated`: ユーザー情報更新
-   `user_deleted`: ユーザー削除
-   `team_created`: チーム作成
-   `team_updated`: チーム更新
-   `team_deleted`: チーム削除
-   `group_created`: グループ作成
-   `group_updated`: グループ更新
-   `group_deleted`: グループ削除
-   `note_created`: ノート作成
-   `role_assigned`: ロール割り当て
-   `permission_changed`: 権限変更

---

### 2. ユーザー管理 API

#### 2.1 ユーザー一覧取得

**エンドポイント**: `GET /api/v1/admin/users`

**概要**: 全ユーザーの一覧を取得（ページネーション対応）

**クエリパラメータ**:

-   `page`: ページ番号（デフォルト: 1）
-   `per_page`: 1 ページあたりの件数（デフォルト: 20, 最大: 100）
-   `search`: 検索キーワード（名前、メールアドレスで検索）
-   `sort`: ソート項目（`created_at`, `name`, `email`）
-   `order`: ソート順（`asc`, `desc`）デフォルト: `desc`
-   `status`: ステータスフィルタ（`active`, `deleted`）

**レスポンス**:

```json
{
    "data": [
        {
            "id": 1,
            "sqid": "xYz34WvU",
            "name": "山田太郎",
            "email": "yamada@example.com",
            "email_verified_at": "2025-01-10T12:00:00Z",
            "created_at": "2025-01-10T10:00:00Z",
            "updated_at": "2025-01-10T10:00:00Z",
            "deleted_at": null,
            "teams_count": 2,
            "notes_count": 15
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "last_page": 8
    }
}
```

#### 2.2 ユーザー詳細取得

**エンドポイント**: `GET /api/v1/admin/users/{sqid}`

**概要**: 特定ユーザーの詳細情報を取得

**レスポンス**:

```json
{
    "id": 1,
    "sqid": "xYz34WvU",
    "name": "山田太郎",
    "email": "yamada@example.com",
    "email_verified_at": "2025-01-10T12:00:00Z",
    "created_at": "2025-01-10T10:00:00Z",
    "updated_at": "2025-01-10T10:00:00Z",
    "deleted_at": null,
    "teams": [
        {
            "id": 1,
            "sqid": "aBc12DeF",
            "name": "FCトーキョー",
            "role": {
                "id": 1,
                "name": "admin",
                "display_name": "管理者"
            }
        }
    ],
    "groups": [
        {
            "id": 5,
            "sqid": "gHi78JkL",
            "name": "Aチーム",
            "team_name": "FCトーキョー"
        }
    ],
    "notes_count": 15,
    "last_login_at": "2025-01-15T08:00:00Z"
}
```

#### 2.3 ユーザー作成

**エンドポイント**: `POST /api/v1/admin/users`

**概要**: 新規ユーザーを作成

**リクエストボディ**:

```json
{
    "name": "山田太郎",
    "email": "yamada@example.com",
    "password": "SecurePassword123!",
    "send_welcome_email": true
}
```

**レスポンス**:

```json
{
    "id": 1,
    "sqid": "xYz34WvU",
    "name": "山田太郎",
    "email": "yamada@example.com",
    "created_at": "2025-01-15T10:00:00Z"
}
```

#### 2.4 ユーザー更新

**エンドポイント**: `PUT /api/v1/admin/users/{sqid}`

**概要**: ユーザー情報を更新

**リクエストボディ**:

```json
{
    "name": "山田太郎",
    "email": "yamada@example.com",
    "password": "NewPassword123!" // オプション
}
```

**レスポンス**:

```json
{
    "id": 1,
    "sqid": "xYz34WvU",
    "name": "山田太郎",
    "email": "yamada@example.com",
    "updated_at": "2025-01-15T11:00:00Z"
}
```

#### 2.5 ユーザー削除（論理削除）

**エンドポイント**: `DELETE /api/v1/admin/users/{sqid}`

**概要**: ユーザーを論理削除

**レスポンス**:

```json
{
    "message": "ユーザーを削除しました",
    "deleted_at": "2025-01-15T12:00:00Z"
}
```

#### 2.6 ユーザー復元

**エンドポイント**: `POST /api/v1/admin/users/{sqid}/restore`

**概要**: 論理削除されたユーザーを復元

**レスポンス**:

```json
{
    "message": "ユーザーを復元しました",
    "id": 1,
    "sqid": "xYz34WvU",
    "name": "山田太郎"
}
```

---

### 3. ロール・権限管理 API

#### 3.1 ロール一覧取得

**エンドポイント**: `GET /api/v1/admin/roles`

**概要**: 全ロールの一覧を取得

**クエリパラメータ**:

-   `include_permissions`: 権限情報を含めるか（`true` / `false`）デフォルト: `false`

**レスポンス**:

```json
{
    "data": [
        {
            "id": 1,
            "name": "admin",
            "display_name": "管理者",
            "description": "チームの管理者",
            "users_count": 25,
            "permissions": [
                {
                    "id": 1,
                    "name": "view_notes",
                    "display_name": "ノート閲覧",
                    "description": "ノートを閲覧できる"
                }
            ]
        }
    ]
}
```

#### 3.2 ロール詳細取得

**エンドポイント**: `GET /api/v1/admin/roles/{id}`

**概要**: 特定ロールの詳細情報を取得

**レスポンス**:

```json
{
    "id": 1,
    "name": "admin",
    "display_name": "管理者",
    "description": "チームの管理者",
    "users_count": 25,
    "permissions": [
        {
            "id": 1,
            "name": "view_notes",
            "display_name": "ノート閲覧",
            "description": "ノートを閲覧できる"
        }
    ],
    "created_at": "2025-01-01T00:00:00Z",
    "updated_at": "2025-01-15T10:00:00Z"
}
```

#### 3.3 ロール作成

**エンドポイント**: `POST /api/v1/admin/roles`

**概要**: 新規ロールを作成

**リクエストボディ**:

```json
{
    "name": "manager",
    "display_name": "マネージャー",
    "description": "チームのマネージャー",
    "permission_ids": [1, 2, 3, 5, 7]
}
```

**レスポンス**:

```json
{
    "id": 4,
    "name": "manager",
    "display_name": "マネージャー",
    "description": "チームのマネージャー",
    "created_at": "2025-01-15T10:00:00Z"
}
```

#### 3.4 ロール更新

**エンドポイント**: `PUT /api/v1/admin/roles/{id}`

**概要**: ロール情報を更新

**リクエストボディ**:

```json
{
    "display_name": "マネージャー",
    "description": "チームのマネージャー",
    "permission_ids": [1, 2, 3, 5, 7, 8]
}
```

**レスポンス**:

```json
{
    "id": 4,
    "name": "manager",
    "display_name": "マネージャー",
    "description": "チームのマネージャー",
    "updated_at": "2025-01-15T11:00:00Z"
}
```

#### 3.5 ロール削除

**エンドポイント**: `DELETE /api/v1/admin/roles/{id}`

**概要**: ロールを削除（使用中のロールは削除不可）

**レスポンス**:

```json
{
    "message": "ロールを削除しました"
}
```

#### 3.6 権限一覧取得

**エンドポイント**: `GET /api/v1/admin/permissions`

**概要**: 全権限の一覧を取得

**レスポンス**:

```json
{
    "data": [
        {
            "id": 1,
            "name": "view_notes",
            "display_name": "ノート閲覧",
            "description": "ノートを閲覧できる",
            "category": "note"
        },
        {
            "id": 2,
            "name": "create_notes",
            "display_name": "ノート作成",
            "description": "ノートを作成できる",
            "category": "note"
        }
    ]
}
```

**権限カテゴリー**:

-   `note`: ノート関連
-   `team`: チーム関連
-   `group`: グループ関連
-   `member`: メンバー関連

---

### 4. システム設定 API

#### 4.1 システム設定取得

**エンドポイント**: `GET /api/v1/admin/settings`

**概要**: システム設定を取得

**レスポンス**:

```json
{
    "app_name": "Soccer Note",
    "app_url": "https://soccer-note.example.com",
    "max_team_members": 50,
    "max_notes_per_user": 1000,
    "allow_public_registration": true,
    "maintenance_mode": false,
    "version": "1.0.0"
}
```

#### 4.2 システム設定更新

**エンドポイント**: `PUT /api/v1/admin/settings`

**概要**: システム設定を更新

**リクエストボディ**:

```json
{
    "app_name": "Soccer Note",
    "max_team_members": 50,
    "max_notes_per_user": 1000,
    "allow_public_registration": true,
    "maintenance_mode": false
}
```

**レスポンス**:

```json
{
    "message": "システム設定を更新しました",
    "updated_at": "2025-01-15T12:00:00Z"
}
```

---

## データベース設計

### 新規テーブル

#### activity_logs テーブル

システムの活動ログを記録

| カラム名    | 型           | NULL | 説明                 |
| ----------- | ------------ | ---- | -------------------- |
| id          | BIGINT       | NO   | 主キー               |
| type        | VARCHAR(50)  | NO   | 活動タイプ           |
| description | TEXT         | NO   | 説明                 |
| user_id     | BIGINT       | YES  | 実行ユーザー ID      |
| target_type | VARCHAR(100) | YES  | 対象のモデルタイプ   |
| target_id   | BIGINT       | YES  | 対象の ID            |
| metadata    | JSON         | YES  | 追加メタデータ       |
| ip_address  | VARCHAR(45)  | YES  | IP アドレス          |
| user_agent  | TEXT         | YES  | ユーザーエージェント |
| created_at  | TIMESTAMP    | NO   | 作成日時             |

**インデックス**:

-   `user_id`
-   `type`
-   `created_at`
-   `target_type, target_id`

#### system_settings テーブル

システム設定を保存

| カラム名    | 型           | NULL | 説明                                       |
| ----------- | ------------ | ---- | ------------------------------------------ |
| id          | BIGINT       | NO   | 主キー                                     |
| key         | VARCHAR(100) | NO   | 設定キー（ユニーク）                       |
| value       | TEXT         | YES  | 設定値                                     |
| type        | VARCHAR(20)  | NO   | データ型（string, integer, boolean, json） |
| description | TEXT         | YES  | 説明                                       |
| created_at  | TIMESTAMP    | NO   | 作成日時                                   |
| updated_at  | TIMESTAMP    | NO   | 更新日時                                   |

**インデックス**:

-   `key` (UNIQUE)

### 既存テーブルの拡張

#### users テーブル

追加カラム:

-   `last_login_at`: 最終ログイン日時（TIMESTAMP, NULL）
-   `is_system_admin`: システム管理者フラグ（BOOLEAN, デフォルト: false）

---

## 実装優先順位

### Phase 1（最優先）

1. ダッシュボード統計 API
    - `GET /api/v1/admin/dashboard/stats`
2. ユーザー一覧・詳細 API
    - `GET /api/v1/admin/users`
    - `GET /api/v1/admin/users/{sqid}`

### Phase 2

3. ユーザー管理 API（CRUD）
    - `POST /api/v1/admin/users`
    - `PUT /api/v1/admin/users/{sqid}`
    - `DELETE /api/v1/admin/users/{sqid}`
4. 活動ログ API
    - `GET /api/v1/admin/dashboard/activities`
    - ActivityLog モデル・マイグレーション作成

### Phase 3

5. ロール・権限管理 API
    - `GET /api/v1/admin/roles`
    - `GET /api/v1/admin/permissions`
    - `POST /api/v1/admin/roles`
    - `PUT /api/v1/admin/roles/{id}`
    - `DELETE /api/v1/admin/roles/{id}`

### Phase 4

6. システム設定 API
    - `GET /api/v1/admin/settings`
    - `PUT /api/v1/admin/settings`
    - SystemSetting モデル・マイグレーション作成

---

## セキュリティ考慮事項

### 認可チェック

-   すべてのエンドポイントで `is_system_admin` フラグをチェック
-   Middleware で一元管理（`EnsureSystemAdmin` ミドルウェア作成）

### ログ記録

-   すべての管理操作を `activity_logs` に記録
-   IP アドレス、ユーザーエージェント、実行内容を保存

### バリデーション

-   入力値の厳格なバリデーション
-   SQL インジェクション、XSS 対策
-   FormRequest を使用した検証

### パスワードポリシー

-   最小 8 文字
-   英数字+記号の組み合わせ
-   既存のパスワードポリシーに準拠

---

## エラーレスポンス

### 401 Unauthorized

```json
{
    "message": "認証に失敗しました"
}
```

### 403 Forbidden

```json
{
    "message": "この操作を実行する権限がありません"
}
```

### 404 Not Found

```json
{
    "message": "リソースが見つかりません"
}
```

### 422 Unprocessable Entity

```json
{
    "message": "バリデーションエラーが発生しました",
    "errors": {
        "email": ["メールアドレスの形式が正しくありません"]
    }
}
```

### 500 Internal Server Error

```json
{
    "message": "サーバーエラーが発生しました"
}
```

---

## テスト方針

### 単体テスト

-   各コントローラーのメソッドごとにテストケース作成
-   FormRequest のバリデーションテスト
-   モデルのリレーション・スコープテスト

### 統合テスト

-   API エンドポイントの E2E テスト
-   認証・認可のテスト
-   ページネーション、ソート、フィルタリングのテスト

### パフォーマンステスト

-   大量データでのページネーション性能確認
-   N+1 クエリ問題の検出と解決

---

## 備考

-   すべての API レスポンスは JSON 形式
-   日時は ISO 8601 形式（UTC）
-   ページネーションは Laravel の標準形式を使用
-   OpenAPI（Swagger）ドキュメントを自動生成
