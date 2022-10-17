#!/usr/bin/env bash

chmod -R ugo+rw /var/www/html/var/log

chmod -R ugo+rw /var/www/html/var/cache

chown -R www-data /var/www/html/var/log/

chown -R www-data /var/www/html/var/cache/

symfony check:requirements

composer install -n

# php bin/console doctrine:database:create -n
# php bin/console make:migration -n
# php bin/console doctrine:migrations:migrate -n

# symfony console doctrine:migrations:migrate -n

# bin/console doc:mig:mig -n
# bin/console doc:fix:load -n

exec "$@"