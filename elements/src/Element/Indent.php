<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a indent form element.
 *
 * Designed for the form builder UI please do not use programmatically.
 *
 * @FormElement("indent")
 */
class Indent extends FormElement {

  public function getInfo() {
    $info = [
      '#input' => FALSE,
      '#tree' => FALSE,
      '#theme_wrappers' => array('container'),
      '#attributes' => array(
        'class' => array('xml-form-elements-indent-element'),
      ),
      '#attached' => array(
        'css' => array("$module_path/css/indent.css"),
      ),
    ];

    return $info;
  }

  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }
}
