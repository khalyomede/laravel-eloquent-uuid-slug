FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get upgrade --yes && \
    apt-get install --yes lsb-release ca-certificates apt-transport-https software-properties-common curl zip && \
    add-apt-repository ppa:ondrej/php && \
    apt-get install --yes \
    php8.3 \
    php8.3-fileinfo \
    php8.3-mbstring \
    php8.3-dom \
    php8.3-curl \
    php8.3-zip \
    php8.3-sqlite \
    php8.3-intl && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer
