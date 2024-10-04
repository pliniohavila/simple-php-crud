FROM php:7.4-fpm

# Instalando extensões PHP necessárias para o PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

RUN pecl install xdebug-3.1.6
RUN docker-php-ext-enable xdebug

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Ajusta as permissões da pasta de trabalho
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exposição da porta padrão do PHP-FPM
EXPOSE 9000
