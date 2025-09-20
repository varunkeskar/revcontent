FROM php:8.2-cli

# Install curl
RUN apt-get update && apt-get install -y curl unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy app files
COPY . .

# Install dependencies
RUN composer install

# Run the PHP script
CMD [ "php", "revcontent_pause.php" ]
