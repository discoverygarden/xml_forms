<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderPreview.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderPreview extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_preview';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $form_name = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'Preview');
    $form = xml_form_builder_get_form($form, $form_state, $form_name);
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];
    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    try {
      $xml_form = new XMLForm($form_state);
      $document = $xml_form->submit($form, $form_state);
      dom_document_pretty_print($document->document);
      exit();
    }
    
      catch (Exception $e) {
      $message = 'File: ' . $e->getFile() . '</br>';
      $message .= 'Line: ' . $e->getLine() . '</br>';
      $message .= 'Error: ' . $e->getMessage() . '</br>';
      drupal_set_message(\Drupal\Component\Utility\Xss::filter($message), 'error');
      $form_state->setRebuild(TRUE);
    }
  }

}
?>
