
# ==========================================
# STAGE 1 : Compilation des extensions PHP
# ==========================================
FROM php:8.3-apache-trixie AS builder

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        libzip5 \
        unzip \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql bcmath zip \
    && rm -rf /var/lib/apt/lists/*

# ==========================================
# STAGE 2 : Image finale
# ==========================================
FROM php:8.3-apache-trixie

# Mise à jour et nettoyage des outils de compilation
RUN apt-get update \
    && apt-get upgrade -y \
       && apt-get purge -y \
        gcc g++ cpp gcc-14 cpp-14 \
        libgcc-14-dev libcc1-0 \
        libc6-dev linux-libc-dev \
        autoconf dpkg-dev make pkg-config re2c \
    && apt-get autoremove -y --purge \
    && apt-get install -y --no-install-recommends libzip5 \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Copier les extensions PHP compilées
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Configuration Apache Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    && sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Application Laravel
WORKDIR /var/www/html

COPY . .

# Installation des dépendances PHP
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache
