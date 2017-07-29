<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderXsltsForm.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderXsltsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_xslts_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');

    $form['xml_form_builder_xslts_vertical_tabs'] = ['#type' => 'vertical_tabs'];
    $form['xml_form_builder_xslts_vertical_tabs']['xslts'] = [
      '#type' => 'fieldset',
      '#title' => t('XSLTs'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['dc_transforms'] = [
      '#type' => 'fieldset',
      '#title' => t('Default DC Transforms'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $xslts = xml_form_builder_get_xslts();
    $xslts_options = [];
    foreach ($xslts as $xslt) {
      $xslts_options[$xslt['xslt_id']] = [$xslt['name']];
    }
    $xslts_header = ['XSLT'];

    $mappings = xml_form_builder_get_default_dc_xslt_mappings_with_xslt_name();
    $mappings_options = [];
    foreach ($mappings as $mapping) {
      $mappings_options[$mapping['id']] = [
        $mapping['name'],
        $mapping['content_model'],
        $mapping['dsid'],
        $mapping['xslt_name'],
      ];
    }
    $mappings_header = ['Default', 'Content Model', 'DSID', 'XSLT'];

    $form['xml_form_builder_xslts_vertical_tabs']['xslts']['xslts_table'] = [
      '#type' => 'tableselect',
      '#header' => $xslts_header,
      '#options' => $xslts_options,
      '#empty' => t('No XSLTs available.'),
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['xslts']['delete'] = [
      '#type' => 'submit',
      '#value' => t('Delete XSLT'),
    ];

    $form['xml_form_builder_xslts_vertical_tabs']['dc_transforms']['transforms_table'] = [
      '#type' => 'tableselect',
      '#header' => $mappings_header,
      '#options' => $mappings_options,
      '#empty' => t('No defaults set.'),
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['dc_transforms']['delete'] = [
      '#type' => 'submit',
      '#value' => t('Delete Default'),
    ];

    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');
    if (in_array('xslts', $form_state->get(['clicked_button', '#array_parents']))) {
      foreach ($form_state->getValue(['xslts_table']) as $uid => $delete) {
        if ($delete) {
          if (xml_form_builder_xslt_is_a_default($uid)) {
            if (!isset($message_set)) {
              $form_state->setErrorByName('xslts_table', t('Please delete any transforms using the XSLTs first.'));
              $message_set = TRUE;
            }
          }
        }
      }
      if (!array_filter($form_state->getValue(['xslts_table']))) {
        $form_state->setErrorByName('xslts_table', t('Please select an XSLT to delete.'));
      }
    }
    elseif (in_array('dc_transforms', $form_state->get([
      'clicked_button',
      '#array_parents',
    ]))) {
      if (!array_filter($form_state->getValue(['transforms_table']))) {
        $form_state->setErrorByName('transforms_table', t('Please select a transform to delete.'));
      }
    }
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    module_load_include('inc', 'xml_form_builder', 'includes/db');
    if (in_array('dc_transforms', $form_state->get(['clicked_button', '#array_parents']))) {
      foreach ($form_state->getValue(['transforms_table']) as $uid => $delete) {
        if ($delete) {
          xml_form_builder_remove_default_dc_transform_mapping($uid);
        }
      }
    }
    elseif (in_array('xslts', $form_state->get(['clicked_button', '#array_parents']))) {
      foreach ($form_state->getValue(['xslts_table']) as $uid => $delete) {
        if ($delete) {
          xml_form_builder_remove_xslt($uid);
        }
      }
    }
  }

}
?>
