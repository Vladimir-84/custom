<?php

namespace Drupal\general\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\general\Utility\Contracts\CreatedTrait;
use Drupal\general\Utility\Storage\CompanyStorageTrait;

/**
 * Defines the Company entity.
 *
 * @ingroup general
 *
 * @ContentEntityType(
 *   id = "student",
 *   label = @Translation("Student"),
 *   handlers = {
 *     "storage" = "Drupal\general\Entity\Storage\StudentStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\general\BaseListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData\EntityViewsData",
 *     "storage" = "Drupal\general\Entity\Storage\StudentStorage",
 *
 *     "form" = {
 *       "default" = "Drupal\general\Form\BaseForm",
 *       "add" = "Drupal\general\Form\BaseForm",
 *       "edit" = "Drupal\general\Form\BaseForm",
 *       "delete" = "Drupal\general\Form\BaseDeleteForm",
 *     },
 *     "access" = "Drupal\general\Entity\Access\BaseAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "students",
 *   admin_permission = "administer student entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "first_name" = "first_name",
 *     "last_name" = "last_name",
 *     "email" = "email",
 *     "status" = "status",
 *     "company" = "company",
 *     "created" = "created",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/students/{student}",
 *     "add-form" = "/admin/content/students/add",
 *     "edit-form" = "/admin/content/students/{student}/edit",
 *     "delete-form" = "/admin/content/students/{student}/delete",
 *     "collection" = "/admin/content/students",
 *   },
 * )
 */
final class Student extends ContentEntityBase implements StudentInterface {

  use EntityChangedTrait, CreatedTrait;

  use CompanyStorageTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields = self::createdFieldDefinitions($fields, $entity_type);

    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First name'))
      ->setDescription(t('The First name student.'))
      ->setSettings([
        'max_length' => 70,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last name'))
      ->setDescription(t('The Last name student.'))
      ->setSettings([
        'max_length' => 70,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('The email of this student.'))
      ->setRequired(TRUE)
      ->addConstraint('UserMailUnique')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
      ])
      ->setLabel(t('Status'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'boolean',
        'weight' => 0,
      ])
      ->setRequired(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['deleted'] = BaseFieldDefinition::create('boolean')
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
      ])
      ->setLabel(t('Deleted'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'boolean',
        'weight' => 0,
      ])
      ->setRequired(TRUE)
      ->setDefaultValue(FALSE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Country'))
      ->setDescription(t("The student's country."))
      ->setSettings([
        'max_length' => 70,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['company'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Company'))
      ->setDescription(t('The company.'))
      ->setSetting('target_type', 'company')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp_ago',
        'weight' => 1,
      ])
      ->setDescription(t('The time that the student was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->hasCompany()) {
      $domain = substr(strstr($this->getEmail(), '@'), 1);
      $company = $this->companyStorage()->loadOrCreate($domain);
      $this->setCompany($company);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function hasCompany(): bool {
    return !$this->get('company')->isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstName(): string {
    return $this->get('first_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFirstName(string $first_name) {
    $this->set('first_name', $first_name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastName(): string {
    return $this->get('last_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastName(string $last_name) {
    $this->set('last_name', $last_name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail(): string {
    return $this->get('email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail(string $email) {
    $this->set('email', $email);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry(): string {
    return $this->get('country')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCountry(string $country) {
    $this->set('country', $country);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus(): bool {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus(bool $status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFullName(): string {
    return $this->getFirstName() . ' ' . $this->getLastName();
  }

  /**
   * {@inheritdoc}
   */
  public function setCompany(CompanyInterface $company): StudentInterface {
    $this->set('company', $company);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCompany(): CompanyInterface {
    return $this->get('company')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getFullName();
  }

  /**
   * {@inheritdoc}
   */
  public function setCompanyId(int $id) {
    $this->set('company', $id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCompanyId(): int {
    return $this->get('company')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDeleted(bool $status = FALSE): StudentInterface {
    $this->set('deleted', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isDeleted(): bool {
    return $this->get('deleted')->value;
  }

}
