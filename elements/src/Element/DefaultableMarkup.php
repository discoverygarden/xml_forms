<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a defaultable_markup form element.
 *
 * @FormElement("defaultable_markup")
 */
class DefaultableMarkup extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = [
      '#markup' => '',
      '#pre_render' => [
        'xml_form_elements_default_value_to_markup',
        'drupal_pre_render_markup',
        'xml_form_elements_remove_empty_markup',
      ],
      '#post_render' => ['xml_form_elements_defaultable_markup'],
    ];

    return $info;
  }

  /**
   *
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }

}
