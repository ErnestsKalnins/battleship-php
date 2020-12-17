FROM php:7.4-fpm

RUN apt-get update && apt-get install -y git zip unzip --no-install-recommends

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY ./ ./

RUN composer install

EXPOSE 8080/tcp

CMD ["./bin/gotty", "-w", "composer", "run", "game"]