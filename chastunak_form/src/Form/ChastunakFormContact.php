<?php

namespace Drupal\chastunak_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * ChastunakFormContact.
 */
class ChastunakFormContact extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'chastunak_form_and_block_contact';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Введите Ваше имя',
      ],
    ];
    $form['phone'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Введите номер телефона',
      ],
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'ПОЛУЧИТЬ ЗВОНОК',
      '#attributes' => [
        'class' => [
          'button-form',
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $phone = $form_state->getValue('phone');
    $email = 'sat.vladimir.kukarekin@gmail.com';

    if (custom_mail_send($email, $name, $phone)) {
      \Drupal::messenger()->addMessage("Ваша заявка принята!");
    };
  }

}
