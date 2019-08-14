Dockerfile to build a MariaDB image which contains the project's databases.

# Description

This Dockerfile creates and populates 3 databases:

* drupal7: the Drupal 7 database.
* drupal8_config: the configuration-only database for Drupal 8, where there is no content, configuration has been
  imported, and configuration migrations have been executed.
* drupal8: the full Drupal 8 database, where content migrations have been executed using the drupal8_config database. 

# Building the image for the first time

## Dump the Drupal 7 database
Create a database dump of the source database and save it to `scripts/database/dumps/drupal7.sql`.

## Create, configure, and dump the configuration database
Install Drupal 8, export configuration, install the migrate modules, generate migrations, 
run configuration migrations, and export the resulting configuration. Then create a database
dump and save it to `scripts/database/dumps/drupal8.sql`. If you need help on these steps,
have a look at [An Overview for Migrating Drupal Sites to 8](https://www.lullabot.com/articles/overview-migrating-drupal-sites-8).

## Dump the configuration database as the full database
Unless you have a small site and want to run the content migration locally
via `/vendor/bin/robo migrate:content`, create a database dump of the
configuration database and save it to `scripts/database/dumps/drupal8.sql`. CircleCI
will update this file in the nightly migration. 

## Create a repository at Quay.io

Create a repository at Quay.io via the web interface. There is no need to link the repository to a GitHub trigger.
Just create the repository.

## Authenticate, build, and push the image

```
docker login quay.io
vendor/bin/robo database:build-image master
vendor/bin/robo database:push-image master
```

## Try pulling the image

Once the tag is available at Quay.io (monitor the web interface to know this), then pull the image locally
and test that it has the databases. Here is an example where I am starting a container at port 3307 (3306
is the default MySQL port but I already have MySQL running at the host machine):

```bash
docker pull quay.io/juampynr/drupal8_migrate:master
docker run -d --name drupal8_migrate -p 3307:3306 quay.io/juampynr/drupal8_migrate:master
mysql -h127.0.0.1 --port=3307 -uroot -p -e 'show databases;'
Enter password: 
+--------------------+
| Database           |
+--------------------+
| drupal7            |
| drupal8            |
| drupal8_config     |
| information_schema |
| mysql              |
| performance_schema |
+--------------------+
```

Next, adjust the image at `.circleci/config.yml` so CircleCI will use it for the migration job.
