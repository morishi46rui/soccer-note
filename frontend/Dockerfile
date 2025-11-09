FROM node:20-alpine

WORKDIR /app

# package.jsonとpackage-lock.jsonをコピー
COPY package*.json ./

# 依存関係をインストール
RUN npm install

# ソースコードをコピー
COPY . .

# ポート5173を公開（Viteのデフォルトポート）
EXPOSE 5173

# 開発サーバーを起動（ホットリロード有効）
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
