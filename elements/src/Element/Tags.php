<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a tags form element.
 *
 * @FormElement("tags")
 */
class Tags extends FormElement {

  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#process' => ['xml_form_elements_tags_process'],
      '#theme_wrappers' => ['tags', 'form_element'],
    ];

    return $info;
  }

  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }
}
