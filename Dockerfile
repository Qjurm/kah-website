FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) pdo pdo_sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copy existing project
COPY . .

# Pull latest from GitHub
RUN git pull origin main || true

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
RUN composer run-script post-autoload-dump || true

ARG APP_URL=https://kah.qjurm.dev
ENV APP_URL=${APP_URL}
ENV ASSET_URL=${APP_URL}

# Install Node dependencies & build assets
RUN npm install
RUN npm run build

# Set up .env if not present
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate app key
RUN php artisan key:generate --force || true

# Ensure full storage directory structure
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Run migrations + seed
RUN php artisan migrate --force || true
RUN php artisan db:seed --force || true

# Storage link
RUN php artisan storage:link || true

# Writable permissions
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
