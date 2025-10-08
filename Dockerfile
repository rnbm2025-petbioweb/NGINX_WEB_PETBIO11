# Imagen base oficial de Nginx
FROM nginx:latest

# Borrar la configuración por defecto para evitar conflictos
RUN rm /etc/nginx/conf.d/default.conf

# Copiar archivos de configuración principales
COPY nginx.conf /etc/nginx/nginx.conf
COPY security-headers.conf /etc/nginx/security-headers.conf

# Copiar todos los virtual hosts
COPY conf.d/ /etc/nginx/conf.d/

# Crear carpetas para logs (opcional, buena práctica)
RUN mkdir -p /var/log/nginx && chmod -R 755 /var/log/nginx

# Exponer puertos HTTP y HTTPS
EXPOSE 80 443

# Comando para arrancar nginx
CMD ["nginx", "-g", "daemon off;"]
