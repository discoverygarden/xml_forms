<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a tabpanel form element.
 *
 * @FormElement("tabpanel")
 */
class Tabpanel extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#process' => ['xml_form_elements_tabpanel_process'],
      '#user_data' => ['add' => TRUE, 'delete' => TRUE],
      '#theme_wrappers' => ['tabpanel'],
    ];

    return $info;
  }

}
