# Dockerfile using PHP Built-in Server (No Apache, 100% Simple for School Project)
FROM php:8.2-cli

# Install SQLite & MySQL PDO extensions
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html
WORKDIR /var/www/html

# Dynamic PORT for Railway
ENV PORT=80
EXPOSE 80

# Start PHP built-in web server directly
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t /var/www/html"]
