# Use an official PHP runtime as a parent image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Ensure permissions are set for the Nginx run directory
RUN chown -R www-data:www-data /var/www

# Copy Nginx configuration file
COPY nginx.conf /etc/nginx/sites-available/default

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
