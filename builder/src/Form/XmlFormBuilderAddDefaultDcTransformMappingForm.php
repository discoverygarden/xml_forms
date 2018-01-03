<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Add a default DC transform form.
 */
class XmlFormBuilderAddDefaultDcTransformMappingForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_add_default_dc_transform_mapping_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');
    $xslts = xml_form_builder_get_xslts();
    $options = [];
    foreach ($xslts as $xslt) {
      $options[$xslt['xslt_id']] = $xslt['name'];
    }
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];
    $form['content_model'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content Model'),
      '#required' => TRUE,
      '#autocomplete_route_name' => 'islandora.content_model_autocomplete',
    ];
    $form['DSID'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('DSID'),
    ];
    $form['xslt'] = [
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => $this->t('XSLT'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');
    if (xml_form_builder_get_default_transform($form_state->getValue(
      'content_model'
      ), $form_state->getValue('DSID'))) {
      $form_state->setErrorByName('content_model', $this->t('There already exists a mapping for this content model and DSID.'));
      $form_state->setErrorByName('DSID', '');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');
    xml_form_builder_add_default_dc_transform_mapping($form_state->getValue(['content_model']), $form_state->getValue(['DSID']), $form_state->getValue(['xslt']), $form_state->getValue(['name']));
    $form_state->setRedirect('xml_form_builder.xslts_form');
  }

}
