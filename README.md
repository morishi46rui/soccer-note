# Soccer Note

Laravelベースのサッカーノートアプリケーションです。

## 必要な環境

- Docker
- Docker Compose
- Make（オプション: コマンドを簡略化）

## クイックスタート（Makeを使用）

### 初回セットアップ

```bash
# リポジトリのクローン
git clone <repository-url>
cd soccer-note

# 環境構築（これだけでOK！）
make init
```

`make init`コマンドで以下の処理が自動実行されます:
- Dockerコンテナの起動
- Composer依存関係のインストール
- NPM依存関係のインストール
- アプリケーションキーの生成
- データベースマイグレーション
- フロントエンドのビルド

### よく使うMakeコマンド

```bash
make up          # コンテナを起動
make down        # コンテナを停止
make restart     # コンテナを再起動
make logs        # ログを表示
make shell       # appコンテナのシェルに入る
make test        # テストを実行
make help        # 全てのコマンドを表示
```

## 従来のセットアップ方法（Makeを使わない場合）

### 1. リポジトリのクローン

```bash
git clone <repository-url>
cd soccer-note
```

### 2. Dockerコンテナの起動

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

### 6. フロントエンドのビルド

開発環境の場合:
```bash
docker-compose exec app npm run dev
```

本番環境の場合:
```bash
docker-compose exec app npm run build
```

## アクセス

アプリケーションは以下のURLでアクセスできます:

- アプリケーション: http://localhost:8000
- データベース: localhost:5432

### データベース接続情報

- ホスト: localhost
- ポート: 5432
- データベース名: backend
- ユーザー名: postgres
- パスワード: postgres

## 開発

### Makeコマンド一覧

| コマンド | 説明 |
|---------|------|
| `make help` | 利用可能なコマンド一覧を表示 |
| `make init` | 初回環境構築 |
| `make up` | コンテナを起動 |
| `make down` | コンテナを停止 |
| `make restart` | コンテナを再起動 |
| `make build` | コンテナを再ビルド |
| `make logs` | ログを表示 |
| `make ps` | コンテナの状態を表示 |
| `make clean` | コンテナとボリュームを完全削除 |
| `make install` | 依存関係をインストール |
| `make migrate` | マイグレーション実行 |
| `make seed` | シーダー実行 |
| `make fresh` | データベースをリフレッシュ |
| `make test` | テスト実行 |
| `make shell` | appコンテナのシェルに入る |
| `make db-shell` | PostgreSQLに接続 |
| `make npm-dev` | npm run dev を実行 |
| `make npm-build` | npm run build を実行 |

### カスタムコマンド実行

```bash
# Artisanコマンド
make artisan cmd="route:list"

# Composerコマンド
make composer cmd="require package-name"

# NPMコマンド
make npm cmd="install package-name"
```

### Docker Composeコマンド（Makeを使わない場合）

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

- **app**: Laravel PHPアプリケーション (PHP 8.2-fpm)
- **nginx**: Webサーバー (Nginx)
- **db**: PostgreSQLデータベース (PostgreSQL 16)
