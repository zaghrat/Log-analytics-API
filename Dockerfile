FROM php:8.3-apache AS webserver

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libzip-dev git wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo mysqli pdo_mysql zip;

RUN wget https://getcomposer.org/download/2.6.6/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
COPY ./app /var/www
WORKDIR /var/www
RUN composer install -n

RUN chmod +x /entrypoint.sh
RUN chmod +x bin/console

CMD ["apache2-foreground"]
ENTRYPOINT ["/entrypoint.sh"]


FROM php:8.3-apache AS cronjob
RUN apt-get update \
  && apt-get install -y cron libzip-dev wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo mysqli pdo_mysql zip;
COPY --from=webserver /var/www /var/www
WORKDIR /var/www
RUN chmod +x bin/console

COPY docker/cronjob /etc/cron.d/cronjobs
RUN chmod 0644 /etc/cron.d/cronjobs
RUN service cron start
RUN crontab /etc/cron.d/cronjobs
CMD ["cron", "-f"]
