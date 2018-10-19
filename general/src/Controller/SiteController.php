<?php

namespace Drupal\general\Controller;

use Drupal\accredible\Services\AccredibleServiceInterface;
use Drupal\automation_academy\Services\ApiInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Queue\QueueFactory;
use Drupal\general\Queue;
use Drupal\general\ResponseTime;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SiteController.
 */
class SiteController extends ControllerBase {

  const STATE_INITIALIZE = 'site_initialize';

  /**
   * Automation academy api.
   *
   * @var \Drupal\automation_academy\Services\ApiInterface
   */
  protected $automationAcademyApi;

  /**
   * Queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * Accredible api.
   *
   * @var \Drupal\accredible\Services\AccredibleServiceInterface
   */
  protected $accredibleApi;

  /**
   * SiteController constructor.
   *
   * @param \Drupal\automation_academy\Services\ApiInterface $automation_academy_api
   *   Automation academy api.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   Queue factory.
   * @param \Drupal\accredible\Services\AccredibleServiceInterface $accredible_api
   *   Accredible api.
   */
  public function __construct(ApiInterface $automation_academy_api, QueueFactory $queue_factory, AccredibleServiceInterface $accredible_api) {
    $this->automationAcademyApi = $automation_academy_api;
    $this->queueFactory = $queue_factory;
    $this->accredibleApi = $accredible_api;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('automation_academy.api'),
      $container->get('queue'),
      $container->get('accredible.api')
    );
  }

  /**
   * Initialize site.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   Response.
   */
  public function initialize() {
    $initialized = $this->state()->get(SiteController::STATE_INITIALIZE, FALSE);
    if ($initialized) {
      $this->messenger()->addMessage($this->t('Site already initialized.'));
      return $this->redirect('system.admin_config_system');
    }
    set_time_limit(0);
    $time = microtime(TRUE);
    $this->doFetch('getStudents', Queue::SYNC_STUDENT, ResponseTime::STUDENT);

    $this->fetchAccredibleGroups();

    $this->doFetch('getCourses', Queue::SYNC_COURSE, ResponseTime::COURSE);

    $this->doFetch('getLearningPaths', Queue::SYNC_LEARNING_PATH, ResponseTime::LEARNING_PATH);

    $this->doFetch('getActivities', Queue::SYNC_ACTIVITY, ResponseTime::ACTIVITY);

    $this->doFetch('getGrades', Queue::SYNC_GRADE, ResponseTime::GRADE);

    $this->doFetch('getCourseProgress', Queue::SYNC_COURSE_PROGRESS, ResponseTime::COURSE_PROGRESS);

    $this->fetchCertificates();

    $complete_fetch = microtime(TRUE) - $time;

    $this->state()->set(SiteController::STATE_INITIALIZE, TRUE);
    $this->messenger()->addMessage($this->t('Site initialized. Time spend : @time', ['@time' => $complete_fetch]));
    return $this->redirect('system.admin_config_system');
  }

  /**
   * Fetch data from automation academy.
   *
   * @param string $method
   *   Api method.
   * @param string $queue_key
   *   Queue.
   * @param string $state_key
   *   State variable.
   */
  protected function doFetch(string $method, string $queue_key, string $state_key) {
    if (!method_exists($this->automationAcademyApi, $method)) {
      $class = get_class($this->automationAcademyApi);
      throw new \BadMethodCallException("Method {$method} not exist in {$class}.");
    }
    $count = 0;
    $page = 1;
    $per_page = 500;
    $time = time();
    $records = $this->automationAcademyApi->$method(0, $page, $per_page, $count);
    $queue = $this->queueFactory->get($queue_key);
    foreach ($records as $record) {
      $queue->createItem($record);
    }
    while ($count > $page * $per_page) {
      ++$page;
      $time = time();
      $records = $this->automationAcademyApi->$method(0, $page, $per_page, $count);
      $queue = $this->queueFactory->get($queue_key);
      foreach ($records as $record) {
        $queue->createItem($record);
      }
    }
    $this->state()->set($state_key, $time);
  }

  /**
   * Fetch groups from accredible.
   */
  protected function fetchAccredibleGroups() {
    $page = 1;
    $page_size = 500;
    $total = 0;
    $groups = $this->accredibleApi->getCertificationGroups($page_size, $page, $total);
    $queue = $this->queueFactory->get(Queue::SYNC_ACCREDIBLE_GROUP);
    foreach ($groups as $group) {
      $item = [];
      $item['external_id'] = $group->id;
      $item['name'] = $group->name;
      $queue->createItem($item);
    }
    while ($total > $page * $page_size) {
      ++$page;
      $groups = $this->accredibleApi->getCertificationGroups($page_size, $page, $total);
      foreach ($groups as $group) {
        $item = [];
        $item['external_id'] = $group->id;
        $item['name'] = $group->name;
        $queue->createItem($item);
      }
    }
  }

  /**
   * Fetch certificates from accredible.
   */
  public function fetchCertificates() {
    $start = date('Y-m-d\TH:i:s.u', 0);

    $page_size = 500;
    $total = 0;
    $page = 1;
    $queue = $this->queueFactory->get(Queue::SYNC_CERTIFICATE);

    $time = time();
    $certificates = $this->accredibleApi->getCredentialsByDate($start, NULL, $page_size, $page, $total);
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

    while ($total > $page * $page_size) {
      ++$page;
      usleep(1000);
      $time = time();
      $certificates = $this->accredibleApi->getCredentialsByDate($start, NULL, $page_size, $page, $total);
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

    $this->state()->set(ResponseTime::ACCREDIBLE_CERTIFICATE, $time);
  }

}
