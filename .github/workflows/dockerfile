# Usa la imagen oficial de PHP con FPM
FROM php:8.2-fpm

# Instala extensiones necesarias
RUN docker-php-ext-install pdo pdo_pgsql

# Copia los archivos del proyecto
WORKDIR /var/www/html
COPY . .

# Instala dependencias de Laravel
RUN apt-get update && apt-get install -y unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Establece permisos adecuados
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto 9000
EXPOSE 9000

CMD ["php-fpm"]