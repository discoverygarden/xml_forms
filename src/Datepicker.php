<?php
namespace Drupal\xml_forms;

/**
 * @file
 * Defines callbacks and resources needed for the 'datepicker' form element.
 */

class Datepicker {

  /**
   * Loads the required resources for displaying the Datepicker element.
   *
   * @staticvar bool $load
   *   Keeps us from loading the same files multiple times; while not required,
   *   it just saves some time.
   */
  public static function addRequiredResources(&$form_state) {
    static $load = TRUE;
    if ($load) {
      $form_state->loadInclude('xml_form_elements', 'inc', 'includes/Element');
      // @todo: add Element::addRequiredResources().
      Element::addUIWidgets('ui.datepicker');
      Element::addUIThemeStyles(array(
        'ui.core.css',
        'ui.datepicker.css',
        'ui.theme.css',
      ));
      Element::addJS('datepicker.js');
      $load = FALSE;
    }
  }

  /**
   * The '#process' callback for the datepicker form element.
   *
   * @param array $element
   *   The datepicker form element.
   * @param array $form_state
   *   The Drupal form state.
   * @param array $complete_form
   *   The complete Drupal form definition.
   *
   * @return array
   *   The datepicker form element.
   */
  public static function process(array $element, array &$form_state, array $complete_form = NULL) {
    self::addRequiredResources($form_state);
    return $element;
  }

}
