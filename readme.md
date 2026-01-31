# Neato!

Neato! is a web directory, originally created for [CGHMN](https://cghmn.org),
that allows people to share and discover services and resources on the network.

## Requirements

- A webserver that supports PHP
- PHP 8.4
- Composer
- A database of some kind

## Installation

1) Clone project into directory
2) Create a `env.local` file to hold local settings
3) run `composer install` to install dependencies
4) run `php bin/console doctrine:migration:migrate` to set up the database
5) run `php bin/console app:default-data` to populate default data
