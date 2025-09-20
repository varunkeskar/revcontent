FROM php:8.2-cli

# Copy files from repo into container
COPY . /app

# Set working directory
WORKDIR /app

# Install any PHP extensions here if needed (e.g., curl)
RUN docker-php-ext-install curl

# Command to run (Render ignores this for cron jobs but it’s good to define)
CMD ["php", "revcontent_pause.php"]
