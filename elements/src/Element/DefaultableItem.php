<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a defaultable_item form element.
 *
 * @FormElement("defaultable_item")
 */
class DefaultableItem extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = [
      '#markup' => '',
      '#pre_render' => [
        'xml_form_elements_default_value_to_markup',
        'xml_form_elements_remove_empty_markup',
      ],
      '#theme_wrappers' => ['form_element'],
    ];

    return $info;
  }

}
