FROM richarvey/nginx-php-fpm:3.1.6

COPY default.conf /etc/nginx/sites-enabled/default.conf

RUN apk update && \
    apk add --no-cache sqlite sqlite-dev && \
    docker-php-ext-install pdo pdo_sqlite pcntl bcmath
#WORKDIR /var/www/api
WORKDIR /var/www

RUN apk add vim


# Set correct permissions for database directory and file
RUN mkdir -p /var/www/database && \
    touch /var/www/database/dbtwo.sqlite && \
    chown -R nginx:nginx /var/www && \
    chmod -R 777 /var/www

RUN chown nginx:nginx /var/www/database/dbtwo.sqlite

# Set read-write permissions for user, group, and others
RUN chmod 666 /var/www/database/dbtwo.sqlite




#run this before docker build:
#chmod -R 777 ./AuthenticationMicroService/database
#chmod 777 ./AuthenticationMicroService/database/database.sqlite \

COPY . .


RUN php artisan migrate --force

RUN chown -R nginx:nginx /var/www/storage && chown -R nginx:nginx /var/www/bootstrap/cache && chown -R nginx:nginx /tmp
