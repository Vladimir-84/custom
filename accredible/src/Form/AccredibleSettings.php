<?php

namespace Drupal\accredible\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Accredible settings for this site.
 */
class AccredibleSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'accredible_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('accredible.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Accredible API Key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('accredible.settings');
    $config->set('api_key', $form_state->getValue('api_key'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['accredible.settings'];
  }

}
