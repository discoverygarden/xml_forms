<?php
namespace Drupal\xml_forms;

class XmlFormBuilderDatastreamForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_datastream_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, AbstractObject $object = NULL, $dsid = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/datastream.form');
    // Leave this here for legacy reasons.
    $form_state->set(['datastream'], isset($object[$dsid]) ? $object[$dsid] : FALSE);
    $associations = xml_form_builder_datastream_form_get_associations($form_state, $object->models, $dsid);
    $association = xml_form_builder_datastream_form_get_association($form_state, $associations);
    return isset($association) ?
      xml_form_builder_datastream_form_metadata_form($form, $form_state, $object, $association) :
      xml_form_builder_datastream_form_select_association_form($form, $form_state, $associations);
  }

}
