# データベース設計書

## 概要

Soccer Note アプリケーションのデータベース設計ドキュメント。
チーム > グループ > ユーザーの階層構造を基本とし、柔軟な権限管理を実現する。

## エンティティ構造

```
チーム (Team)
└─ グループ (Group)
   └─ ユーザー (User)
```

ユーザーは複数のチームやグループに所属可能。
ノートは個人、チーム、グループのいずれにも紐づけ可能。

## テーブル定義

### 基本エンティティ

#### teams (チーム)

| カラム名    | 型           | NULL | デフォルト     | 説明         |
| ----------- | ------------ | ---- | -------------- | ------------ |
| id          | bigint       | NO   | AUTO_INCREMENT | 主キー       |
| name        | varchar(255) | NO   | -              | チーム名     |
| description | text         | YES  | NULL           | チームの説明 |
| created_at  | timestamp    | YES  | NULL           | 作成日時     |
| updated_at  | timestamp    | YES  | NULL           | 更新日時     |

**インデックス**

-   PRIMARY KEY: `id`

---

#### groups (グループ)

| カラム名    | 型           | NULL | デフォルト     | 説明                 |
| ----------- | ------------ | ---- | -------------- | -------------------- |
| id          | bigint       | NO   | AUTO_INCREMENT | 主キー               |
| team_id     | bigint       | NO   | -              | チーム ID (外部キー) |
| name        | varchar(255) | NO   | -              | グループ名           |
| description | text         | YES  | NULL           | グループの説明       |
| created_at  | timestamp    | YES  | NULL           | 作成日時             |
| updated_at  | timestamp    | YES  | NULL           | 更新日時             |

**インデックス**

-   PRIMARY KEY: `id`
-   FOREIGN KEY: `team_id` REFERENCES `teams(id)` ON DELETE CASCADE
-   INDEX: `team_id`

---

#### users (ユーザー)

既存テーブルを使用。必要に応じて拡張。

| カラム名          | 型           | NULL | デフォルト     | 説明                   |
| ----------------- | ------------ | ---- | -------------- | ---------------------- |
| id                | bigint       | NO   | AUTO_INCREMENT | 主キー                 |
| name              | varchar(255) | NO   | -              | ユーザー名             |
| email             | varchar(255) | NO   | -              | メールアドレス         |
| email_verified_at | timestamp    | YES  | NULL           | メール確認日時         |
| password          | varchar(255) | NO   | -              | パスワード(ハッシュ化) |
| remember_token    | varchar(100) | YES  | NULL           | ログイン保持トークン   |
| created_at        | timestamp    | YES  | NULL           | 作成日時               |
| updated_at        | timestamp    | YES  | NULL           | 更新日時               |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `email`

---

### 権限管理

#### roles (ロール定義)

| カラム名     | 型           | NULL | デフォルト     | 説明                                                     |
| ------------ | ------------ | ---- | -------------- | -------------------------------------------------------- |
| id           | bigint       | NO   | AUTO_INCREMENT | 主キー                                                   |
| name         | varchar(50)  | NO   | -              | ロール識別子 (例: 'player', 'coach', 'manager', 'admin') |
| display_name | varchar(255) | NO   | -              | 表示名 (例: '選手', 'コーチ', 'マネージャー', '管理者')  |
| description  | text         | YES  | NULL           | ロールの説明                                             |
| created_at   | timestamp    | YES  | NULL           | 作成日時                                                 |
| updated_at   | timestamp    | YES  | NULL           | 更新日時                                                 |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `name`

**初期データ例**

-   admin: 管理者
-   player: 選手
-   coach: コーチ

---

#### permissions (権限定義)

| カラム名     | 型           | NULL | デフォルト     | 説明                                        |
| ------------ | ------------ | ---- | -------------- | ------------------------------------------- |
| id           | bigint       | NO   | AUTO_INCREMENT | 主キー                                      |
| name         | varchar(50)  | NO   | -              | 権限識別子 (例: 'view_notes', 'edit_notes') |
| display_name | varchar(255) | NO   | -              | 表示名 (例: 'ノート閲覧', 'ノート編集')     |
| description  | text         | YES  | NULL           | 権限の説明                                  |
| created_at   | timestamp    | YES  | NULL           | 作成日時                                    |
| updated_at   | timestamp    | YES  | NULL           | 更新日時                                    |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `name`

