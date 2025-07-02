# Imagen base oficial de PHP con Apache
FROM php:8.3-apache

# Instalar extensiones necesarias
RUN apt-get update \
    && apt-get install -y \
        libxml2-dev \
        libpq-dev \
        git \
        unzip \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        soap

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar el código al contenedor
COPY . /var/www/html

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80 (Apache)
EXPOSE 80
