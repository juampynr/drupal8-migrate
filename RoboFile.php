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

}
