<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a creative_commons form element.
 *
 * @FormElement("creative_commons")
 */
class CreativeCommons extends FormElement {

  /**
   *
   */
  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#tree' => FALSE,
      '#process' => ['xml_form_elements_creative_commons_process'],
      '#value_callback' => 'xml_form_elements_creative_commons_value_callback',
    ];

    return $info;
  }

  /**
   *
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }

}
