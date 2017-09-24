<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a tag form element.
 *
 * @FormElement("tag")
 */
class Tag extends FormElement {

  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#process' => array('xml_form_elements_tag_process'),
      '#theme' => 'tag',
    ];

    return $info;
  }

  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }
}
