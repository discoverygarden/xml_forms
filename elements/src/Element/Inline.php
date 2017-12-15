<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a inline form element.
 *
 * Designed for the form builder UI please do not use programmatically.
 *
 * @FormElement("inline")
 */
class Inline extends FormElement {

  /**
   *
   */
  public function getInfo() {
    $info = [
      '#input' => FALSE,
      '#tree' => FALSE,
      '#theme_wrappers' => ['container'],
      '#attributes' => [
        'class' => ['xml-form-elements-inline-element'],
      ],
      '#attached' => [
        'library' => ['xml_form_elements/inline-css'],
      ],
    ];

    return $info;
  }

  /**
   *
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }

}
