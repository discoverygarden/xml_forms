<?php

namespace Drupal\xml_form_elements\Element;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a creative_commons form element.
 *
 * @FormElement("creative_commons")
 */
class CreativeCommons extends FormElement {

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

  /**
   * Value callback for creative commons element.
   *
   * Input isn't set in form builder edits. Data from the process function can't
   * be relied on to be available in value callbacks because Drupal caches before
   * the element is processed.
   *
   * @param array $element
   *   The element.
   * @param array $input
   *   The input of the element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return string
   *   The license URI.
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_elements', 'inc', 'includes/creative_commons');
    if (isset($input) && $input && Url::fromRoute("<current>")->toString() != 'system/ajax') {
      $storage = $form_state->getStorage();
      if (isset($storage['xml_form_elements'][$element['#name']]['license_uri'])) {
        return $storage['xml_form_elements'][$element['#name']]['license_uri'];
      }
      else {
        $license_fieldset = $input['license_fieldset'];
        $allow_commercial = $license_fieldset['allow_commercial'];
        $allow_modifications = $license_fieldset['allow_modifications'];
        $license_jurisdiction = $license_fieldset['license_jurisdiction'];
        return xml_form_elements_creative_commons_value($allow_commercial, $allow_modifications, $license_jurisdiction);
      }
    }
  }

}
