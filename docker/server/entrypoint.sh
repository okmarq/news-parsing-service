#!/usr/bin/env bash

chmod -R ugo+rw /var/www/html/var/log

chmod -R ugo+rw /var/www/html/var/cache

chown -R www-data /var/www/html/var/log/

chown -R www-data /var/www/html/var/cache/

symfony console doctrine:migrations:migrate -n

exec "$@"