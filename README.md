# Drupal 8 migrate example

[![CircleCI](https://circleci.com/gh/juampynr/drupal8-migrate.svg?style=svg)](https://circleci.com/gh/juampynr/drupal8-migrate) [![Docker Repository on Quay](https://quay.io/repository/juampynr/drupal8_migrate/status "Docker Repository on Quay")](https://quay.io/repository/juampynr/drupal8_migrate)

This repository contains a Drupal 8 project that is linked to CircleCI via a job that
runs a Drupal 7 to 8 migration every night. The resulting database of each migration is
added to a Docker image and pushed to Quay.io. 

* Here is the [list of past CircleCI jobs](https://circleci.com/gh/juampynr/drupal8-migrate).
* Here is the [list of Docker image tags](https://quay.io/repository/juampynr/drupal8_migrate?tab=history).

Further details on how this repository works will be published soon in an article at the Lullabot blog.
