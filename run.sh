#!/bin/bash
set -e

docker-compose up --build -d

docker-compose exec application composer install

docker-compose exec application php bin/console doctrine:migration:migrate -n