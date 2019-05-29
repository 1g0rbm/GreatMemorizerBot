FROM nginx:1.15-alpine

COPY ./docker/production/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

COPY ./ /app