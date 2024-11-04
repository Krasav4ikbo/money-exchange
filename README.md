# Money exchange application

### Setup project
This application is shipped with the Docker Compose environment and requires Docker to be installed locally and running.
If you're not familiar with Docker or don't have it locally, please reach out to
[the official website](https://www.docker.com) to learn more and follow the Docker installation instructions to install it on your platform:

[Docker for Mac](https://docs.docker.com/desktop/install/mac-install/)  
[Docker for Linux](https://docs.docker.com/desktop/get-started/)  
[Docker for Windows](https://docs.docker.com/desktop/install/windows-install/)

The test assignment application is containerized within three containers that have PHP-FPM, Nginx and MySQL respectively.
You don't need to build anything locally, the related images will be automatically pulled from the remote registry
as soon as you run the application for the first time.

Included tools:
- PHP 8.3
- PHPUnit 9.5
- Composer
- MySQl 8.0
- Nginx 1.20

## Local setup

Once you have Docker up and running please perform the following steps:

**1. Setup .env files**

Copy `/.env.dist` to `/.env`.

**2. Run application**

Please execute the following command to start the application:
```
docker-compose up
```

If you run the application for the first time, this will pull three images from the remote repository,
create `money-exchange-database`, `money-exchange-php` and `money-exchange-nginx` containers.

To set up application for the first time use functionality inside the `money-exchange-php` container, so first you need to connect to it by
creating a terminal:
```
docker exec -it money-exchange-php /bin/bash
```
Use following commands to install composer dependencies, make migrations to init database and load test data. Choose `yes` to all options in this process
```
composer install

php bin/console doctrine:migrations:migrate
```

The main web container will be listening on port `8080` on your `localhost`, you can access the application main page using the
following URL: [http://localhost:8080](http://localhost:8080).

## Update currency rates

To update currency rates, first you need to connect to it by creating a terminal:
```
docker exec -it money-exchange-php /bin/bash
```

Run following command with one of two valid sources `(CRB, ECB)`:
```
php bin/console app:currency-rate-update CBR
or
php bin/console app:currency-rate-update ECB
```

## Unit tests

PHP Unit tests are performed inside the `money-exchange-php` container, so first you need to connect to it by
creating a terminal:
```
docker exec -it money-exchange-php /bin/bash
```

Run the tests with the following command:
```
php bin/phpunit
```

## API endpoints

This application is using four custom API endpoints:

Available API method:
**http://localhost:8080/api/exchange**

It is a **POST** type request with example body:
```
{
    "iso_from": string, // required
    "iso_to": string, // required
    "amount": integer, // required
    "app_source": string // optional
}
```