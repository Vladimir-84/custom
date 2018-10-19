<?php

namespace Drupal\chastunak_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'chastunak_form' block.
 *
 * @Block(
 *   id = "chastunak_form_block",
 *   admin_label = "Chastunak form and block",
 *   category = "Custom"
 * )
 */
class ChastunakBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()
      ->getForm('Drupal\chastunak_form\Form\ChastunakForm');
    return $form;
  }

}
