# Use the official PHP image with Apache
FROM php:7.4-apache

# Copy your project into the container
COPY . /var/www/html/

# Copy custom Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Installing sendmail
RUN apt-get update && apt-get install -y sendmail

# Enable mod_rewrite for URL rewriting
RUN a2enmod rewrite

RUN docker-php-ext-install mysqli

# Optionally, enable other Apache mods here if needed
RUN a2enmod headers

# Set permissions for files and directories in /var/www/html recursively
RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;

# Set specific permissions for the assets directory
RUN find /var/www/html/assets -type d -exec chmod 755 {} \;
RUN find /var/www/html/assets -type f -exec chmod 644 {} \;

# Set permissions for the logs directory
RUN mkdir -p /var/www/html/logs
RUN chown -R www-data:www-data /var/www/html/logs
RUN chmod -R 775 /var/www/html/logs

# Expose port 80 to access Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
