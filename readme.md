## Chat REST API for the ORA Challenge

Uses the following packages:

* JWT-Auth - [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
* Dingo API - [dingo/api](https://github.com/dingo/api)
* Laravel-CORS [barryvdh/laravel-cors](http://github.com/barryvdh/laravel-cors)

## Installation

1. run `composer install`;
2. run `php artisan migrate --seed`;

## Secrets Generation

Every time you create a new project starting from this repository, the _php artisan jwt:generate_ command will be executed.

## Configuration

There are some extra options that placed in a _config/boilerplate.php_ file:

* `sign_up.release_token`: set it to `true` if you want the token right after the sign up process;
* `reset_password.release_token`: set it to `true` if you want the token right after the password reset process;
