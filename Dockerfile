# =========================================================
# 🐳 PETBIO Landing + Formularios - PHP 8.2 + Nginx (Render)
# =========================================================
FROM php:8.2-fpm

# ---------------------------------------------------------
# 🧩 Instalar Nginx, extensiones y librerías necesarias
# ---------------------------------------------------------
RUN apt-get update && \
    apt-get install -y nginx nano bash curl unzip libpq-dev && \
    docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# 📁 Copiar proyecto completo
# ---------------------------------------------------------
COPY . /var/www/html/

# ---------------------------------------------------------
# ⚙️ Configuración personalizada de Nginx
# ---------------------------------------------------------
COPY nginx.conf /etc/nginx/nginx.conf
COPY security-headers.conf /etc/nginx/security-headers.conf
COPY conf.d/ /etc/nginx/conf.d/

# 🔧 Cambiar el root de Nginx al directorio real de los scripts
RUN sed -i 's|root /var/www/html;|root /var/www/html/petbio_landing;|g' /etc/nginx/nginx.conf

# ---------------------------------------------------------
# 🔐 Permisos y configuraciones PHP
# ---------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    echo "cgi.fix_pathinfo=0" > /usr/local/etc/php/conf.d/security.ini && \
    echo "upload_max_filesize=250M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=250M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

# ---------------------------------------------------------
# 🌍 Exponer el puerto HTTP (Render usa HTTPS externo)
# ---------------------------------------------------------
#EXPOSE 80

# ---------------------------------------------------------
# 🚀 Comando final (levanta PHP-FPM y Nginx)
# ---------------------------------------------------------
#CMD ["bash", "-c", "php-fpm -D && nginx -g 'daemon off;'"]


EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
