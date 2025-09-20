# Use official PHP CLI (Debian) and enable the cURL extension
FROM php:8.2-cli

WORKDIR /app

# Install libcurl and enable PHP's curl extension
RUN apt-get update \
 && apt-get install -y --no-install-recommends libcurl4-openssl-dev ca-certificates \
 && docker-php-ext-install curl \
 && rm -rf /var/lib/apt/lists/*

# Copy your script
COPY revcontent_pause.php /app/

# Default command (Render Cron will override with its Command, but this is handy for local runs)
CMD ["php", "/app/revcontent_pause.php"]
