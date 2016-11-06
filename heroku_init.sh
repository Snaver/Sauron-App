#!/bin/bash

#
# Creates a new Heroku app with the given name and adds required add-ons.
#
# Usage:
# $ ./heroku)init.sh <APP-NAME>
#

# Check we got a valid new name
if [ -z "$1" ]
then
    echo >&2 "Please specify a name (subdomain) for your new Heroku WP app."
    exit 1
fi

if [[ "$1" =~ [^a-z0-9-]+ ]]
then
    echo >&2 "App name '$1' is invalid."
    exit 1
fi

# Check to see if Composer is installed if not install it
type composer >/dev/null 2>&1 || {
    echo >&2 "Composer does not exist."
    exit 1
}

# Check to see if Heroku Toolbelt is installed
type heroku >/dev/null 2>&1 || {
    echo >&2 "Heroku Toolbelt must be installed. (https://toolbelt.heroku.com)"
    exit 1
}

# Create new app and check for success
heroku apps:create "$1" --region eu || {
    echo >&2 "Could not create Heroku WP app."
    exit 1
}

# Addons
heroku addons:create \
    --app "$1" \
    heroku-postgresql:hobby-dev

heroku addons:create \
    --app "$1" \
    scheduler:standard

heroku labs:enable runtime-dyno-metadata \
    --app "$1"

# Configs
heroku config:set \
    --app "$1" \
    APP_KEY="`php artisan --no-ansi key:generate --show`"

heroku config:set \
    --app "$1" \
    APP_LOG="errorlog"

heroku config:set \
    --app "$1" \
    DB_CONNECTION="pgsql"

heroku config:set \
    --app "$1" \
    QUEUE_DRIVER="database"

heroku config:set \
    --app "$1" \
    GITHUB_TOKEN="XXX"

heroku config:set \
    --app "$1" \
    GITHUB_GIST_ID="XXX"

heroku buildpacks:set heroku/php

# Heroku git remote
heroku git:remote \
    -a "$1"

# Deploy
git push heroku master

EXIT_CODE="$?"
if [ "$EXIT_CODE" -ne "0" ]; then
    printf >&2 "\n\nDeploy failed for '$1'.\n\n"
else
    printf "\n\nNew Heroku WP app '$1' created and deployed via:\n\$ git push heroku $1:master\n\n"
fi

heroku ps:scale web=1
heroku ps:scale worker=1

heroku run php artisan migrate --force --seed

heroku addons --app "$1"

# Keep everything fresh
heroku restart

exit "$EXIT_CODE"