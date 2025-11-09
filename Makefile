.PHONY: help init up down re build logs ps clean clean-deps install migrate seed fresh test b db-shell npm-dev npm-build artisan composer npm copy-vendor f frontend-logs

# デフォルトのターゲット
.DEFAULT_GOAL := help

# ヘルプ
help:
	@echo "使用可能なコマンド:"
	@echo "  make init         - 初回環境構築（コンテナ起動・依存関係インストール・DB構築）"
	@echo "  make up           - コンテナを起動"
	@echo "  make down         - コンテナを停止"
	@echo "  make restart      - コンテナを再起動"
	@echo "  make build        - コンテナをビルドして起動"
	@echo "  make logs         - 全てのコンテナのログを表示"
	@echo "  make ps           - コンテナの状態を表示"
	@echo "  make clean        - コンテナとボリュームを完全削除"
	@echo "  make clean-deps   - ホストのnode_modulesとvendorを削除"
	@echo "  make install      - 依存関係をインストール（Composer & NPM）"
	@echo "  make migrate      - データベースマイグレーションを実行"
	@echo "  make seed         - シーダーを実行"
	@echo "  make fresh        - データベースをリフレッシュ（migrate:fresh）"
	@echo "  make test         - テストを実行"
	@echo "  make shell        - appコンテナのbashシェルに入る"
	@echo "  make db-shell     - PostgreSQLに接続"
	@echo "  make npm-dev      - npm run devを実行"
	@echo "  make npm-build    - npm run buildを実行"
	@echo "  make swagger      - Swaggerドキュメントを生成"

# 初回環境構築
init:
	@echo "==> 環境構築を開始します..."
	@make up
	@echo "==> アプリケーションキーを生成中..."
	@docker-compose exec app php artisan key:generate
	@echo "==> データベースマイグレーションを実行中..."
	@make migrate
	@echo "==> 環境構築が完了しました！"
	@echo "==> アプリケーションにアクセス: http://localhost:8000"

# コンテナ起動
up:
	@echo "==> コンテナを起動中..."
	@docker-compose up -d
	@echo "==> コンテナが起動しました"
	@make ps

# コンテナ停止
down:
	@echo "==> コンテナを停止中..."
	@docker-compose down
	@echo "==> コンテナが停止しました"

# コンテナ再起動
re:
	@echo "==> コンテナを再起動中..."
	@make down
	@make up

# コンテナビルド
build:
	@echo "==> コンテナをビルド中..."
	@docker-compose build --no-cache
	@make up

# ログ表示
logs:
	@docker-compose logs -f

# コンテナ状態表示
ps:
	@docker-compose ps

# 完全削除
clean:
	@echo "==> コンテナとボリュームを削除中..."
	@docker-compose down -v
	@echo "==> 削除が完了しました"

# ホストの依存関係を削除
clean-deps:
	@echo "==> ホストのnode_modulesとvendorを削除中..."
	@rm -rf backend/node_modules backend/vendor
	@echo "==> 削除が完了しました"

# 依存関係インストール
install:
	@echo "==> Composer依存関係をインストール中..."
	@docker-compose exec -u root app composer install
	@echo "==> NPM依存関係をインストール中..."
	@docker-compose exec -u root app npm install
	@echo "==> 依存関係のインストールが完了しました"

# マイグレーション
migrate:
	@echo "==> マイグレーションを実行中..."
	@docker-compose exec app php artisan migrate
	@echo "==> マイグレーションが完了しました"

# シーダー実行
seed:
	@echo "==> シーダーを実行中..."
	@docker-compose exec app php artisan db:seed
	@echo "==> シーダーが完了しました"

# データベースリフレッシュ
fresh:
	@echo "==> データベースをリフレッシュ中..."
	@docker-compose exec app php artisan migrate:fresh --seed
	@echo "==> データベースのリフレッシュが完了しました"

# テスト実行
test:
	@echo "==> テストを実行中..."
	@docker-compose exec app php artisan test
	@echo "==> テストが完了しました"

# シェルに入る
bashb:
	@docker-compose exec app bash

# データベースシェル
db-shell:
	@docker-compose exec db psql -U postgres -d backend

# NPM開発モード
npm-dev:
	@echo "==> NPM開発サーバーを起動中..."
	@docker-compose exec app npm run dev

# NPMビルド
npm-build:
	@echo "==> フロントエンドをビルド中..."
	@docker-compose exec app npm run build
	@echo "==> ビルドが完了しました"

# Artisanコマンド実行（使用例: make artisan cmd="route:list"）
artisan:
	@docker-compose exec app php artisan $(cmd)

# Composerコマンド実行（使用例: make composer cmd="require package"）
composer:
	@docker-compose exec -u root app composer $(cmd)
	@echo "==> composer.jsonとcomposer.lockをホストにコピー中..."
	@docker cp soccer-note-app:/var/www/html/composer.json ./backend/composer.json
	@docker cp soccer-note-app:/var/www/html/composer.lock ./backend/composer.lock
	@echo "==> コピーが完了しました"

# NPMコマンド実行（使用例: make npm cmd="install package"）
npm:
	@docker-compose exec app npm $(cmd)

# vendorをホストにコピー（IDE用）
copy-vendor:
	@echo "==> vendorをホストにコピー中..."
	@docker cp soccer-note-app:/var/www/html/vendor ./backend/vendor
	@echo "==> コピーが完了しました"

# フロントエンドシェル
bashf:
	@docker-compose exec frontend sh

# フロントエンドログ
frontend-logs:
	@docker-compose logs -f frontend

# Swaggerドキュメント生成
swagger:
	@echo "==> Swaggerドキュメントを生成中..."
	@docker-compose exec app php artisan l5-swagger:generate
	@echo "==> ドキュメントが生成されました"
	@echo "==> アクセス: http://localhost:8000/api/documentation"
