<?php

namespace Drupal\general\Entity\Storage;

use Drupal\general\Entity\AccredibleGroupInterface;

/**
 * Interface AccredibleGroupStorageInterface.
 */
interface AccredibleGroupStorageInterface extends EntityStorageBaseInterface {

  /**
   * Loads Accredible Groups for course.
   *
   * @param int $id
   *   Course id.
   *
   * @return \Drupal\general\Entity\AccredibleGroupInterface[]
   *   Accredible Groups
   */
  public function loadByCourse(int $id): array;

  /**
   * Set Courses for Accredible Group.
   *
   * @param int $id
   *   Accredible Group id.
   * @param \Drupal\general\Entity\CourseInterface[]|array $courses
   *   Array of Courses or Courses ids.
   *
   * @throws \Exception
   */
  public function setCourses(int $id, array $courses = []);

  /**
   * Remove all relations between Accredible Group and Courses.
   *
   * @param array $accredible_groups
   *   Array of Accredible Group ID.
   */
  public function deleteCourseRelations(array $accredible_groups);

  /**
   * Get group by external id.
   *
   * @param int $external_id
   *   External id.
   *
   * @return \Drupal\general\Entity\AccredibleGroupInterface
   *   Group.
   */
  public function loadOrCreate(int $external_id): AccredibleGroupInterface;

}
