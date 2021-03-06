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

memo-composer:
	docker-compose run --rm memo-php-cli composer $(arg)

memo-test:
	docker-compose run --rm memo-php-cli php bin/phpunit $(arg)

memo-cli:
	docker-compose run --rm memo-php-cli $(arg)

run-production-build:
	docker-compose -f docker-compose-production.yml up -d

stop-production-build:
	docker-compose -f docker-compose-production.yml down --remove-orphans

docker-up-staging:
	docker-compose -f docker-compose-staging.yml up -d

docker-down-staging:
	docker-compose -f docker-compose-staging.yml down --remove-orphans

memo-composer-install-staging:
	docker-compose -f docker-compose-staging.yml run --rm memo-php-cli-stage composer install

memo-cli-staging::
	docker-compose -f docker-compose-staging.yml run --rm memo-php-cli-stage $(arg)

memo-migrate:
	docker exec -it $(container) "/app/bin/migrate.sh"

memo-reminder-run:
	docker exec -i $(container) "/app/bin/reminder_run.sh"

memo-license-deactivate:
	docker exec -i $(container) "/app/bin/licenses_deactivate.sh"

build-image: memo-test
	docker build --pull --file=./docker/production/nginx.dockerfile --tag ${REGISTRY_HOST}/memo-nginx:${REGISTRY_PRODUCTION_TAG} .
	docker build --pull --file=./docker/production/php-fpm.dockerfile --tag ${REGISTRY_HOST}/memo-php-fpm:${REGISTRY_PRODUCTION_TAG} .
	docker build --pull --file=./docker/production/postgres.dockerfile --tag ${REGISTRY_HOST}/memo-postgres:${REGISTRY_PRODUCTION_TAG} .
	docker build --pull --file=./docker/production/php-cli.dockerfile --tag ${REGISTRY_HOST}/memo-php-cli:${REGISTRY_PRODUCTION_TAG} .
	docker build --pull --file=./docker/production/redis.dockerfile --tag ${REGISTRY_HOST}/memo-redis:${REGISTRY_PRODUCTION_TAG} .

push-registry:
	docker push ${REGISTRY_HOST}/memo-nginx:${REGISTRY_PRODUCTION_TAG}
	docker push ${REGISTRY_HOST}/memo-php-fpm:${REGISTRY_PRODUCTION_TAG}
	docker push ${REGISTRY_HOST}/memo-postgres:${REGISTRY_PRODUCTION_TAG}
	docker push ${REGISTRY_HOST}/memo-php-cli:${REGISTRY_PRODUCTION_TAG}
	docker push ${REGISTRY_HOST}/memo-redis:${REGISTRY_PRODUCTION_TAG}

deploy-production:
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'cd /var/www/${STAGING_HOST}; rm -rf docker-compose.yml'
	scp docker-compose-production.yml ${PRODUCTION_USER}@${PRODUCTION_IP}:/var/www/${PRODUCTION_HOST}/docker-compose.yml
	scp .env.prod ${PRODUCTION_USER}@${PRODUCTION_IP}:/var/www/${PRODUCTION_HOST}/.env
	scp Makefile ${PRODUCTION_USER}@${PRODUCTION_IP}:/var/www/${PRODUCTION_HOST}/Makefile
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'cd /var/www/${PRODUCTION_HOST}; docker-compose pull'
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'cd /var/www/${PRODUCTION_HOST}; docker-compose up --build -d'
	ssh ${PRODUCTION_USER}@${PRODUCTION_IP} 'source ~/.bashrc'

production-dump:
	./bin/production_db_dump.sh