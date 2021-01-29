FROM smartvision/app:webrtc

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update

# 1 install apache
RUN apt-get install -y --no-install-recommends apache2

# 2 install pagekite 
COPY pagekite.py /

# 3 install certbot
RUN apt-get install -y --no-install-recommends software-properties-common

# 4 install websocket server
COPY simple-server.py /

RUN apt-get install -y --no-install-recommends certbot python-certbot-apache

# 5 install php7.2
RUN add-apt-repository -y ppa:ondrej/php

RUN apt-get update

RUN apt-get install -y curl php7.2 libapache2-mod-php7.2 php7.2-mbstring php7.2-dom php7.2-sqlite sqlite3 mediainfo wget php7.2-cli php7.2-zip unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
