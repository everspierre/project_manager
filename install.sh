#!/bin/bash
# install.sh
# Script d'installation de l'application
echo "Saisissez un environnement (dev, test, prod) :"
read env
if [ "$env" = "prod" -o "$env" = "test" -o "$env" = "dev" ]
then
    composer install
    npm install
    if [ "$env" = "prod" ]
    then
        npm run build
    else
        npm run dev
    fi
    php bin/console doctrine:database:drop --if-exists --force --env=$env
    php bin/console doctrine:database:create --env=$env
    php bin/console doctrine:migrations:migrate --env=$env
    if [ "$env" = "dev" ]
    then
        echo "Voulez-vous charger les fixtures ? (o:oui/n:non) :"
        read response
        if [ "$response" = "oui" -o "$response" = "o" ]
        then
            php bin/console doctrine:fixtures:load
        fi
    fi
    php bin/console cache:clear --env=$env
else
    exit
fi
