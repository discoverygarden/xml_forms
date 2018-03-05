<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a tag form element.
 *
 * @FormElement("tag")
 */
class Tag extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#process' => ['xml_form_elements_tag_process'],
      '#theme' => 'tag',
    ];

    return $info;
  }

}
