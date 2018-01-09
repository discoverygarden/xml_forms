<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Metadata creation form.
 */
class CreateMetadataForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_create_metadata_form';
  }

  /**
   * Form for selecting which metadata datastream to create.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $object = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/datastream.form');
    $form_state->loadInclude('islandora', 'inc', 'includes/utilities');

    $datastreams = xml_form_builder_empty_metadata_datastreams($object);
    $options = array_combine($datastreams, $datastreams);
    if ($options) {
      $form_state->set('object_id', $object->id);
      $form['dsid'] = [
        '#title' => $this->t('Datastream ID'),
        '#type' => 'select',
        '#options' => $options,
        '#description' => $this->t('Select the ID of a datastream to create new metadata.'),
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Create'),
      ];
    }
    return $form;
  }

  /**
   * Redirects the user to the xml form association form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect(
      'xml_form_builder.datastream_form',
      ['object' => $form_state->get('object_id'), 'dsid' => $form_state->getValue('dsid')]
    );
  }

}
