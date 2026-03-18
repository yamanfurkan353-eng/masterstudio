FROM php:8.2-apache

# Sistem paketlerini indir
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache modüllerini etkinleştir
RUN a2enmod rewrite headers

# PHP ayarları
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "display_errors = Off" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "session.use_only_cookies = 1" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "session.cookie_samesite = Strict" >> /usr/local/etc/php/conf.d/security.ini

# Çalışma dizini
WORKDIR /var/www/html

# Apache ayarları
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Projeyi kopyala
COPY . .

# Gereksiz dosyaları sil
RUN rm -f install.php install.sh

# Dosya izinleri
RUN chown -R www-data:www-data /var/www/html && \
    chmod 600 /var/www/html/.env 2>/dev/null || true

# Healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s \
    CMD curl -f http://localhost/ || exit 1

EXPOSE 80

CMD ["apache2-foreground"]
