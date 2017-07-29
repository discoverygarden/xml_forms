<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderAddXsltForm.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderAddXsltForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_add_xslt_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');

    $form['name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Name'),
    ];
    $form['xslt'] = [
      '#type' => 'managed_file',
      '#required' => TRUE,
      '#title' => t('XSLT'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          'xsl xslt'
          ]
        ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Add'),
    ];

    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');
    $file = file_load($form_state->getValue(['xslt']));
    xml_form_builder_add_xslt(file_get_contents($file->uri), $form_state->getValue(['name']));
    file_delete($file);
    $form_state->set(['redirect'], XML_FORM_BUILDER_XSLTS_MENU);
  }

}
?>
