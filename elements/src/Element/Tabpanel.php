<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a tabpanel form element.
 *
 * @FormElement("tabpanel")
 */
class Tabpanel extends FormElement {

  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#process' => array('xml_form_elements_tabpanel_process'),
      '#user_data' => array('add' => TRUE, 'delete' => TRUE),
      '#theme_wrappers' => array('tabpanel'),
    ];

    return $info;
  }

  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }
}
