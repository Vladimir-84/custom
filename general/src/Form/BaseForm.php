<?php

namespace Drupal\general\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for entity edit forms.
 */
class BaseForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label @type.', [
          '%label' => $entity->label(),
          '@type' => $entity->getEntityType()->getLabel(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label @type.', [
          '%label' => $entity->label(),
          '@type' => $entity->getEntityType()->getLabel(),
        ]));
    }
    $form_state->setRedirect("entity.{$entity->getEntityTypeId()}.canonical", [$entity->getEntityTypeId() => $entity->id()]);

  }

}
