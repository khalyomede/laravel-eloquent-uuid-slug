FROM ubuntu:20.04

RUN apt-get update && \
    apt-get upgrade --yes && \
    apt-get install --yes lsb-release ca-certificates apt-transport-https software-properties-common curl zip && \
    add-apt-repository ppa:ondrej/php && \
    apt-get install --yes \
    php8.2 \
    php8.2-fileinfo \
    php8.2-mbstring \
    php8.2-dom \
    php8.2-curl \
    php8.2-zip \
    php8.2-sqlite \
    php8.2-intl && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer
