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
   * @param $tag_name
   *   The image tag name to use. Usually a Git branch name.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function databasePushImage($tag_name) {
    $collection = $this->collectionBuilder();

    $collection->addTask($this->taskExec('docker login quay.io'));
    $collection->addTask($this->taskExec('sleep 2'));
    $collection->addTask($this->taskExec("docker push {$this->quayRepository}:$tag_name"));

    return $collection->run();
  }

  /**
   * Dumps databases to the host machine into scripts/database/dumps.
   *
   * Assumes that there is a container running as drupal8_migrate which
   * has the databases.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  public function databaseDumpToHost() {
    $tasks = [];
    $tasks[] = $this->taskExec('docker inspect -f \'{{.State.Running}}\' drupal8_migrate');
    $tasks[]= $this->taskParallelExec()
      ->process('docker exec drupal8_migrate /usr/bin/mysqldump -u root --password=root drupal7 > scripts/database/dumps/drupal7.sql')
      ->process('docker exec drupal8_migrate /usr/bin/mysqldump -u root --password=root drupal8_config > scripts/database/dumps/drupal8_config.sql')
      ->process('docker exec drupal8_migrate /usr/bin/mysqldump -u root --password=root drupal8 > scripts/database/dumps/drupal8.sql');
    return $tasks;
  }

}
