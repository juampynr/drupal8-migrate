<?php

use Robo\Common\ResourceExistenceChecker;

/**
 * Tasks for Drupal 8 Migrate.
 *
 * @codingStandardsIgnoreStart
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD)
 */
class RoboFile extends \Robo\Tasks {

  private $quayRepository = 'quay.io/juampynr/drupal8_migrate';

  private $databases = [
    'drupal7' => 'scripts/database/dumps/drupal7.sql',
    'drupal8_config' => 'scripts/database/dumps/drupal8_config.sql',
    'drupal8' => 'scripts/database/dumps/drupal8.sql',
  ];

  /**
   * Task to build a database image.
   *
   * @param $tag_name
   *   The image tag name to use. Normally a Git branch name.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function databaseBuildImage($tag_name) {
    $collection = $this->collectionBuilder();

    $tasks[] = $this->taskExecStack()
      ->stopOnFail()
      ->exec("cd scripts/database; docker build --tag {$this->quayRepository}:$tag_name .");

    return $collection->addTaskList($tasks)->run();
  }

  /**
   * Task to push a database image to Quay.io.
   *
   * Requires that docker login has been executed previously in order to
   * have push permission.
   *
   * @param $tag_name
   *   The image tag name to use. Usually a Git branch name.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function databasePushImage($tag_name) {
    $collection = $this->collectionBuilder();

    $collection->addTask($this->taskExec("docker push {$this->quayRepository}:$tag_name"));

    return $collection->run();
  }

  /**
   * Task to install the Docker client.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function dockerInstallClient() {
    $version = '18.09.3';

    $collection = $this->collectionBuilder();

    $collection->addTask($this->taskExec('curl -L -o /tmp/docker-' . $version . '.tgz https://download.docker.com/linux/static/stable/x86_64/docker-' . $version . '.tgz'))
      ->addTask($this->taskExec('tar -xz -C /tmp -f /tmp/docker-' . $version . '.tgz'))
      ->addTask($this->taskExec('mv /tmp/docker/* /usr/bin'));

    return $collection->run();
  }

}
