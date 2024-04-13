# Use the official PHP image with Apache
FROM php:7.4-apache

# Copy your project into the container
COPY . /var/www/html/

# Enable mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Expose port 80 to access Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
