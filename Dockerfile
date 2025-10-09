# =========================================================
# 🐳 Contenedor unificado PETBIO: PHP 8.2 + Nginx + Landing
# =========================================================

FROM php:8.2-fpm

# ---------------------------------------------------------
# 🧩 Instalar Nginx y extensiones PHP necesarias
# ---------------------------------------------------------
RUN apt-get update && \
    apt-get install -y nginx nano bash curl unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# ⚙️ Copiar configuraciones de Nginx
# ---------------------------------------------------------
COPY nginx.conf /etc/nginx/nginx.conf
COPY security-headers.conf /etc/nginx/security-headers.conf
COPY conf.d/ /etc/nginx/conf.d/

# ---------------------------------------------------------
# 📁 Copiar el código del landing y formularios
# ---------------------------------------------------------
COPY petbio_landing /var/www/html/petbio_landing

# ---------------------------------------------------------
# 🔐 Permisos correctos para PHP/Nginx
# ---------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# ---------------------------------------------------------
# 🔧 Configuración de PHP-FPM
# ---------------------------------------------------------
RUN echo "cgi.fix_pathinfo=0" > /usr/local/etc/php/conf.d/security.ini && \
    echo "upload_max_filesize=250M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=250M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

# ---------------------------------------------------------
# 🌍 Exponer puertos HTTP y HTTPS
# ---------------------------------------------------------
EXPOSE 80 443

# ---------------------------------------------------------
# 🚀 Iniciar PHP-FPM y Nginx juntos
# ---------------------------------------------------------
CMD ["sh", "-c", "php-fpm -R && nginx -g 'daemon off;'"]
