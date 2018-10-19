<?php

namespace Drupal\accredible\Services;

use ACMS\Api;
use ACMS\ApiInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Class AccredibleService.
 */
class AccredibleService implements AccredibleServiceInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The api.
   *
   * @var \ACMS\ApiInterface
   */
  protected $apiObj;

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * GroupService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   The Guzzle HTTP client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger channel factory.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ConfigFactoryInterface $configFactory,
    ClientInterface $httpClient,
    LoggerChannelFactoryInterface $loggerFactory
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
    $this->httpClient = $httpClient;
    $this->loggerFactory = $loggerFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function getApi(): ApiInterface {
    if (!$this->apiObj instanceof ApiInterface) {
      $apiKey = $this->configFactory->get('accredible.settings')
        ->get('api_key');
      $this->apiObj = new Api($apiKey);
    }
    return $this->apiObj;
  }

  /**
   * {@inheritdoc}
   */
  public function getCertificationGroups($page_size = 500, $page = 1, &$total = 0): array {
    $groups = [];
    try {
      $response = $this->getApi()->getGroups($page_size, $page);
      if ($total == 0) {
        $total = (int) $response->meta->total_count;
      }
      foreach ($response->groups as $group) {
        if (!is_null($group->design_id)) {
          $groups[] = $group;
        }
        else {
          --$total;
        }
      }
    }
    catch (ClientException $response) {
    }
    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  public function getCertificationGroup($group_id) {
    $group = NULL;
    try {
      $group = $this->getApi()->getGroup($group_id)->group;
    }
    catch (ClientException $response) {
    }
    return $group;
  }

  /**
   * {@inheritdoc}
   */
  public function getCredentialsByDate($start_date_update, $end_date_update = NULL, $page_size = 1000, $page = 1, &$total = 0) {
    $result = [];
    $params = [
      'headers' => [
        'Authorization' => 'Token token="' . $this->getApi()->getApiKey() . '"',
      ],
    ];
    $params['query'] = [
      'start_updated_date' => $start_date_update,
      'page_size' => $page_size,
      'page' => $page,
    ];
    if (!is_null($end_date_update)) {
      $params['query']['end_updated_date'] = $end_date_update;
    }
    try {
      $request = $this->httpClient->request('GET', self::API_ENDPOINT . 'all_credentials', $params);
      $result = json_decode($request->getBody()->getContents());
      $total = (int) $result->meta->total_count;
      if ($result->meta->total_count > $page_size) {
        $message = $this->t("In the request, the number of certificates issued exceeded their requested amount. (Requested - @total_count; The - @page_size; Start date - @start_date_update; End date - @end_date_update)", [
          '@total_count' => $result->meta->total_count,
          '@page_size' => $page_size,
          '@start_date_update' => $start_date_update,
          '@end_date_update' => is_null($end_date_update) ? 'null' : $end_date_update,
        ]);
        $this->loggerFactory->get('accredible')->error($message);
      }
      $result = $result->credentials;
    }
    catch (ClientException $request) {
    }
    return $result;
  }

}
