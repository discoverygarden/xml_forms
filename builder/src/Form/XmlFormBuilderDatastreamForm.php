<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Datastream form.
 */
class XmlFormBuilderDatastreamForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_datastream_form';
  }

  /**
   * Datastream form.
   *
   * Displays a select association form if more than one association is defined
   * for the given datastream.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $object = NULL, $dsid = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/datastream.form');

    // Leave this here for legacy reasons.
    $storage = $form_state->getStorage();
    $form_state->set('datastream', isset($object[$dsid]) ? $object[$dsid] : FALSE);
    $form_state->setStorage(array_merge($storage, ['pid' => $object->id, 'dsid' => $dsid]));
    $associations = xml_form_builder_datastream_form_get_associations($form_state, $object->models, $dsid);
    $association = xml_form_builder_datastream_form_get_association($form_state, $associations);
    return isset($association) ?
      xml_form_builder_datastream_form_metadata_form($form, $form_state, $object, $association) :
      xml_form_builder_datastream_form_select_association_form($form, $form_state, $associations);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/datastream.form');

    $storage = $form_state->getStorage();
    $object = islandora_object_load($storage['pid']);
    $associations = xml_form_builder_datastream_form_get_associations($form_state, $object->models, $storage['dsid']);
    $association = xml_form_builder_datastream_form_get_association($form_state, $associations);
    if ($association) {
      xml_form_builder_datastream_form_metadata_form($form, $form_state);
    }
    else {
      xml_form_builder_datastream_form_select_association_form($form, $form_state);
    }
  }

}
