#!make
include .env
export $(shell sed 's/=.*//' .env)

init: docker-down docker-up memo-composer-install

docker-up:
	docker volume create --name=memo-sync
	docker-compose up -d
	docker-sync start

docker-down:
	docker-compose down --remove-orphans
	docker-sync stop

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

memo-composer-install:
	docker-compose run --rm memo-php-cli composer install

memo-test:
	docker-compose run --rm memo-php-cli php bin/phpunit

memo-cli:
	docker-compose run --rm memo-php-cli

run-production-build:
	docker-compose -f docker-compose-production.yml up -d

stop-production-build:
	docker-compose -f docker-compose-production.yml down --remove-orphans

build-production:
	docker build --pull --file=./docker/production/nginx.dockerfile --tag registry.1g0rbm.com/memo-nginx .
	docker build --pull --file=./docker/production/php-fpm.dockerfile --tag registry.1g0rbm.com/memo-php-fpm .
	docker build --pull --file=./docker/production/postgres.dockerfile --tag registry.1g0rbm.com/postgres .

push-production:
	docker push registry.1g0rbm.com/memo-nginx
	docker push registry.1g0rbm.com/memo-php-fpm
	docker push registry.1g0rbm.com/postgres

deploy-production:
	ssh igor@104.248.132.228 'cd /var/www/dev.1g0rbm.com; rm -rf docker-compose.yml'
	scp docker-compose-production.yml igor@104.248.132.228:/var/www/dev.1g0rbm.com/docker-compose.yml
	ssh igor@104.248.132.228 'cd /var/www/dev.1g0rbm.com; docker-compose pull'
	ssh igor@104.248.132.228 'cd /var/www/dev.1g0rbm.com; docker-compose up --build -d'
