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
	docker-sync clean

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

docker-up-staging:
	docker-compose -f docker-compose-staging.yml up

docker-down-staging:
	docker-compose -f docker-compose-staging.yml down --remove-orphans

memo-composer-install-staging:
	docker-compose -f docker-compose-staging.yml run --rm memo-php-cli composer install

stop-production-build:
	docker-compose -f docker-compose-production.yml down --remove-orphans

build-image:
	docker build --pull --file=./docker/production/nginx.dockerfile --tag ${REGISTRY_HOST}/memo-nginx:${REGISTRY_PRODUCTION_TAG} .
	docker build --pull --file=./docker/production/php-fpm.dockerfile --tag ${REGISTRY_HOST}/memo-php-fpm:${REGISTRY_PRODUCTION_TAG} .
	docker build --pull --file=./docker/production/postgres.dockerfile --tag ${REGISTRY_HOST}/memo-postgres:${REGISTRY_PRODUCTION_TAG} .

push-registry:
	docker push ${REGISTRY_HOST}/memo-nginx:${REGISTRY_PRODUCTION_TAG}
	docker push ${REGISTRY_HOST}/memo-php-fpm:${REGISTRY_PRODUCTION_TAG}
	docker push ${REGISTRY_HOST}/memo-postgres:${REGISTRY_PRODUCTION_TAG}

deploy-production:
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'cd /var/www/${STAGING_HOST}; rm -rf docker-compose.yml'
	scp docker-compose-production.yml ${PRODUCTION_USER}@${PRODUCTION_IP}:/var/www/${PRODUCTION_HOST}/docker-compose.yml
	scp .env.prod ${PRODUCTION_USER}@${PRODUCTION_IP}:/var/www/${PRODUCTION_HOST}/.env
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'cd /var/www/${PRODUCTION_HOST}; docker-compose pull'
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'cd /var/www/${PRODUCTION_HOST}; docker-compose up --build -d'
