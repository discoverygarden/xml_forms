<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a creative_commons form element.
 *
 * @FormElement("creative_commons")
 */
class CreativeCommons extends FormElement {
  const BASE_LICENSE_URL = 'http://creativecommons.org/licenses/';

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#tree' => FALSE,
      '#process' => ['xml_form_elements_creative_commons_process'],
    ];

    return $info;
  }

}
