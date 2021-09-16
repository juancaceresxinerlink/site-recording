FROM php:7.4.1-apache

USER root

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        zip \
        curl \
        unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-source delete


RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

RUN apt install build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev libssl-dev libsqlite3-dev libreadline-dev libffi-dev curl libbz2-dev -y

COPY docker/Python-3.8.2.tgz /Python-3.8.2.tgz

RUN cd / && tar xzf Python-3.8.2.tgz && cd /Python-3.8.2 && ./configure --enable-optimizations && make altinstall

#Complemento para grabaciones
RUN apt install ffmpeg -y
#Instalar modulo pydub
RUN pip3.8 install pydub

RUN pip3.8 install --upgrade pip setuptools wheel
#instala Pandas
RUN pip3.8 install pandas



# COPY docker/apache2/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#set our application folder as an environment variable
ENV APP_HOME /var/www/html

#change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY docker/apache2/vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/apache2/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf


#Cambia el puerto de escucha por el 443
COPY docker/apache2/ports.conf /etc/apache2/ports.conf

#Copiar certificado SSL

COPY docker/STAR_xinerlink_cl.crt /STAR_xinerlink_cl.crt
COPY docker/xinerlink.key /xinerlink.key


COPY docker/ProcesaGrabaciones.py /ProcesaGrabaciones.py

#change the web_root to laravel /var/www/html/public folder
#RUN sed -i -e "s/html/html\/public/g" /etc/apache2/sites-enabled/000-default.conf

#RUN cp .env.example .env

#Copiar Procecto Python 



#RUN

# enable apache module rewrite
RUN a2enmod rewrite

RUN a2enmod ssl

#copy source files and run composer
COPY . $APP_HOME

# install all PHP dependencies
RUN composer install --no-interaction
# RUN composer update --no-interaction


#RUN artisan
RUN php artisan storage:link

RUN php artisan key:generate

#change ownership of our applications
RUN chown -R www-data:www-data $APP_HOME
