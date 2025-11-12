FROM node:20-alpine

WORKDIR /app

# ソースコードをコピー
COPY . .

# 依存関係をインストール
RUN npm ci

# ポート3000を公開（Next.jsのデフォルトポート）
EXPOSE 3000

# 開発サーバーを起動
CMD ["npm", "run", "dev", "--", "--hostname", "0.0.0.0"]
