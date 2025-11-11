# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones de PHP (ejemplo: mysqli para bases de datos)
RUN apt-get update && \
    apt-get install -y libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Habilita el módulo de reescritura de Apache, útil para URLs amigables
RUN a2enmod rewrite

# Establece el directorio de trabajo (por defecto para Apache)
WORKDIR /var/www/html