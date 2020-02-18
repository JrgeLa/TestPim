#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"
cd ./../../

cp .env.dist .env
cp ./app/config/parameters.yml.dist ./app/config/parameters.yml
sed -i "s/database_host:.*localhost/database_host: mysql/g" ./app/config/parameters.yml
sed -i "s/localhost: 9200/elasticsearch:9200/g" ./app/config/parameters.yml

cp ./app/config/parameters.yml ./app/config/parameters_test.yml
