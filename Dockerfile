# ================================================
# 🌐 DOCKERFILE OPTIMIZADO PARA RENDER
# Evita reconstrucciones innecesarias y mantiene
# compatibilidad con PHP-FPM + Nginx unificado
# ================================================


# =========================================================
# 🐳 PETBIO Landing + Formularios - Contenedor Unificado
# PHP 8.2 + Nginx + Seguridad + Logs
# =========================================================

# Etapa base: PHP-FPM 8.2
# Se mantiene separada para permitir cache de dependencias
FROM php:8.2-fpm AS base

# ---------------------------------------------------------
# 🧩 Instalar Nginx y extensiones PHP necesarias
# ---------------------------------------------------------
RUN apt-get update && \
    apt-get install -y nginx nano bash curl unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# ⚙️ Copiar configuraciones personalizadas de Nginx
# ---------------------------------------------------------
COPY nginx.conf /etc/nginx/nginx.conf
COPY security-headers.conf /etc/nginx/security-headers.conf
COPY conf.d/ /etc/nginx/conf.d/
COPY dir_config /var/www/html/dir_config

# ---------------------------------------------------------
# 📁 Copiar el código de la landing y formularios PHP
# ---------------------------------------------------------
# ⚠️ Colocamos esta capa al final para aprovechar el cache
COPY . /var/www/html/

# ---------------------------------------------------------
# 🔐 Permisos correctos para PHP/Nginx
# ---------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# ---------------------------------------------------------
# 🔥 Ajustar configuración de PHP-FPM
# ---------------------------------------------------------
RUN echo "cgi.fix_pathinfo=0" > /usr/local/etc/php/conf.d/security.ini && \
    echo "upload_max_filesize=250M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=250M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

# ---------------------------------------------------------
# 🌍 Exponer puertos HTTP y HTTPS
# ---------------------------------------------------------
# Render solo necesita el puerto 80
EXPOSE 80

# ---------------------------------------------------------
# 🚀 Comando final: iniciar PHP y Nginx juntos
# ---------------------------------------------------------
CMD ["bash", "-c", "php-fpm -D && nginx -g 'daemon off;'"]


# =========================================================
# 🧱 OPCIÓN 1 (COMENTADA): Nginx puro
# Ideal si Render sirve el backend PHP por separado
# =========================================================

# Imagen base oficial de Nginx
#FROM nginx:latest

# Borrar la configuración por defecto para evitar conflictos
#RUN rm /etc/nginx/conf.d/default.conf

# Copiar archivos de configuración principales
#COPY nginx.conf /etc/nginx/nginx.conf
#COPY security-headers.conf /etc/nginx/security-headers.conf

# Copiar todos los virtual hosts
#COPY conf.d/ /etc/nginx/conf.d/

# Crear carpetas para logs (opcional, buena práctica)
#RUN mkdir -p /var/log/nginx && chmod -R 755 /var/log/nginx

# Exponer puertos HTTP y HTTPS
#EXPOSE 80 443

# Comando para arrancar nginx
#CMD ["nginx", "-g", "daemon off;"]


# =========================================================
# 🧱 OPCIÓN 2 (COMENTADA): STAGING con Nginx 1.22-extras
# Incluye soporte para more_set_headers
# =========================================================

# Dockerfile final para staging con Nginx 1.22-extras
#FROM nginx:1.22-extras

# Copiar configuración personalizada de Nginx
#COPY ./conf.d/ /etc/nginx/conf.d/

# Exponer puerto para staging
#EXPOSE 8081

# Arrancar Nginx en primer plano
#CMD ["nginx", "-g", "daemon off;"]


# =========================================================
# 🧱 OPCIÓN 3 (COMENTADA): Dockerfile separado
# Si quieres construir desde otro Dockerfile dentro del repo
# =========================================================

#FROM ./petbio_landing/Dockerfile


# =========================================================
# ✅ NOTAS DE OPTIMIZACIÓN
# ---------------------------------------------------------
# 1️⃣ COPY de código se deja al final → usa cache para Nginx y PHP
# 2️⃣ EXPOSE 80 → Render solo sirve HTTP (HTTPS lo maneja externamente)
# 3️⃣ CMD combina php-fpm + nginx sin procesos secundarios
# 4️⃣ Evita borrar caché entre builds (Render la reutiliza)
# =========================================================
