<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Object ingest metadata form.
 *
 * @package \Drupal\xml_form_builder\Form
 */
class XmlFormBuilderIngestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_ingest_form';
  }

  /**
   * The ingest form.
   *
   * @param array $form
   *   The Drupal form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   * @param array $association
   *   The form association.
   *
   * @return array
   *   A drupal form definition.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $configuration = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'ingest.form');
    $step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_metadata_step');
    $step_storage['association'] = $association;
    $form_name = $association['form_name'];
    $dsid = $association['dsid'];
    $object = islandora_ingest_form_get_object($form_state);
    $template = is_string($association['template']) && !empty($association['template']) ? $association['template'] : NULL;
    $xml = isset($object[$dsid]) ? $object[$dsid]->content : $template;
    $form = xml_form_builder_get_form($form, $form_state, $form_name, $xml);
    if ($form !== FALSE && $association['title_field']) {
      // Make the Object label field required.
      $title_element = $association['title_field'];
      $title_element[] = '#required';
      \Drupal\Component\Utility\NestedArray::setValue($form, $title_element, TRUE);
    }
    return $form;
  }

  /**
   * Updates the ingestable object's datastream.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_metadata_step');
    $association = $step_storage['association'];
    $object = islandora_ingest_form_get_object($form_state);
    $xml_form = new XMLForm($form_state);
    $document = $xml_form->submit($form, $form_state);
    $label = NULL;
    if ($association['title_field']) {
      $title_field = \Drupal\Component\Utility\NestedArray::getValue($form, $association['title_field']);
      $label = $title_field['#value'];
    }
    $step_storage['created'] = xml_form_builder_update_object($object, $association, $document, $label);
  }

}
