<?php

namespace Drupal\general;

use Drupal\accredible\Services\AccredibleServiceInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AccredibleCronJob.
 */
class AccredibleCronJob implements ContainerInjectionInterface {

  /**
   * Accredible api.
   *
   * @var \Drupal\accredible\Services\AccredibleServiceInterface
   */
  protected $api;

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * AccredibleCronJob constructor.
   *
   * @param \Drupal\accredible\Services\AccredibleServiceInterface $api
   *   Accredible api.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(AccredibleServiceInterface $api, QueueFactory $queue_factory, StateInterface $state) {
    $this->api = $api;
    $this->queueFactory = $queue_factory;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('accredible.api'),
      $container->get('queue'),
      $container->get('state')
    );
  }

  /**
   * Fetch certificates from accredible.
   */
  public function fetchCertificates() {
    $last_time = $this->state->get(ResponseTime::ACCREDIBLE_CERTIFICATE, 0);
    $start = date('Y-m-d\TH:i:s.u', $last_time);

    $end_time = time();
    $this->state->set(ResponseTime::ACCREDIBLE_CERTIFICATE, $end_time);
    $end = date('Y-m-d\TH:i:s.u', $end_time);

    $certificates = $this->api->getCredentialsByDate($start, $end);

    $queue = $this->queueFactory->get(Queue::SYNC_CERTIFICATE);
    foreach ($certificates as $certificate) {
      $item = [];
      $item['external_id'] = $certificate->accredible_internal_id;
      $item['grade'] = $certificate->grade;
      $item['date'] = $certificate->issued_on;
      $item['accredible_group'] = $certificate->group_id;
      $item['url'] = $certificate->url;
      $item['image'] = $certificate->seo_image;
      $item['student']['email'] = $certificate->recipient->email;
      $item['student']['name'] = trim($certificate->recipient->name);
      $queue->createItem($item);
    }
  }

  /**
   * Fetch groups from accredible.
   */
  public function fetchAccredibleGroups() {
    $responses = $this->api->getCertificationGroups();
    $queue = $this->queueFactory->get(Queue::SYNC_ACCREDIBLE_GROUP);
    foreach ($responses as $response) {
      $item = [];
      $item['external_id'] = $response->id;
      $item['name'] = $response->name;
      $queue->createItem($item);
    }
  }

}
