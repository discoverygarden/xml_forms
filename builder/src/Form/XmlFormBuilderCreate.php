<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderCreate.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderCreate extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_create';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $type = NULL) {
    if (isset($_POST['cancel'])) {
      drupal_goto(XML_FORM_BUILDER_MAIN_MENU);
    }
    $return = [];

    $return['#attributes'] = ['enctype' => "multipart/form-data"];

    $return['form_name'] = [
      '#type' => 'textfield',
      '#title' => t('Form Name'),
      '#required' => TRUE,
      '#maxlength' => 128,
      '#element_validate' => [
        'xml_form_builder_create_validate_name'
        ],
    ];

    if ($type == 'import') {
      $return['file'] = [
        '#type' => 'file',
        '#title' => t('Form Definition'),
        '#size' => 64,
        '#description' => t('An optional XML form definition template.'),
      ];
    }

    $return['create'] = [
      '#type' => 'submit',
      '#value' => t('Create'),
      '#name' => 'create',
    ];

    $return['cancel'] = [
      '#type' => 'submit',
      '#value' => t('Cancel'),
      '#name' => 'cancel',
    ];

    return $return;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $file_uploaded = isset($_FILES['files']['error']['file']) && ($_FILES['files']['error']['file'] == 0);
    if ($file_uploaded) {
      module_load_include('inc', 'xml_form_api', 'XMLFormDefinition');
      $filename = $_FILES['files']['tmp_name']['file'];
      $definition = new DOMDocument();
      try {
        $definition->load($filename);
      }
      
        catch (Exception $e) {
        $form_state->setErrorByName('files', t("Could not load uploaded file as XML, with error: %error.", [
          '%error' => $e->getMessage()
          ]));
      }
      try {
        $version = XMLFormDefinition::getVersion($definition);
        if (!XMLFormDefinition::isValid($definition, $version)) {
          $form_state->setErrorByName('files', t('The given form definition is not valid.'));
        }
      }
      
        catch (Exception $e) {
        $form_state->setErrorByName('files', $e->getMessage());
      }
    }
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
    $form_name = $form_state->getValue(['form_name']);
    if ($form_state->get(['clicked_button', '#name']) == 'create') {
      $definition = xml_form_builder_create_get_uploaded_file();
      $definition = $definition ? $definition : xml_form_api_get_empty_form_definition();
      if (XMLFormRepository::Create($form_name, $definition)) {
        drupal_set_message(t('Successfully created form "%name".', [
          '%name' => $form_name
          ]));
        $form_state->set(['redirect'], xml_form_builder_get_edit_form_path($form_name));
        return;
      }
      else {
        drupal_set_message(t('Failed to create form %name.', [
          '%name' => $form_name
          ]), 'error');
      }
    }
    $form_state->set(['redirect'], XML_FORM_BUILDER_MAIN_MENU);
  }

}
?>
