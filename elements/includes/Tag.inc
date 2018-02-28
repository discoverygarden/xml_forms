<?php

/**
 * @file
 * Allows forms to dynamically add new tabs to a tabpanel in a form.
 */

use Drupal\Core\Form\FormStateInterface;

module_load_include('inc', 'xml_form_elements', 'includes/Element');

/**
 * Static functions that allow for theming and processing tabpanels.
 */
// @codingStandardsIgnoreLine
class Tag {

  /**
   * Constants.
   */
  const REMOVE_BUTTON = 'remove-tag';
  const EDIT_BUTTON = 'edit-tag';

  // @deprecated Constants
  // @codingStandardsIgnoreStart
  const RemoveButton = self::REMOVE_BUTTON;
  const EditButton = self::EDIT_BUTTON;
  // @codingStandardsIgnoreEnd

  /**
   * The default #process function for tags.
   *
   * Adds elements that allow for adding/remove form elements.
   *
   * @param array $element
   *   The element to process.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   * @param array $complete_form
   *   The completed form.
   *
   * @return array
   *   The processed element.
   */
// @codingStandardsIgnoreLine
  public static function Process(array $element, FormStateInterface $form_state, array $complete_form = NULL) {
    $tags = &get_form_element_parent($element, $complete_form);
    $element['#id'] = $element['#hash'];
    $element['#title'] = isset($tags['#title']) ? $tags['#title'] : FALSE;
    $element['#value'] = isset($element['#value']) ? $element['#value'] : FALSE;
    return $element;
  }

}
