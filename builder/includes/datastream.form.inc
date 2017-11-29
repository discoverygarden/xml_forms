<?php

/**
 * @file
 * Allows for adding / editing XML datastreams.
 */

use Drupal\Core\Form\FormStateInterface;

use Drupal\xml_form_builder\Form\XmlFormBuilderSelectAssociationForm;

/**
 * Gets the form associations for the given content models and datastream.
 *
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The drupal form state.
 * @param string[] $models
 *   The content models to match against.
 * @param string $dsid
 *   The Datastream ID to match against.
 *
 * @return array
 *   An array of found associations.
 */
function xml_form_builder_datastream_form_get_associations(FormStateInterface $form_state, $models, $dsid) {
  module_load_include('inc', 'xml_form_builder', 'includes/associations');
  if ($form_state->get('associations')) {
    return $form_state->get('associations');
  }
  return xml_form_builder_get_associations([], $models, [$dsid]);
}

/**
 * Gets the selected form association.
 *
 * If only one association exists, it is used.
 *
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The drupal form state.
 * @param array $associations
 *   A passed in array of associations.
 *
 * @return array|NULL
 *   The selected association, or NULL if no association is selected.
 */
function xml_form_builder_datastream_form_get_association(FormStateInterface $form_state, array $associations) {
  if ($form_state->get('association')) {
    return $form_state->get('association');
  }
  return count($associations) == 1 ? current($associations) : NULL;
}

/**
 * Gets the select associations form.
 *
 * @param array $form
 *   The drupal form.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The drupal form state.
 * @param array $associations
 *   The associations to choose from.
 *
 * @return array
 *   The form for selecting the association to edit the datastream.
 */
function xml_form_builder_datastream_form_select_association_form(array $form, FormStateInterface $form_state, array $associations) {
  $form_state->set('associations', $associations);
  $select_form = new XmlFormBuilderSelectAssociationForm();
  $form = $select_form->buildForm($form, $form_state, $associations);
  $form['next'] = [
    '#type' => 'submit',
    '#value' => t('Next'),
    '#name' => 'next',
    '#submit' => ['xml_form_builder_datastream_form_select_association_form_submit'],
  ];
  return $form;
}

/**
 * Selects an association.
 *
 * @param array $form
 *   The drupal form.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The drupal form state.
 */
function xml_form_builder_datastream_form_select_association_form_submit(array $form, FormStateInterface $form_state) {
  $model = $form_state->getValue('models');
  $form_name = $form_state->getValue('available_forms');
  $association_matches = function($o) use($model, $form_name) {
    return $o['content_model'] == $model && $o['form_name'] == $form_name;
  };
  $matching_associations = array_filter($form_state->get('associations'), $association_matches);
  $form_state->set('association', array_pop($matching_associations));
  $form_state->setRebuild();
}

/**
 * The edit datastream form.
 *
 * @param array $form
 *   The drupal form.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The drupal form state.
 * @param AbstractObject $object
 *   The object to which the datastream belongs.
 * @param array $association
 *   The association to use for creating the edit datastream form.
 *
 * @return array
 *   The Drupal form.
 */
function xml_form_builder_datastream_form_metadata_form(array $form, FormStateInterface $form_state, AbstractObject $object, array $association) {
  $form_state->set('pid', $object->id);
  $form_state->set('association', $association);
  $form_name = $association['form_name'];
  $dsid = $association['dsid'];
  $xml = NULL;
  if (isset($object[$dsid])) {
    $xml = $object[$dsid]->content;
  }
  $form = xml_form_builder_get_form($form, $form_state, $form_name, $xml);
  // Make the Object label field required.
  if ($association['title_field']) {
    $title_field_parents = $association['title_field'];
    $title_field_parents[] = '#required';
    \Drupal\Component\Utility\NestedArray::setValue($form, $title_field_parents, TRUE);
  }
  $form['update'] = [
    '#type' => 'submit',
    '#value' => t('Update'),
    '#name' => 'update',
    '#submit' => ['xml_form_builder_datastream_form_metadata_form_submit'],
  ];
  return $form;
}

/**
 * Updates the datastream's content with generated xml from the metadata form.
 *
 * Also updates the objects label, and applies a transform to update the DC.
 *
 * @param array $form
 *   The drupal form.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The drupal form state.
 */
function xml_form_builder_datastream_form_metadata_form_submit(array $form, FormStateInterface $form_state) {
  $object = islandora_object_load($form_state->get('pid'));
  $association = $form_state->get('association');
  $label = NULL;
  if ($association['title_field']) {
    $title_field = \Drupal\Component\Utility\NestedArray::getValue($form, $association['title_field']);
    $label = $title_field['#value'];
  }
  $xml_form = new XMLForm($form_state);
  $document = $xml_form->submit($form, $form_state);
  xml_form_builder_update_object($object, $association, $document, $label);
  $form_state->setRedirect('islandora.view_object', ['object' => $object->id]);
}

/**
 * Get empty metadata datastreams on an object.
 */
function xml_form_builder_empty_metadata_datastreams($object) {
  module_load_include('inc', 'xml_form_builder', 'includes/associations');
  $datastreams = [];
  foreach (islandora_get_datastreams_requirements($object) as $dsid => $datastream) {
    if (!isset($object[$dsid])) {
      $datastreams[] = $dsid;
    }
  }
  $filtered_datastreams = [];
  foreach (xml_form_builder_get_associations([], $object->models, $datastreams) as $value) {
    $filtered_datastreams[] = $value['dsid'];
  }
  return $filtered_datastreams;
}
