<?php

/**
 * @file
 * Callbacks and functions used in the Copy Form Page.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Validates the name Drupal Form Element.
 *
 * Ensures that the name doesn't match any existing form names.
 *
 * @param array $element
 *   The name Drupal Form Element.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The Drupal Form State.
 */
function xml_form_builder_copy_validate_name(array $element, FormStateInterface $form_state) {
  module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
  $form_name = $form_state->getValue('form_name');
  if (XMLFormRepository::Exists($form_name)) {
    $error_msg = t('The given form name "%name" is already in use. Form names must be unique.', ['%name' => $form_name]);
    $form_state->setErrorByName('form_name', $error_msg);
  }
}
