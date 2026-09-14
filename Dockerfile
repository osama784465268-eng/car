# Dockerfile for Railway Deployment (PHP + Apache + MySQL PDO)
FROM php:8.2-apache

# Install PDO MySQL extension required for database operations
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into Apache web directory
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Dynamic PORT configuration for Railway
ENV PORT=80
EXPOSE 80

# Configure Apache to listen on Railway's dynamic PORT
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

CMD ["apache2-foreground"]
