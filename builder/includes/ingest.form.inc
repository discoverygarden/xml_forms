<?php

/**
 * @file
 * The ingest metadata form helpers.
 */

/**
 * Alter the form such that previous will unset any stored form.
 */
function xml_form_builder_form_xml_form_builder_ingest_form_alter(array &$form, $form_state) {
  if (isset($form['prev']['#submit'])) {
    array_unshift($form['prev']['#submit'], 'xml_form_builder_ingest_form_previous_submit');
  }
}

/**
 * Unset the form storage.
 */
function xml_form_builder_ingest_form_previous_submit(array $form, array &$form_state) {
  unset($form_state['storage'][FormStorage::STORAGE_ROOT]);
  $step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_metadata_step');
  unset($step_storage['values']);
}

/**
 * Undoes the submit, purging any datastreams created by this step.
 *
 * @param array $form
 *   The Drupal form.
 * @param array $form_state
 *   The Drupal form state.
 */
function xml_form_builder_ingest_form_undo_submit(array $form, array &$form_state) {
  $step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_metadata_step');
  $object = islandora_ingest_form_get_object($form_state);
  foreach ($step_storage['created'] as $dsid => $created) {
    if ($created) {
      $object->purgeDatastream($dsid);
    }
  }
  unset($step_storage['created']);
  unset($step_storage['association']);
}
