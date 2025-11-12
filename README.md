# Soccer Note

Next.js + Laravel ベースのサッカーノートアプリケーションです。

## 技術スタック

### フロントエンド

- **Next.js 15** (App Router)
- **React 19** + TypeScript
- **Material-UI (MUI)**
- **React Query** (@tanstack/react-query)
- **TailwindCSS**

### バックエンド

- **Laravel 12**
- **PostgreSQL 16**
- **Laravel Sanctum** (API 認証)
- **Swagger/OpenAPI** (API 仕様書)

## アクセス URL

- フロントエンド: http://localhost:3000
- バックエンド API: http://localhost:8000
- Swagger UI: http://localhost:8000/api/documentation

## 必要な環境

- Docker
- Docker Compose
- Make（オプション: コマンドを簡略化）

## クイックスタート（Make を使用）

### 初回セットアップ

```bash
# リポジトリのクローン
git clone <repository-url>
cd soccer-note

# 環境構築（これだけでOK！）
make init
```

`make init`コマンドで以下の処理が自動実行されます:

- Docker コンテナの起動
- Composer 依存関係のインストール
- NPM 依存関係のインストール
- アプリケーションキーの生成
- データベースマイグレーション
- フロントエンドのビルド

### よく使う Make コマンド

```bash
make up          # コンテナを起動
make down        # コンテナを停止
make restart     # コンテナを再起動
make logs        # ログを表示
make shell       # appコンテナのシェルに入る
make test        # テストを実行
make help        # 全てのコマンドを表示
```

## 従来のセットアップ方法（Make を使わない場合）

### 1. リポジトリのクローン

```bash
git clone <repository-url>
cd soccer-note
```

### 2. Docker コンテナの起動

```bash
docker-compose up -d
```

### 3. 依存関係のインストール

```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 4. アプリケーションキーの生成

```bash
docker-compose exec app php artisan key:generate
```

### 5. データベースのマイグレーション

```bash
docker-compose exec app php artisan migrate
```

### 6. フロントエンドの起動

フロントエンドは別コンテナで自動起動します:

```bash
# フロントエンドのログを確認
docker-compose logs -f frontend
```

Next.js の開発サーバーが http://localhost:3000 で起動します。

## プロジェクト構造

```
soccer-note/
├── backend/              # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/Api/V1/  # APIコントローラー
│   │   ├── Models/
│   │   └── UseCase/      # ビジネスロジック
│   ├── routes/
│   │   └── api.php       # APIルーティング（自動生成）
│   └── storage/
│       └── api-docs/     # OpenAPI仕様書
├── frontend/             # Next.js App
│   ├── src/
│   │   ├── app/          # App Router
│   │   ├── features/     # 機能別コンポーネント
│   │   │   └── auth/
│   │   │       └── login/
│   │   │           ├── api/         # API呼び出し + React Query
│   │   │           ├── components/  # UIコンポーネント
│   │   │           ├── hooks/       # カスタムフック
│   │   │           └── types/       # 型定義
│   │   ├── lib/          # ユーティリティ
│   │   │   └── api-client.ts  # APIクライアント
│   │   └── types/
│   │       └── api.ts    # OpenAPIから自動生成される型
│   └── Dockerfile
├── docker/               # Docker設定
├── scripts/
│   └── generate-routes.php  # ルーティング自動生成
└── compose.yml           # Docker Compose設定
```

## アクセス

アプリケーションは以下の URL でアクセスできます:

- **フロントエンド**: http://localhost:3000 (Next.js)
- **バックエンド API**: http://localhost:8000 (Laravel)
- **Swagger UI**: http://localhost:8000/api/documentation
- **データベース**: localhost:5432 (PostgreSQL)

### データベース接続情報

- ホスト: localhost
- ポート: 5432
- データベース名: backend
- ユーザー名: postgres
- パスワード: postgres

## 開発

### Make コマンド一覧

| コマンド             | 説明                            |
| -------------------- | ------------------------------- |
| `make help`          | 利用可能なコマンド一覧を表示    |
| `make init`          | 初回環境構築                    |
| `make up`            | コンテナを起動                  |
| `make down`          | コンテナを停止                  |
| `make restart`       | コンテナを再起動                |
| `make build`         | コンテナを再ビルド              |
| `make logs`          | 全コンテナのログを表示          |
| `make frontend-logs` | フロントエンドのログを表示      |
| `make ps`            | コンテナの状態を表示            |
| `make clean`         | コンテナとボリュームを完全削除  |
| `make install`       | 依存関係をインストール          |
| `make migrate`       | マイグレーション実行            |
| `make seed`          | シーダー実行                    |
| `make fresh`         | データベースをリフレッシュ      |
| `make test`          | テスト実行                      |
| `make shell`         | app コンテナのシェルに入る      |
| `make bashf`         | frontend コンテナのシェルに入る |
| `make db-shell`      | PostgreSQL に接続               |
| `make api`           | API 関連ファイルを自動生成      |
| `make swagger`       | Swagger ドキュメント生成        |

### API 開発ワークフロー

新しい API エンドポイントを追加する場合:

1. `backend/app/Http/Controllers/Api/V1/` にコントローラーを作成
2. OpenAPI 属性を使ってドキュメント化
3. `make api` を実行して自動生成:
   - ルーティング (`backend/routes/api.php`)
   - Swagger ドキュメント
   - TypeScript 型定義 (`frontend/src/types/api.ts`)

```bash
make api
```

### カスタムコマンド実行

```bash
# Artisanコマンド
make artisan cmd="route:list"

# Composerコマンド
make composer cmd="require package-name"

# NPMコマンド
make npm cmd="install package-name"
```

### Docker Compose コマンド（Make を使わない場合）

起動:

```bash
docker-compose up -d
```

停止:

```bash
docker-compose down
```

完全に削除(データベースを含む):

```bash
docker-compose down -v
```

ログの確認:

```bash
docker-compose logs -f
```

特定のサービスのログ:

```bash
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

コンテナ内でのコマンド実行:

```bash
# Artisanコマンド
docker-compose exec app php artisan <command>

# Composerコマンド
docker-compose exec app composer <command>

# NPMコマンド
docker-compose exec app npm <command>

# bashシェルに入る
docker-compose exec app bash
```

## トラブルシューティング

### パーミッションエラーが発生する場合

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
```

### データベース接続エラーが発生する場合

1. データベースコンテナが起動しているか確認:

```bash
docker-compose ps
```

2. データベースの接続情報が正しいか`.env`ファイルを確認
3. コンテナを再起動:

```bash
docker-compose restart
```

## サービス構成

- **app**: Laravel PHP アプリケーション (PHP 8.2-fpm)
- **nginx**: Web サーバー (Nginx) - ポート 8000
- **frontend**: Next.js アプリケーション (Node.js 20) - ポート 3000
- **db**: PostgreSQL データベース (PostgreSQL 16) - ポート 5432

## 主な機能

### 認証機能

- ログイン (Laravel Sanctum + React Query)
- トークンベース認証
- ユーザー登録

### API 仕様書

- OpenAPI 3.0 準拠
- Swagger UI で閲覧可能
- TypeScript 型定義の自動生成

### 開発体験

- ホットリロード (Next.js + Laravel)
- React Query Devtools
- 型安全な API 呼び出し
- 自動ルーティング生成
