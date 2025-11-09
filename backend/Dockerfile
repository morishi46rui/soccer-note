FROM php:8.2-fpm

# ビルドキャッシュの削除を無効化
RUN rm -f /etc/apt/apt.conf.d/docker-clean; echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache

# 必要なライブラリのインストール
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    nodejs \
    npm \
    locales \
    && docker-php-ext-install mbstring exif pcntl bcmath gd pdo_pgsql pgsql zip

# 日本語設定
RUN echo "ja_JP.UTF-8 UTF-8" > /etc/locale.gen && locale-gen

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Laravelプロジェクトのファイルをコンテナにコピー
COPY . /var/www/html/

# .envファイルが存在しない場合は.env.exampleからコピー
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# rootユーザーでcomposerインストールを許可
ENV COMPOSER_ALLOW_SUPERUSER=1

# Composerを使ってLaravelプロジェクトの依存関係をインストール
RUN composer install --no-dev --optimize-autoloader

# NPMの依存関係をインストール
RUN npm install

# フロントエンドをビルド
RUN npm run build

# 権限の設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.env

# www-dataユーザーに切り替え
USER www-data

# ポート9000を公開
EXPOSE 9000

CMD ["php-fpm"]
