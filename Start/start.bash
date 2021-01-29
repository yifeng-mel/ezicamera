#!/bin/bash

# set up this env variable so C runtime can find the related library like opus, ..., webrtc, etc.
export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/lib

# create cusomter user account
cd /web && php artisan create:user $5 $6

chmod +x /pagekite.py
chmod +x /simple-server.py

# start apache for opening port 80
service apache2 start 

# start pagekite for public port 80
/pagekite.py --daemonize --clean --frontend=$2:80 --service_on=http:$3:localhost:80:$4

# acquire https certificate
# certbot --apache --non-interactive --agree-tos -m $1 -d $3
privateKeyHome="/etc/letsencrypt/live/$3"
privateKeyFile="$privateKeyHome/privkey.pem"

echo "Checking if certificate [$privateKeyFile] exist )."
if [ ! -f $privateKeyFile ]; then
    echo "Certificate file [$privateKeyFile] does not exist"
    certbot --apache --non-interactive --agree-tos -m $1 -d $3 # production
    # certbot certonly --dry-run --apache --non-interactive --agree-tos -m $1 -d $3 #test
    # configure apache websocket proxy
    sed -i '/<\/VirtualHost>/i ProxyPass /wss/  ws://localhost:8443' /etc/apache2/sites-available/000-default-le-ssl.conf # production
    sed -i '/<\/VirtualHost>/i <Directory /var/www/html>\n Options Indexes FollowSymLinks \n AllowOverride All \n Require all granted \n</Directory>' /etc/apache2/sites-available/000-default-le-ssl.conf # production
    # sed -i '/<\/VirtualHost>/i ProxyPass /wss/  ws://localhost:8443' /etc/apache2/sites-available/default-ssl.conf #test
else
    echo "Certificate file [$privateKeyFile] exist, checking for renewal"
    certbot renew --no-random-sleep-on-renew --apache --no-self-upgrade
    # only for test, it causing multiple entries added to 000-default.conf
    sed -i "/Redirect \//d" /etc/apache2/sites-available/000-default.conf
    sed -i "/<\/VirtualHost>/i Redirect / https://$3" /etc/apache2/sites-available/000-default.conf
fi

# stop pagekite for public port 80
pkill pagekite 

# restart apache to apply websocket proxy
a2enmod rewrite && a2enmod proxy && a2enmod proxy_wstunnel && a2enmod ssl && a2ensite 000-default.conf && a2ensite 000-default-le-ssl.conf && service apache2 reload && service apache2 restart 

# start the pagekite for port 443
/pagekite.py --daemonize --clean --frontend=$2:80 --service_on=https:$3:localhost:443:$4 --service_on=http:$3:localhost:80:$4

# start signalling server
/simple-server.py --disable-ssl