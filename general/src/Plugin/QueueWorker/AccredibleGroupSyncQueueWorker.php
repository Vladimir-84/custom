<?php

namespace Drupal\general\Plugin\QueueWorker;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\general\Entity\Storage\AccredibleGroupStorageInterface;
use Drupal\general\Services\EntityExternalResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Activity Sync Queue Worker.
 *
 * @QueueWorker(
 *   id = "sync_accredible_groups",
 *   title = @Translation("Queue worker: Accredible Group Sync"),
 *   cron = {"time" = 50}
 * )
 */
class AccredibleGroupSyncQueueWorker extends BaseSyncQueueWorker {

  /**
   * The accredible group storage.
   *
   * @var \Drupal\general\Entity\Storage\AccredibleGroupStorageInterface
   */
  protected $accredibleGroupStorage;

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
   * @param \Drupal\general\Entity\Storage\AccredibleGroupStorageInterface $accredible_group_storage
   *   The accredible group storage.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    LoggerChannelInterface $logger,
    EntityExternalResolver $external_resolver,
    AccredibleGroupStorageInterface $accredible_group_storage
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $external_resolver);
    $this->accredibleGroupStorage = $accredible_group_storage;
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
      $container->get('general.storage.accredible_group')
      $container->get('general.storage.accredible_group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doProcessItem($data): ContentEntityInterface {
    $accredible_group = $this->accredibleGroupStorage->loadOrCreate($data['external_id']);
    $accredible_group->setName($data['name']);
    return $accredible_group;
  }

}
