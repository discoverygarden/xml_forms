<?php

/**
 * @file
 * Select association form helpers.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Get the models to select from.
 *
 * @param array $associations
 *   The form associations to select from.
 *
 * @return array
 *   An associative array where the keys is the model ID and the value is the
 *   models label.
 */
function xml_form_builder_select_association_form_get_models(array $associations) {
  $get_model = function($o) {
    return $o['content_model'];
  };
  $models = array_map($get_model, $associations);
  $models = array_unique($models);
  $get_label = function($o) {
    return islandora_object_load($o)->label;
  };
  $model_labels = array_map($get_label, $models);
  return array_combine($models, $model_labels);
}

/**
 * Get the forms to select from for the given content model.
 *
 * @param array $associations
 *   The form associations to select from.
 *
 * @return array
 *   An associative array where the keys/value is the form name.
 */
function xml_form_builder_select_association_form_get_forms(array $associations, $selected_model) {
  $get_selected_models = function($o) use($selected_model) {
    return $o['content_model'] == $selected_model;
  };
  $selected_model_associations = array_filter($associations, $get_selected_models);
  $get_form = function($o) {
    return $o['form_name'];
  };
  $forms = array_map($get_form, $selected_model_associations);
  return array_combine($forms, $forms);
}

/**
 * The AJAX callback for the models select field.
 *
 * @param array $form
 *   The Drupal form.
 * @param array $form_state
 *   The Drupal form state.
 *
 * @return array
 *   A Drupal form definition.
 */
function xml_form_builder_select_association_form_ajax_callback(array $form, array $form_state) {
  if ($form['available_forms']['#access']) {
    return $form['available_forms'];
  }
  else {
    return array(
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => array(
        'id' => 'forms_wrapper',
      ),
    );
  }
}

/**
 * Populates the association field.
 *
 * @param array $element
 *   The assocoiation element.
 * @param mixed $input
 *   The input for the element.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The Drupal form state.
 */
function xml_form_builder_select_association_form_value_callback(array $element, $input, FormStateInterface $form_state) {
  $association_matches = function($o) use($form_state) {
    return $o['content_model'] == $form_state->getValue('models') && $o['form_name'] == $form_state->getValue('available_forms');
  };
  $filtered_associations = array_filter($form_state->get('associations'), $association_matches);
  return array_pop($filtered_associations);
}
