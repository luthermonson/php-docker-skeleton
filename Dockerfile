FROM php-base
RUN mkdir -p /app
COPY ./app /app
WORKDIR /app