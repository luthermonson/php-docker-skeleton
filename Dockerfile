FROM ghcr.io/luthermonson/php-docker-skeleton:base
RUN mkdir -p /app
COPY ./app /app
WORKDIR /app