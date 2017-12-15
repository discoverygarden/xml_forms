<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a datepicker_xml_form_elements form element.
 *
 * @FormElement("datepicker_xml_form_elements")
 */
class DatePickerXmlFormElements extends FormElement {

  /**
   *
   */
  public function getInfo() {
    $info = [
      '#input' => TRUE,
      '#process' => ['xml_form_elements_datepicker_xml_form_elements_process'],
      '#theme' => XML_FORM_ELEMENTS_DATEPICKER_THEME,
      '#theme_wrappers' => ['form_element'],
    ];

    return $info;
  }

  /**
   *
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
  }

}
