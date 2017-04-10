Symfony comentator sample app
========================

This is a simple Symfony web app for posting and viewing comments.

Installation
--------------

1) Clone this github repo
```sh
$ git clone https://github.com/ldolancic/symfony-comentator-app.git
```

2) Install dependencies and specify parameters
```sh
$ composer install
```
3) Create database
```sh
$ php bin/console doctrine:database:create
```

4) Update database schema
```sh
$ php bin/console doctrine:schema:update --force
```