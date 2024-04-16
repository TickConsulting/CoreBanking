# Use the official PHP image with Apache
FROM php:7.4-apache

# Copy your project into the container
COPY . /var/www/html/

# Copy custom Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable mod_rewrite for URL rewriting
RUN a2enmod rewrite
  
RUN docker-php-ext-install mysqli

# Optionally, enable other Apache mods here if needed
RUN a2enmod headers

# Expose port 80 to access Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
