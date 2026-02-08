FROM php:8.2-apache

# Sistem paketlerini indir
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql gd

# Apache mod_rewrite etkinleştir
RUN a2enmod rewrite

# php.ini ayarları
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Çalışma dizini
WORKDIR /var/www/html

# Apache ayarları
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Projeyi kopyala
COPY . .

# Dosya izinleri
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
