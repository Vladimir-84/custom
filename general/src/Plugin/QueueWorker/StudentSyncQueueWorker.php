<?php

namespace Drupal\general\Plugin\QueueWorker;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\general\Entity\Exception\InvalidEmailException;
use Drupal\general\Entity\Storage\StudentStorageInterface;
use Drupal\general\Services\EntityExternalResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Activity Sync Queue Worker.
 *
 * @QueueWorker(
 *   id = "sync_students",
 *   title = @Translation("Queue worker: Students Sync"),
 *   cron = {"time" = 50}
 * )
 */
class StudentSyncQueueWorker extends BaseSyncQueueWorker {

  /**
   * The student storage.
   *
   * @var \Drupal\general\Entity\Storage\StudentStorageInterface
   */
  protected $studentStorage;

  /**
   * Constructs a ActivitySyncQueueWorker object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger chanel.
   * @param \Drupal\general\Services\EntityExternalResolver $external_resolver
   *   The external resolver service.
   * @param \Drupal\general\Entity\Storage\StudentStorageInterface $student_storage
   *   The student storage.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    LoggerChannelInterface $logger,
    EntityExternalResolver $external_resolver,
    StudentStorageInterface $student_storage
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $external_resolver);
    $this->studentStorage = $student_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('general.logger.sync'),
      $container->get('general.entity.external_resolver'),
      $container->get('general.storage.student')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doProcessItem($data): ContentEntityInterface {
    $student = $this->studentStorage->loadOrCreate($data['email']);
    $student->setFirstName($data['firstname']);
    $student->setLastName($data['lastname']);
    $student->setCountry($data['country']);
    $student->setCreatedTime($data['timecreated']);
    $student->setStatus($data['suspended']);
    $student->setDeleted($data['deleted']);

    $violations = $student->get('email')->validate();
    if ($violations->count() > 0) {
      \Drupal::database()->merge('invalid_student_email')
        ->insertFields(['count' => 1])
        ->expression('count', 'count + 1')
        ->key('email', $data['email'])
        ->execute();
      throw new InvalidEmailException("Found invalid email : {$data['email']}");
    }

    return $student;
  }

  /**
   * {@inheritdoc}
   */
  protected function isValid(ContentEntityInterface $entity) {
    $violations = $entity->validate();
    // Do not count company error, because in will be added on save.
    if ($violations->filterByFields(['company'])->count() > 0) {
      $this->logger->error($violations);
      return FALSE;
    }
    return TRUE;
  }

}
