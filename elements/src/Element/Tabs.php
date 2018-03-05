<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a tabs form element.
 *
 * @FormElement("tabs")
 */
class Tabs extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#process' => ['xml_form_elements_tabs_process'],
      '#theme_wrappers' => ['tabs', 'form_element'],
    ];

    return $info;
  }

}