**初期データ例**

-   view_notes: ノート閲覧
-   edit_notes: ノート編集
-   delete_notes: ノート削除
-   manage_team: チーム管理
-   manage_group: グループ管理
-   manage_members: メンバー管理

---

#### role_permissions (ロールと権限の紐付け)

| カラム名      | 型     | NULL | デフォルト | 説明                 |
| ------------- | ------ | ---- | ---------- | -------------------- |
| role_id       | bigint | NO   | -          | ロール ID (外部キー) |
| permission_id | bigint | NO   | -          | 権限 ID (外部キー)   |

**インデックス**

-   PRIMARY KEY: `(role_id, permission_id)`
-   FOREIGN KEY: `role_id` REFERENCES `roles(id)` ON DELETE CASCADE
-   FOREIGN KEY: `permission_id` REFERENCES `permissions(id)` ON DELETE CASCADE
-   INDEX: `role_id`
-   INDEX: `permission_id`

---

### 関連テーブル (中間テーブル)

#### team_user (チームとユーザーの関係)

| カラム名   | 型        | NULL | デフォルト     | 説明                   |
| ---------- | --------- | ---- | -------------- | ---------------------- |
| id         | bigint    | NO   | AUTO_INCREMENT | 主キー                 |
| team_id    | bigint    | NO   | -              | チーム ID (外部キー)   |
| user_id    | bigint    | NO   | -              | ユーザー ID (外部キー) |
| role_id    | bigint    | NO   | -              | ロール ID (外部キー)   |
| created_at | timestamp | YES  | NULL           | 作成日時               |
| updated_at | timestamp | YES  | NULL           | 更新日時               |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `(team_id, user_id)` (同じチームに同じユーザーは 1 回のみ所属)
-   FOREIGN KEY: `team_id` REFERENCES `teams(id)` ON DELETE CASCADE
-   FOREIGN KEY: `user_id` REFERENCES `users(id)` ON DELETE CASCADE
-   FOREIGN KEY: `role_id` REFERENCES `roles(id)` ON DELETE RESTRICT
-   INDEX: `team_id`
-   INDEX: `user_id`
-   INDEX: `role_id`

---

#### group_user (グループとユーザーの関係)

| カラム名   | 型        | NULL | デフォルト     | 説明                   |
| ---------- | --------- | ---- | -------------- | ---------------------- |
| id         | bigint    | NO   | AUTO_INCREMENT | 主キー                 |
| group_id   | bigint    | NO   | -              | グループ ID (外部キー) |
| user_id    | bigint    | NO   | -              | ユーザー ID (外部キー) |
| created_at | timestamp | YES  | NULL           | 作成日時               |
| updated_at | timestamp | YES  | NULL           | 更新日時               |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `(group_id, user_id)` (同じグループに同じユーザーは 1 回のみ所属)
-   FOREIGN KEY: `group_id` REFERENCES `groups(id)` ON DELETE CASCADE
-   FOREIGN KEY: `user_id` REFERENCES `users(id)` ON DELETE CASCADE
-   INDEX: `group_id`
-   INDEX: `user_id`

---

### ノート関連

#### notes (ノート)

既存テーブルを拡張し、各エンティティとの紐付けは専用の中間テーブルで管理する。

| カラム名   | 型           | NULL | デフォルト     | 説明                           |
| ---------- | ------------ | ---- | -------------- | ------------------------------ |
| id         | bigint       | NO   | AUTO_INCREMENT | 主キー                         |
| user_id    | bigint       | NO   | -              | 作成者のユーザー ID (外部キー) |
| title      | varchar(255) | NO   | -              | タイトル                       |
| content    | text         | NO   | -              | 内容                           |
| created_at | timestamp    | YES  | NULL           | 作成日時                       |
| updated_at | timestamp    | YES  | NULL           | 更新日時                       |

**インデックス**

