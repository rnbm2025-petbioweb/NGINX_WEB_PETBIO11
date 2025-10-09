# Imagen base PHP-FPM
FROM php:8.2-fpm

# Instalar Nginx y extensiones
RUN apt-get update && \
    apt-get install -y nginx nano bash curl unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar configuraciones de Nginx
COPY nginx.conf /etc/nginx/nginx.conf
COPY security-headers.conf /etc/nginx/security-headers.conf
COPY conf.d/ /etc/nginx/conf.d/

# Copiar código de la landing
COPY petbio_landing /var/www/html/petbio_landing

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Configuración PHP
RUN echo "cgi.fix_pathinfo=0" > /usr/local/etc/php/conf.d/security.ini && \
    echo "upload_max_filesize=250M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=250M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

# Exponer puertos
EXPOSE 80 443

# Supervisar PHP-FPM y Nginx juntos
CMD ["sh", "-c", "php-fpm -R && nginx -g 'daemon off;'"]
