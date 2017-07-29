<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderAddDefaultDcTransformMappingForm.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderAddDefaultDcTransformMappingForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_add_default_dc_transform_mapping_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');
    $xslts = xml_form_builder_get_xslts();
    $options = [];
    foreach ($xslts as $xslt) {
      $options[$xslt['xslt_id']] = $xslt['name'];
    }
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#required' => TRUE,
    ];
    $form['content_model'] = [
      '#type' => 'textfield',
      '#title' => t('Content Model'),
      '#required' => TRUE,
      '#autocomplete_path' => ISLANDORA_CONTENT_MODELS_AUTOCOMPLETE,
    ];
    $form['DSID'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('DSID'),
    ];
    $form['xslt'] = [
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => t('XSLT'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Add'),
    ];
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');
    if (xml_form_builder_get_default_transform($form_state->getValue([
      'content_model'
      ]), $form_state->getValue(['DSID']))) {
      $form_state->setErrorByName('content_model', t('There already exists a mapping for this content model and DSID.'));
      $form_state->setErrorByName('DSID', '');
    }
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');
    xml_form_builder_add_default_dc_transform_mapping($form_state->getValue(['content_model']), $form_state->getValue(['DSID']), $form_state->getValue(['xslt']), $form_state->getValue(['name']));
    $form_state->set(['redirect'], XML_FORM_BUILDER_XSLTS_MENU);
  }

}
?>
