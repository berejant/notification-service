#!/usr/bin/env bash
[ -f .env ] || cp .env.example .env

docker compose build;

docker compose up -d mysql;
echo 'Wait for load Mysql server...';
while true;
do
    docker compose logs --tail 20 mysql | grep "ready for connections"| grep -v "Plugin" && break;
    sleep 1;
done;

docker compose up -d php-worker;

composer install
docker compose exec php-worker /var/www/artisan migrate;

docker compose up -d;
