#!/usr/bin/env bash

source "$(pwd)/.env"

docker exec -i $(docker ps -q -f "ancestor=registry.1g0rbm.com/memo-postgres:prod") pg_dump -U ${POSTGRES_DB} ${POSTGRES_DB} > /var/dumps/production_dump.sql