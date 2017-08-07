<?php
namespace Drupal\xml_forms;

class XmlFormBuilderCopy extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_copy';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $form_name = NULL) {
    module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
    if (isset($_POST['cancel'])) {
      drupal_goto(XML_FORM_BUILDER_MAIN_MENU);
    }
    if (!XMLFormRepository::Exists($form_name)) {
      drupal_set_message(t('Form "%name" does not exist.', [
        '%name' => $form_name
        ]), 'error');
      drupal_not_found();
      exit();
    }
    return [
      'original' => [
        '#type' => 'hidden',
        '#value' => $form_name,
      ],
      'form_name' => [
        '#type' => 'textfield',
        '#title' => t('Form Name'),
        '#required' => TRUE,
        '#element_validate' => [
          'xml_form_builder_copy_validate_name'
          ],
      ],
      'copy' => [
        '#type' => 'submit',
        '#value' => t('Copy'),
        '#name' => 'copy',
      ],
      'cancel' => [
        '#type' => 'submit',
        '#value' => t('Cancel'),
        '#name' => 'cancel',
      ],
    ];
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
    if ($form_state->get(['clicked_button', '#name']) == 'copy') {
      $original = $form_state->getValue(['original']);
      $form_name = $form_state->getValue(['form_name']);
      if (XMLFormRepository::Copy($original, $form_name)) {
        drupal_set_message(t('Successfully copied form "%name".', [
          '%name' => $form_name
          ]));
        $form_state->set(['redirect'], xml_form_builder_get_edit_form_path($form_name));
        return;
      }
      drupal_set_message(t('Failed to copy form "%name".', [
        '%name' => $form_name
        ]), 'error');
    }
    $form_state->set(['redirect'], XML_FORM_BUILDER_MAIN_MENU);
  }

}
