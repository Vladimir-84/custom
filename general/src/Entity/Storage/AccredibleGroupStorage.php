<?php

namespace Drupal\general\Entity\Storage;

use Drupal\general\Entity\AccredibleGroupInterface;
use Drupal\general\Entity\CourseInterface;
use Drupal\general\Utility\Entity;

/**
 * Class AccredibleGroupStorage.
 */
class AccredibleGroupStorage extends EntityStorageBase implements AccredibleGroupStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadByCourse(int $id): array {
    $query = $this->database->select('accredible_group_course', 'r');
    $query->fields('r', ['accredible_group_id']);
    $query->condition('r.course_id', $id);
    $ids = $query->execute()->fetchAllKeyed(0, 0);

    return $this->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function setCourses(int $accredible_group_id, array $courses = []) {
    $course_ids = Entity::prepareId($courses, CourseInterface::class);

    $this->database->delete('accredible_group_course')
      ->condition('accredible_group_id', $accredible_group_id)
      ->execute();
    if (!empty($course_ids)) {
      $query = $this->database->insert('accredible_group_course');
      $query->fields([
        'course_id',
        'accredible_group_id',
      ]);
      foreach ($course_ids as $course_id) {
        $query->values([
          'course_id' => $course_id,
          'accredible_group_id' => $accredible_group_id,
        ]);
      }
      $query->execute();
    }

  }

  /**
   * {@inheritdoc}
   */
  public function deleteCourseRelations(array $accredible_groups) {
    $this->database->delete('accredible_group_course')
      ->condition('accredible_group_id', $accredible_groups, 'IN')
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function loadOrCreate(int $external_id): AccredibleGroupInterface {
    $query = $this->getQuery();
    $query->accessCheck(FALSE);
    $this->buildPropertyQuery($query, ['external_id' => $external_id]);
    $ids = $query->execute();
    if (empty($ids)) {
      /** @var \Drupal\general\Entity\AccredibleGroupInterface $group */
      $group = $this->create();
      $group->setExternalId($external_id);
    }
    else {
      $groups = $this->loadMultiple($ids);
      $group = reset($groups);
    }
    return $group;
  }

}
