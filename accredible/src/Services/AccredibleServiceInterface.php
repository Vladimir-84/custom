<?php

namespace Drupal\accredible\Services;

use ACMS\ApiInterface;

/**
 * Accredible service interface.
 */
interface AccredibleServiceInterface {

  /**
   * Endpoint.
   *
   * @var string
   */
  const API_ENDPOINT = "https://api.accredible.com/v1/";

  /**
   * Get object Api Accredible.
   *
   * @return \ACMS\ApiInterface
   *   Object Api.
   */
  public function getApi(): ApiInterface;

  /**
   * Get groups.
   *
   * @param int $page_size
   *   Page size.
   * @param int $page
   *   Page.
   * @param int $total
   *   Total records.
   *
   * @return array
   *   Array groups.
   */
  public function getCertificationGroups($page_size = 500, $page = 1, &$total = 0): array;

  /**
   * Get group.
   *
   * @param string $group_id
   *   Group id.
   *
   * @return \stdClass|null
   *   Group.
   */
  public function getCertificationGroup($group_id);

  /**
   * Get Certificates by date.
   *
   * @param string $start_date_update
   *   Start date update.
   * @param string|null $end_date_update
   *   End date update.
   * @param int $page_size
   *   Page size.
   * @param int $page
   *   Page.
   * @param int $total
   *   Total records.
   *
   * @return array
   *   Certificates.
   */
  public function getCredentialsByDate($start_date_update, $end_date_update = NULL, $page_size = 1000, $page = 1, &$total = 0);

}
