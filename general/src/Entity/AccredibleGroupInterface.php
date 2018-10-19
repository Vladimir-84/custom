<?php

namespace Drupal\general\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\general\Entity\Contracts\CreatedInterface;
use Drupal\general\Entity\Contracts\ExternalInterface;
use Drupal\general\Entity\Contracts\NamedInterface;

/**
 * Provides an interface for defining Accredible group entities.
 */
interface AccredibleGroupInterface extends ContentEntityInterface, EntityChangedInterface, ExternalInterface, NamedInterface, CreatedInterface {

  /**
   * Return Courses for Accredible Group.
   *
   * @return \Drupal\general\Entity\CourseInterface[]
   *   Courses.
   */
  public function getCourses();

  /**
   * Set Courses for Accredible Group.
   *
   * @param \Drupal\general\Entity\LearningPathInterface[]|array $courses
   *   Array of Courses or Courses ids.
   */
  public function setCourses(array $courses = []);

}
