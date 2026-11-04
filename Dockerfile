FROM php:8.2-cli-alpine

# Install SQLite PDO extension for lightweight zero-config persistence
RUN apk add --no-cache sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite

WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Ensure database directory is writable for SQLite database operations
RUN chmod -R 777 /var/www/html/database

ENV PORT=8080
EXPOSE 8080
EXPOSE 10000

# Start PHP built-in web server binding to dynamic PORT (DigitalOcean 8080 / Render 10000) serving public/
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]
