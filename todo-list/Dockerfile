FROM php:8.1-apache

# Install ekstensi yang dibutuhkan
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite untuk .htaccess
RUN a2enmod rewrite

# Copy file konfigurasi Apache (opsional, jika mau .htaccess aktif)
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# Set working directory
WORKDIR /var/www/html

# Copy source code ke dalam container (nanti saat build)
COPY ./app /var/www/html

EXPOSE 80
