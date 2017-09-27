<?php

/**
 * @file
 * The ingest metadata form helpers.
 */

use Drupal\objective_forms\FormStorage;
use Drupal\Core\Form\FormStateInterface;

/**
 * Alter the form such that previous will unset any stored form.
 */
function xml_form_builder_form_xml_form_builder_ingest_form_alter(array &$form, FormStateInterface $form_state) {
  if (isset($form['prev']['#submit'])) {
    array_unshift($form['prev']['#submit'], 'xml_form_builder_ingest_form_previous_submit');
  }
}

/**
 * Unset the form storage.
 */
function xml_form_builder_ingest_form_previous_submit(array $form, FormStateInterface $form_state) {
  $form_state->set(['storage', FormStorage::STORAGE_ROOT], NULL);
  $step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_metadata_step');
  unset($step_storage['values']);
}
