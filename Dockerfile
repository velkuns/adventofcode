FROM php:8.1-cli

#~ Add php extention
RUN  apt update \
  && apt install -y \
         libzip-dev \
  && docker-php-ext-install zip

#~ Install composer
RUN  curl -fsSL 'https://getcomposer.org/download/latest-stable/composer.phar' -o 'composer' \
  && chmod 0755 composer \
  && mv composer /usr/local/bin/composer


COPY src/ /server/src/application/src
COPY config/ /server/src/application/config
COPY data/ /server/src/application/data
COPY bin/ /server/src/application/bin
COPY composer.json /server/src/application/

WORKDIR /server/src/application

RUN composer install

ENTRYPOINT ["/server/src/application/bin/console"]

CMD ["--help"]
