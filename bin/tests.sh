#!/bin/bash

export APP_ENV="test"

if [ -f "vendor/bin/phpstan" ]; then
    docker compose exec app php vendor/bin/phpstan analyse -c phpstan.neon

    rc=$?
    if [[ $rc != 0 ]] ; then
        echo -n "It looks like some of your tests failed."
        exit $rc;
    fi
fi

if [ -f "vendor/bin/phpcs" ]; then
    docker compose exec app php vendor/bin/phpcs -n

    rc=$?
    if [[ $rc != 0 ]] ; then
        echo -n "It looks like some of your tests failed."
        exit $rc;
    fi
fi

if [ -f "vendor/bin/simple-phpunit" ]; then
    docker compose exec app php vendor/bin/simple-phpunit --colors --no-interaction

    rc=$?
    if [[ $rc != 0 ]] ; then
        echo -n "It looks like some of your tests failed."
        exit $rc;
    fi
fi

if [ -f "vendor/bin/behat" ]; then
    docker compose exec app php vendor/bin/behat -f progress

    rc=$?
    if [[ $rc != 0 ]] ; then
        echo -n "It looks like some of your tests failed."
        exit $rc;
    fi
fi

if [ -f "vendor/bin/rector" ]; then
    docker compose exec app php vendor/bin/rector --dry-run

    rc=$?
    if [[ $rc != 0 ]] ; then
        echo -n "It looks like some of your tests failed."
        exit $rc;
    fi
fi

exit 0;