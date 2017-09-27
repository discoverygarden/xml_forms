<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default DC transforms form.
 */
class XmlFormBuilderXsltsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_xslts_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');

    $form['xml_form_builder_xslts_vertical_tabs']['tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'xslts',
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['xslts'] = [
      '#type' => 'details',
      '#title' => $this->t('XSLTs'),
      '#group' => 'tabs',
      '#open' => TRUE,
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['dc_transforms'] = [
      '#type' => 'details',
      '#group' => 'tabs',
      '#title' => $this->t('Default DC Transforms'),
      '#open' => TRUE,
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
      '#empty' => $this->t('No XSLTs available.'),
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['xslts']['delete'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete XSLT'),
    ];

    $form['xml_form_builder_xslts_vertical_tabs']['dc_transforms']['transforms_table'] = [
      '#type' => 'tableselect',
      '#header' => $mappings_header,
      '#options' => $mappings_options,
      '#empty' => $this->t('No defaults set.'),
    ];
    $form['xml_form_builder_xslts_vertical_tabs']['dc_transforms']['delete'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete Default'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');
    if (in_array('xslts', $form_state->getTriggeringElement()['#array_parents'])) {
      foreach ($form_state->getValue('xslts_table') as $uid => $delete) {
        if ($delete) {
          if (xml_form_builder_xslt_is_a_default($uid)) {
            if (!isset($message_set)) {
              $form_state->setErrorByName('xslts_table', $this->t('Please delete any transforms using the XSLTs first.'));
              $message_set = TRUE;
            }
          }
        }
      }
      if (!array_filter($form_state->getValue('xslts_table'))) {
        $form_state->setErrorByName('xslts_table', $this->t('Please select an XSLT to delete.'));
      }
    }
    elseif (in_array('dc_transforms', $form_state->getTriggeringElement()['#array_parents'])) {
      if (!array_filter($form_state->getValue('transforms_table'))) {
        $form_state->setErrorByName('transforms_table', $this->t('Please select a transform to delete.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');
    if (in_array('dc_transforms', $form_state->getTriggeringElement()['#array_parents'])) {
      foreach ($form_state->getValue(['transforms_table']) as $uid => $delete) {
        if ($delete) {
          xml_form_builder_remove_default_dc_transform_mapping($uid);
        }
      }
    }
    elseif (in_array('xslts', $form_state->getTriggeringElement()['#array_parents'])) {
      foreach ($form_state->getValue(['xslts_table']) as $uid => $delete) {
        if ($delete) {
          xml_form_builder_remove_xslt($uid);
        }
      }
    }
  }

}
