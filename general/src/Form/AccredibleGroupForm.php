<?php

namespace Drupal\general\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\general\Entity\AccredibleGroupInterface;
use Drupal\general\Utility\Entity;
use Drupal\general\Utility\Storage\CourseStorageTrait;

/**
 * Form controller for Accredible Group edit forms.
 */
class AccredibleGroupForm extends BaseForm {

  use CourseStorageTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $exists = (bool) $this->courseStorage()->getQuery()->count()->execute();

    if ($exists) {
      /** @var \Drupal\general\Entity\AccredibleGroupInterface $entity */
      $entity = $this->getEntity();
      $form['courses'] = [
        '#type' => 'select',
        '#title' => $this->t('Courses'),
        '#options' => Entity::optionsList($this->courseStorage()
          ->getEntityTypeId()),
        '#multiple' => TRUE,
      ];
      if (!$entity->isNew()) {
        $form['courses']['#default_value'] = array_keys($entity->getCourses());
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $entity = $this->entity;
    if ($entity instanceof AccredibleGroupInterface) {
      $courses = $form_state->getValue('courses') ?? [];
      $entity->setCourses($courses);
    }
  }

  /**
   * Retrieves the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The entity type manager.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

}
