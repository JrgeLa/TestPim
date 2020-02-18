#!/usr/bin/env bash
set -e

# docker-compose exec fpm sh -c 'mkdir -p /home/docker/.ssh'
docker-compose exec fpm sh -c 'echo "Host github.com\n\tStrictHostKeyChecking no\n" >> /home/docker/.ssh/config'
docker-compose exec fpm php -d memory_limit=3G /usr/local/bin/composer update
docker-compose run --rm node yarn install
