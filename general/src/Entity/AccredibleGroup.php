<?php

namespace Drupal\general\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\general\Entity\Storage\AccredibleGroupStorageInterface;
use Drupal\general\Utility\Contracts\CreatedTrait;
use Drupal\general\Utility\Contracts\ExternalTrait;
use Drupal\general\Utility\Contracts\NamedTrait;
use Drupal\general\Utility\Entity;
use Drupal\general\Utility\Storage\AccredibleGroupStorageTrait;
use Drupal\general\Utility\Storage\CourseStorageTrait;

/**
 * Defines the Accredible group entity.
 *
 * @ingroup general
 *
 * @ContentEntityType(
 *   id = "accredible_group",
 *   label = @Translation("Accredible group"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\general\BaseListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData\EntityViewsData",
 *     "storage" = "Drupal\general\Entity\Storage\AccredibleGroupStorage",
 *
 *     "form" = {
 *       "default" = "Drupal\general\Form\AccredibleGroupForm",
 *       "add" = "Drupal\general\Form\AccredibleGroupForm",
 *       "edit" = "Drupal\general\Form\AccredibleGroupForm",
 *       "delete" = "Drupal\general\Form\BaseDeleteForm",
 *     },
 *     "access" = "Drupal\general\Entity\Access\BaseAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "accredible_groups",
 *   admin_permission = "administer accredible group entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "external_id" = "external_id",
 *     "created" = "created",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/accredible-groups/{accredible_group}",
 *     "add-form" = "/admin/content/accredible-groups/add",
 *     "edit-form" = "/admin/content/accredible-groups/{accredible_group}/edit",
 *     "delete-form" = "/admin/content/accredible-groups/{accredible_group}/delete",
 *     "collection" = "/admin/content/accredible-groups",
 *   },
 *   field_ui_base_route = "accredible_group.settings"
 * )
 */
final class AccredibleGroup extends ContentEntityBase implements AccredibleGroupInterface {

  use CreatedTrait, EntityChangedTrait, ExternalTrait, NamedTrait;
  use CourseStorageTrait, AccredibleGroupStorageTrait;

  protected $courses;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields = self::externalFieldDefinitions($fields, $entity_type);
    $fields = self::createdFieldDefinitions($fields, $entity_type);
    $fields = self::nameFieldDefinitions($fields, $entity_type);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCourses() {
    return $this->courseStorage()->loadByAccredibleGroup($this->id());
  }

  /**
   * {@inheritdoc}
   */
  public function setCourses(array $courses = []) {
    // Clean to not store objects in property.
    $this->courses = Entity::prepareId($courses, CourseInterface::class);
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    if (!is_null($this->courses)) {
      $this->accredibleGroupStorage()->setCourses($this->id(), $this->courses);
      $this->courses = NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);
    if ($storage instanceof AccredibleGroupStorageInterface) {
      $storage->deleteCourseRelations(array_keys($entities));
    }
  }

}
