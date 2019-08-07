Dockerfile to build a MariaDB image which contains the project's databases.

# Description

This Dockerfile creates and populates 3 databases:

* drupal7: the Drupal 7 database.
* drupal8_config: the configuration-only database for Drupal 8, where there is no content, configuration has been
  imported, and configuration migrations have been executed.
* drupal8: the full Drupal 8 database, where content migrations have been executed using the drupal8_config database. 

# Usage

Assuming that you want to update any of the above databases:

1. Pull this image locally and start a container:

```bash
docker run -d --rm --name drupal8_migrate quay.io/juampynr/drupal8_migrate:master
```

2. Download databases into the `scripts/database/dumps` directory:

```bash
docker exec drupal8_migrate /usr/bin/mysqldump -u root --password=root drupal7 > scripts/database/dumps/drupal7.sql
docker exec drupal8_migrate /usr/bin/mysqldump -u root --password=root drupal8_config > scripts/database/dumps/drupal8_config.sql
docker exec drupal8_migrate /usr/bin/mysqldump -u root --password=root drupal8 > scripts/database/dumps/drupal8.sql
```

3. Overwrite any of the above database dumps with the one that you want to update.

4. Build a new image and give it a tag name. Use 'master' if you want to overwrite the image that the
team and CircleCI use.

```bash
vendor/bin/robo database:build-image some-tag
```

5. Push the resulting image to Docker Hub:

```bash
vendor/bin/robo database:push-image master
``` 

6. Wait for the tag to be available at Docker Hub by monitoring
https://quay.io/repository/juampynr/drupal8_migrate:some-tag