-   PRIMARY KEY: `id`
-   FOREIGN KEY: `user_id` REFERENCES `users(id)` ON DELETE CASCADE
-   INDEX: `user_id`

---

#### user_notes (個人ノート)

個人用ノートと個人の紐付け。

| カラム名   | 型        | NULL | デフォルト     | 説明                   |
| ---------- | --------- | ---- | -------------- | ---------------------- |
| id         | bigint    | NO   | AUTO_INCREMENT | 主キー                 |
| note_id    | bigint    | NO   | -              | ノート ID (外部キー)   |
| user_id    | bigint    | NO   | -              | ユーザー ID (外部キー) |
| created_at | timestamp | YES  | NULL           | 作成日時               |
| updated_at | timestamp | YES  | NULL           | 更新日時               |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `note_id` (1 つのノートは 1 つの個人にのみ紐づく)
-   FOREIGN KEY: `note_id` REFERENCES `notes(id)` ON DELETE CASCADE
-   FOREIGN KEY: `user_id` REFERENCES `users(id)` ON DELETE CASCADE
-   INDEX: `note_id`
-   INDEX: `user_id`

---

#### team_notes (チームノート)

チーム用ノートとチームの紐付け。

| カラム名   | 型        | NULL | デフォルト     | 説明                 |
| ---------- | --------- | ---- | -------------- | -------------------- |
| id         | bigint    | NO   | AUTO_INCREMENT | 主キー               |
| note_id    | bigint    | NO   | -              | ノート ID (外部キー) |
| team_id    | bigint    | NO   | -              | チーム ID (外部キー) |
| created_at | timestamp | YES  | NULL           | 作成日時             |
| updated_at | timestamp | YES  | NULL           | 更新日時             |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `note_id` (1 つのノートは 1 つのチームにのみ紐づく)
-   FOREIGN KEY: `note_id` REFERENCES `notes(id)` ON DELETE CASCADE
-   FOREIGN KEY: `team_id` REFERENCES `teams(id)` ON DELETE CASCADE
-   INDEX: `note_id`
-   INDEX: `team_id`

---

#### group_notes (グループノート)

グループ用ノートとグループの紐付け。

| カラム名   | 型        | NULL | デフォルト     | 説明                   |
| ---------- | --------- | ---- | -------------- | ---------------------- |
| id         | bigint    | NO   | AUTO_INCREMENT | 主キー                 |
| note_id    | bigint    | NO   | -              | ノート ID (外部キー)   |
| group_id   | bigint    | NO   | -              | グループ ID (外部キー) |
| created_at | timestamp | YES  | NULL           | 作成日時               |
| updated_at | timestamp | YES  | NULL           | 更新日時               |

**インデックス**

-   PRIMARY KEY: `id`
-   UNIQUE: `note_id` (1 つのノートは 1 つのグループにのみ紐づく)
-   FOREIGN KEY: `note_id` REFERENCES `notes(id)` ON DELETE CASCADE
-   FOREIGN KEY: `group_id` REFERENCES `groups(id)` ON DELETE CASCADE
-   INDEX: `note_id`
-   INDEX: `group_id`

---

## ER 図

```
┌──────────┐
│  teams   │────────┐
└────┬─────┘        │
     │ 1            │
     │              │
     │ N            │
┌────┴─────┐       │         ┌─────────────┐
│  groups  │───┐   │         │ team_user   │
└────┬─────┘   │   │         └──────┬──────┘
     │ 1       │   │                │
     │         │   │          ┌─────┴──────┐
     │ N       │   │          │   roles    │
┌────┴──────┐ │   │          └──────┬─────┘
│group_user │ │   │                 │
└─────┬─────┘ │   │           ┌─────┴──────────┐
      │       │   │           │role_permissions│
      │       │   │           └──────┬─────────┘
      │       │   │                  │
┌─────┴─────┐ │   │           ┌──────┴──────┐
│   users   │─┼───┼───┐       │ permissions │
└─────┬─────┘ │   │   │       └─────────────┘
      │ 1     │   │   │
      │       │   │   │
      │ N     │   │   │
┌─────┴─────┐ │   │   │
│   notes   │ │   │   │
└─────┬─────┘ │   │   │
      │       │   │   │
      ├───────┼───┼───┘
      │       │   │
      │ 1     │   │
      │       │   │
      │ 1     │   │
┌─────┴──────┐│   │
│ user_notes ││   │
└────────────┘│   │
      │ 1     │ 1 │ 1
┌─────┴────────┴───┴───┐
│     team_notes       │
└──────────┬───────────┘
           │ 1
     ┌─────┴──────┐
     │group_notes │
     └────────────┘
```

