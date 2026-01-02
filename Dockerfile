# Usamos PHP con Apache
FROM php:8.2-apache

# Instalamos las librerías necesarias para conectar con PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copiamos todos tus archivos al servidor
COPY . /var/www/html/

# Exponemos el puerto 80
EXPOSE 80
