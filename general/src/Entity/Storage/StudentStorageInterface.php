<?php

namespace Drupal\general\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\general\Entity\StudentInterface;

/**
 * StudentStorageInterface.
 */
interface StudentStorageInterface extends ContentEntityStorageInterface {

  /**
   * Get students by company.
   *
   * @param int $company_id
   *   Company id.
   *
   * @return \Drupal\general\Entity\StudentInterface[]
   *   Students.
   */
  public function loadByCompany(int $company_id);

  /**
   * Change company to student that have first company.
   *
   * @param int $from
   *   Previous student company.
   * @param int $to
   *   New student company.
   */
  public function changeCompany(int $from, int $to);

  /**
   * Get students by email.
   *
   * @param string $email
   *   Email.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   Student.
   */
  public function loadOrCreate(string $email): StudentInterface;

}
