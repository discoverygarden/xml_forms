<?php
namespace Drupal\xml_forms;

class Tabs {

  /**
   * Loads the required resources for displaying the Tabs element.
   *
   * @param array $form_state
   *   The Drupal form state.
   *
   * @staticvar bool $load
   *   Keeps us from loading the same files multiple times. While not required
   *   it just saves some time.
   */
  public static function addRequiredResources(array &$form_state) {
    static $load = TRUE;
    if ($load) {
      $form_state->loadInclude('xml_form_elements', 'inc', 'includes/Element');
      Element::addUIWidgets('ui.tabs');
      Element::addUIThemeStyles(array(
        'ui.core.css',
        'ui.tabs.css',
        'ui.theme.css',
      ));
      Element::addJS('tabs.js');
      Element::addCSS('tabs.css');
      $load = FALSE;
    }
  }

  /**
   * Processes the element.
   *
   * @param array $element
   *   The tabs element.
   * @param array $form_state
   *   The Drupal form state.
   * @param array $complete_form
   *   The completed form.
   *
   * @return array
   *   The processed tabs element.
   */
  public static function process(array $element, array &$form_state, array $complete_form = NULL) {
    self::addRequiredResources($form_state);
    $element['#prefix'] = "<div class='clear-block' id='{$element['#hash']}'>";
    $element['#suffix'] = '</div>';
    return $element;
  }

  /**
   * Checks if a child element has the 'tabpanel' #type.
   *
   * @param array $child
   *   The child to determine.
   *
   * @return bool
   *   TRUE if it is a tabpanel, FALSE otherwise.
   */
  public static function FilterChildren(array $child) {
    $ret = ($child['#type'] == 'tabpanel') ? TRUE : FALSE;
    return $ret;
  }

}
