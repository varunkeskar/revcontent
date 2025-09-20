FROM php:8.2-cli

# Copy all files to /app inside the container
COPY . /app

# Set working directory
WORKDIR /app

# Optional: install PHP extensions if needed
RUN docker-php-ext-install curl

# ✅ Set the command that will run during cron schedule
CMD ["php", "revcontent_pause.php"]
