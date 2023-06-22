#!/bin/sh
set -e

if [ "$APP_ENV" != 'prod' ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

if grep -q ^DATABASE_URL= .env; then
  if [ "$(find ./migrations -iname '*.php' -print -quit)" ]; then
    bin/console doctrine:migrations:migrate --no-interaction
  fi
fi

setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
