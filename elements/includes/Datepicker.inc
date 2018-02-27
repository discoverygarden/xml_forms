<?php

/**
 * @file
 * Defines callbacks and resources needed for the 'datepicker' form element.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;

/**
 * Datepicker element helper class.
 */
class Datepicker {

  /**
   * The '#process' callback for the datepicker form element.
   *
   * @param array $element
   *   The datepicker form element.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   * @param array $complete_form
   *   The complete Drupal form definition.
   *
   * @return array
   *   The datepicker form element.
   */
  public static function process(array $element, FormStateInterface $form_state, array $complete_form = NULL) {
    $element['#attached']['library'][] = 'xml_form_elements/datepicker';
    return $element;
  }

}

/**
 * Implements theme_hook().
 *
 * @param array $variables
 *   An array of theming variables.
 */
function theme_datepicker_xml_form_elements(array $variables) {
  $element = $variables['element'];
  $array_switch = is_array($element['#value']) && isset($element['#value']['value']);
  $element_val = $array_switch ? $element['#value']['value'] : $element['#value'];
  $size = empty($element['#size']) ? '' : ' size="' . $element['#size'] . '"';
  $maxlength = empty($element['#maxlength']) ? '' : ' maxlength="' . $element['#maxlength'] . '"';
  $class = ['form-text', 'datepicker'];
  $output = '';
  $element['#attributes']['class'] = array_merge(
    isset($element['#attributes']['class']) ? $element['#attributes']['class'] : [],
    $class
  );
  if (isset($element['#field_prefix'])) {
    $output .= '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ';
  }
  $attributes = new Attribute($element['#attributes']);
  $output .= '<input type="text"' . $maxlength . ' name="' . $element['#name'] . '" id="' . $element['#id'] . '"' . $size . ' value="' . Html::escape($element_val) . '"' . $attributes . ' />';
  if (isset($element['#field_suffix'])) {
    $output .= ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>';
  }
  return $output;
}
