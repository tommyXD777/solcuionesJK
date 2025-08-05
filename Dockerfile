# Imagen base con PHP y Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite para Apache (útil en proyectos MVC)
RUN a2enmod rewrite

# Copiar los archivos del proyecto
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Exponer puerto
EXPOSE 80
