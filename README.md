Reshipi
=======

Simple Chef Solo recipe server for Vagrant

Features
--------

* Manage collections of Chef recipes (only Git repos for now)
* Use the Chef Solo `recipe_url` option to get at 'em

Requirements
------------

* PHP 5.3.3+
* MySQL 5.1+
* Apache2+

Installation
------------

Reshipi is a Symfony 2 app, so once you've cloned the repo, change to the root of the directory and create your `parameters.yml` config file. You will need to change the `database` and `auth_user` parameters to suit your setup.

```bash
$ cp app/config/parameters.yml.dist app/config/parameters.yml
$ vim app/config/parameters.yml
```

Next, create a `data` directory and make sure it and the `app/cache` and `app/logs` directories are writable by your web server.

```bash
$ mkdir data
$ chmod -R 777 data        #
$ chmod -R 777 app/cache   # not very secure, but it works
$ chmod -R 777 app/logs    #
```

Then, make sure you have Composer available and get the app ready to run.

```bash
$ composer install
$ php app/console doctrine:database:create
$ php app/console doctrine:schema:create
$ php app/console assets:install web
```

The app should be ready where you've just installed it.

Usage
-----

### Create a Collection

1. From the start-page, use the "Create Collection" form to create a collection
2. Once created, follow the collection name to a page that lists all recipes for that collection
3. Use the "Add Recipe" form to add a recipe to the collection (only Git repos are supported for now)

### Configure Vagrant

1. Copy the "Archive URL" link for the collection on the start-page
2. Set the `chef.recipe_url` option to use the URL you've just copied
3. `vagrant up` and the recipes will be used for provisioning