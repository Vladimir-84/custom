<?php

namespace Drupal\general\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\general\Entity\Contracts\CreatedInterface;

/**
 * Provides an interface for defining Company entities.
 */
interface StudentInterface extends ContentEntityInterface, EntityChangedInterface, CreatedInterface {

  /**
   * Gets the First name student.
   *
   * @return string
   *   First name student.
   */
  public function getFirstName(): string;

  /**
   * Sets the First name student.
   *
   * @param string $first_name
   *   The First name student.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setFirstName(string $first_name);

  /**
   * Gets the Last name student.
   *
   * @return string
   *   Last name student.
   */
  public function getLastName(): string;

  /**
   * Sets the Last name student.
   *
   * @param string $last_name
   *   The Last name student.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setLastName(string $last_name);

  /**
   * Gets the Student email.
   *
   * @return string
   *   Student email.
   */
  public function getEmail(): string;

  /**
   * Sets the Student email.
   *
   * @param string $email
   *   The Student email.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setEmail(string $email);

  /**
   * Gets the Student country.
   *
   * @return string
   *   Student country.
   */
  public function getCountry(): string;

  /**
   * Sets the Student country.
   *
   * @param string $country
   *   The Student country.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setCountry(string $country);

  /**
   * Gets the Student status.
   *
   * @return bool
   *   Student status.
   */
  public function getStatus(): bool;

  /**
   * Sets the Student status.
   *
   * @param bool $status
   *   The Student status.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setStatus(bool $status);

  /**
   * Gets the Student Full name.
   *
   * @return string
   *   Full name student.
   */
  public function getFullName(): string;

  /**
   * Sets the Company id student.
   *
   * @param \Drupal\general\Entity\CompanyInterface $company
   *   Company.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setCompany(CompanyInterface $company): StudentInterface;

  /**
   * Gets the Student company.
   *
   * @return \Drupal\general\Entity\CompanyInterface
   *   Company.
   */
  public function getCompany(): CompanyInterface;

  /**
   * Sets the Company id student.
   *
   * @param int $id
   *   Company id.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setCompanyId(int $id);

  /**
   * Gets the Company id student.
   *
   * @return int
   *   Company id.
   */
  public function getCompanyId(): int;

  /**
   * Has company.
   *
   * @return bool
   *   Bool.
   */
  public function hasCompany(): bool;

  /**
   * Set deleted status.
   *
   * @param bool $status
   *   Status.
   *
   * @return \Drupal\general\Entity\StudentInterface
   *   Student.
   */
  public function setDeleted(bool $status = FALSE): StudentInterface;

  /**
   * Deleted status.
   *
   * @return bool
   *   True if student is deleted.
   */
  public function isDeleted(): bool;

}
