FROM php:7.4-cli
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git unzip

WORKDIR /usr/src/tonic-friends
COPY . /usr/src/tonic-friends

# Delete the vendor and environment specific phpunit binaries as they'll be re-installed
RUN rm -rf vendor
RUN rm -rf bin/.phpunit

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install
RUN php bin/phpunit install

# install the symfony command to run a development web server:
RUN curl -sS https://get.symfony.com/cli/installer | bash

# run the migrations on dev and test databases
RUN php bin/console -e dev doctrine:migrations:migrate -n
RUN php bin/console -e test doctrine:migrations:migrate -n

CMD [ "/root/.symfony/bin/symfony", "serve" ]