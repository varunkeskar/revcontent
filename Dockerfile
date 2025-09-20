# Use an image that already has PHP CLI
FROM php:8.2-cli

# Keep times consistent with your script (it uses UTC)
ENV TZ=UTC

# Certificates & tzdata for HTTPS and proper time
RUN apt-get update \
 && apt-get install -y --no-install-recommends ca-certificates tzdata \
 && rm -rf /var/lib/apt/lists/*

# Workdir inside the container
WORKDIR /app

# Copy only what we need for this job
COPY revcontent_pause.php /app/

# (Optional) If you later add Composer deps, uncomment this block:
# COPY composer.json composer.lock /app/
# RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
#  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
#  && composer install --no-dev --prefer-dist --no-interaction --no-progress \
#  && rm composer-setup.php

# Nice for local runs; Render ignores CMD for Cron (it uses your “Command”)
CMD ["php", "/app/revcontent_pause.php"]