## データの関係性

### 1 対多の関係

-   Team (1) → Groups (N)
-   User (1) → Notes (N) (作成者として)
-   Note (1) → UserNote (1) (個人ノートとして)
-   Note (1) → TeamNote (1) (チームノートとして)
-   Note (1) → GroupNote (1) (グループノートとして)

### 多対多の関係

-   Team (N) ↔ User (N) (team_user テーブル経由)
-   Group (N) ↔ User (N) (group_user テーブル経由)
-   Role (N) ↔ Permission (N) (role_permissions テーブル経由)

### 中間テーブルによる紐付け

-   User (N) ↔ Note (N) (user_notes テーブル経由、個人ノート)
-   Team (N) ↔ Note (N) (team_notes テーブル経由、チームノート)
-   Group (N) ↔ Note (N) (group_notes テーブル経由、グループノート)

**重要**: 1 つのノートは必ず 1 つのエンティティ (User, Team, Group のいずれか) に紐づく。
複数のエンティティに同時に紐づくことはない (note_id に UNIQUE 制約)

## 使用例

### ユースケース 1: ユーザーが所属するチーム一覧を取得

```php
$user = User::find(1);
$teams = $user->teams; // team_user テーブル経由
```

### ユースケース 2: チーム内のコーチ一覧を取得

```php
$team = Team::find(1);
$coaches = $team->users()->wherePivot('role_id', Role::where('name', 'coach')->first()->id)->get();
```

### ユースケース 3: ユーザーの権限チェック

```php
$user = User::find(1);
$team = Team::find(1);
$teamUser = TeamUser::where('user_id', $user->id)->where('team_id', $team->id)->first();
$role = $teamUser->role;
$hasPermission = $role->permissions->contains('name', 'edit_notes');
```

### ユースケース 4: チームノートの作成

```php
// ノートを作成
$note = Note::create([
    'user_id' => $userId,
    'title' => 'チーム練習メモ',
    'content' => '本日の練習内容...',
]);

// チームと紐付け
TeamNote::create([
    'note_id' => $note->id,
    'team_id' => $teamId,
]);
```

### ユースケース 5: チームに紐づくノート一覧を取得

```php
$team = Team::find(1);
$notes = Note::whereHas('teamNote', function($query) use ($team) {
    $query->where('team_id', $team->id);
})->get();

// またはリレーション経由で
$notes = $team->notes; // Team モデルに hasManyThrough リレーションを定義した場合
```

### ユースケース 6: ノートがどのエンティティに紐づいているか確認

```php
$note = Note::find(1);

if ($note->userNote) {
    echo '個人ノート: ' . $note->userNote->user->name;
} elseif ($note->teamNote) {
    echo 'チームノート: ' . $note->teamNote->team->name;
} elseif ($note->groupNote) {
    echo 'グループノート: ' . $note->groupNote->group->name;
}
```

## マイグレーション実行順序

1. `roles` テーブル
2. `permissions` テーブル
3. `role_permissions` テーブル
4. `teams` テーブル
5. `groups` テーブル
6. `team_user` テーブル
7. `group_user` テーブル
8. `notes` テーブルの拡張 (既存テーブルの場合は ALTER)
9. `user_notes` テーブル
10. `team_notes` テーブル
11. `group_notes` テーブル

## 注意事項

-   外部キー制約により、参照されているレコードは削除できない (role_id は ON DELETE RESTRICT)
-   チームやグループを削除すると、関連する中間テーブルのレコードも削除される (ON DELETE CASCADE)
-   ユーザーを削除すると、作成したノートも削除される
-   同じチーム/グループに同じユーザーは重複して所属できない (UNIQUE 制約)
