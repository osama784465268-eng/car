# Dockerfile for Railway Deployment (PHP + Apache + SQLite & MySQL)
FROM php:8.2-apache

# Install PDO MySQL and PDO SQLite extensions without extra MPM recommends
RUN apt-get update && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copy project files into Apache web directory
COPY . /var/www/html/

# Set correct permissions so Apache www-data can write to SQLite database and db directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/db

# Dynamic PORT configuration for Railway
ENV PORT=80
EXPOSE 80

# Configure Apache to listen on Railway's dynamic PORT
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

CMD ["apache2-foreground"]
