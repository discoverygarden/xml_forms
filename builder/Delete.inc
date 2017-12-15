<?php

/**
 * @file
 * Callbacks and functions used in the Delete Form Page.
 */

/**
 * Get the Delete Page Form.
 *
 * @param array $form
 *   The Drupal form.
 * @param array $form_state
 *   The Drupal Form State.
 * @param string $form_name
 *   The name of the form to delete.
 *
 * @return array
 *   The Drupal Form.
 */
function xml_form_builder_delete($form, array &$form_state, $form_name) {
  module_load_include('inc', 'xml_form_builder', 'XMLFormDatabase');
  if (!XMLFormDatabase::Exists($form_name)) {
    drupal_set_message(t('Form "%name" does not exist.', ['%name' => $form_name]), 'error');
    drupal_not_found();
    exit();
  }
  return [
    'form_name' => [
      '#type' => 'hidden',
      '#value' => $form_name,
    ],
    'description' => [
      '#type' => 'markup',
      '#prefix' => '<div>',
      '#markup' => t('Are you sure you want to delete the form <strong>%name</strong> and all related form associations? This action is irreversible.', ['%name' => $form_name]),
      '#suffix' => '</div>',
    ],
    'delete' => [
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#name' => 'delete',
    ],
    'cancel' => [
      '#type' => 'submit',
      '#value' => t('Cancel'),
      '#name' => 'cancel',
    ],
  ];
}

/**
 * Submit the delete form. Deletes a Form definition within the database.
 *
 * Returns the user to the main menu.
 *
 * @param array $form
 *   The Drupal Form.
 * @param array $form_state
 *   The Drupal Form State.
 */
function xml_form_builder_delete_submit(array $form, array &$form_state) {
  if ($form_state['clicked_button']['#name'] == 'delete') {
    module_load_include('inc', 'xml_form_builder', 'XMLFormDatabase');
    module_load_include('inc', 'xml_form_builder', 'includes/associations');
    $form_name = $form_state['values']['form_name'];
    if (XMLFormDatabase::Delete($form_name)) {
      foreach (xml_form_builder_get_associations([$form_name]) as $assoc) {
        db_delete('xml_form_builder_form_associations')
          ->condition('id', intval($assoc['id']))
          ->execute();
        drupal_set_message(t('Deleted the association ID:%id with the form %form_name.', [
          '%id' => $assoc['id'],
          '%form_name' => $form_name,
        ]));
      }
      drupal_set_message(t('Successfully deleted form "%name".', ['%name' => $form_name]));
    }
    else {
      drupal_set_message(t('Failed to delete form "%name".', ['%name' => $form_name]), 'error');
    }
  }
  $form_state['redirect'] = XML_FORM_BUILDER_MAIN_MENU;
}
