###########################################
# Build Assets & Dependencies
###########################################
FROM serversideup/php:8.4-cli AS builder

USER root

# Set working directory
WORKDIR /var/www/html

# Install Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Copy package files and install Node dependencies
COPY package*.json ./
RUN npm install

# Copy composer files and install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Copy application files and build frontend assets
COPY . .

ENV NODE_ENV=production
RUN npm run build

###########################################
# Production Image
###########################################
FROM serversideup/php:8.4-fpm-nginx

# Set working directory
WORKDIR /var/www/html

# Switch to root to install dependencies
USER root

# Install PostgreSQL client for health checks
RUN apt-get update && apt-get install -y \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# Copy composer dependencies from builder
COPY --from=builder --chown=www-data:www-data /var/www/html/vendor ./vendor

# Copy built frontend assets from builder
COPY --from=builder --chown=www-data:www-data /var/www/html/public/build ./public/build

# Copy application files
COPY --chown=www-data:www-data . .

# Create required directories and set permissions
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Switch back to www-data user
USER www-data
