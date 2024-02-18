#!/usr/bin/env bash
sleep 30
php bin/console doctrine:database:create --if-not-exists --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"