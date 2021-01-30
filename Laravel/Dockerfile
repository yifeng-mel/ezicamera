FROM smartvision/app:web

ENV DEBIAN_FRONTEND=noninteractive

COPY web /web

RUN rm -r /var/www/html

RUN ln -s /web/public /var/www/html

RUN chmod -R 777 /web/storage

RUN cd /web && composer install

RUN cd / && mkdir database && chmod -R 777 /database

RUN cd /database && rm -f database.sqlite && touch database.sqlite && chmod 777 database.sqlite

RUN cd /web && php artisan migrate && php artisan key:generate

# RUN chmod -R 777 /web