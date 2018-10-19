<?php

namespace Drupal\general\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\general\Entity\StudentInterface;

/**
 * StudentStorage.
 */
class StudentStorage extends SqlContentEntityStorage implements StudentStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadByCompany(int $company_id) {
    $query = $this->getQuery();
    $query->accessCheck(FALSE);
    $this->buildPropertyQuery($query, ['company' => $company_id]);
    $ids = $query->execute();
    return $this->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function changeCompany(int $from, int $to) {
    $query = $this->database->update('students');
    $query->condition('company', $from);
    $query->fields([
      'company' => $to,
    ]);
    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function loadOrCreate(string $email): StudentInterface {
    $query = $this->getQuery();
    $query->accessCheck(FALSE);
    $this->buildPropertyQuery($query, ['email' => $email]);
    $ids = $query->execute();
    if (empty($ids)) {
      /** @var \Drupal\general\Entity\StudentInterface $student */
      $student = $this->create();
      $student->setEmail($email);
      return $student;
    }
    $students = $this->loadMultiple($ids);
    return reset($students);
  }

}
