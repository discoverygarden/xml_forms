<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderAssociationsForm.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderAssociationsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_associations_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $form_name = NULL) {
    module_load_include('inc', 'xml_form_builder', 'includes/associations');
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/associations.form');

    $associations = xml_form_builder_get_associations([$form_name], [], [], FALSE);
    $create_table_rows = function($association) {
      if (is_array($association['title_field'])) {
        $association['title_field'] = "['" . implode("']['", $association['title_field']) . "']";
      }
      else {
        $association['title_field'] = t('None');
      }
      $association['type'] = $association['in_db'] ? 'custom' : 'hook';
      return $association;
    };
    // @FIXME
    // theme() has been renamed to _theme() and should NEVER be called directly.
    // Calling _theme() directly can alter the expected output and potentially
    // introduce security issues (see https://www.drupal.org/node/2195739). You
    // should use renderable arrays instead.
    //
    //
    // @see https://www.drupal.org/node/2195739
    // $form += array(
    //     'list' => array(
    //       '#type' => 'fieldset',
    //       '#title' => t('Current associations'),
    //       '#value' => theme('xml_form_builder_association_table', array(
    //         'associations' => array_map($create_table_rows, $associations),
    //         'use_default_transforms' => \Drupal::config('xml_form_builder.settings')->get('xml_form_builder_use_default_dc_xslts'),
    //       )),
    //     ),
    //     'fieldset' => array(
    //       '#type' => 'fieldset',
    //       '#title' => t('Add Association'),
    //       '#collapsible' => TRUE,
    //       'content_model' => array(
    //         '#type' => 'textfield',
    //         '#title' => t('Content Model'),
    //         '#required' => TRUE,
    //         '#autocomplete_path' => ISLANDORA_CONTENT_MODELS_AUTOCOMPLETE,
    //         '#description' => t('The content model to associate with a form. If the content model has no decendents it will not show up in autocomplete.'),
    //         '#default_value' => isset($form_state['values']['content_model']) ? $form_state['values']['content_model'] : NULL,
    //       ),
    //       'dsid' => array(
    //         '#type' => 'textfield',
    //         '#description' => t("The datastream ID where the object's metadata is stored."),
    //         '#title' => t('Metadata Datastream ID'),
    //         '#required' => TRUE,
    //         '#default_value' => isset($form_state['values']['dsid']) ? $form_state['values']['dsid'] : NULL,
    //       ),
    //       'form_name' => array(
    //         '#type' => 'value',
    //         '#title' => t('Form Name'),
    //         '#value' => $form_name,
    //       ),
    //       'title_field' => array(
    //         '#type' => 'select',
    //         '#title' => t('Title Field'),
    //         '#description' => t("The form field for the object's label."),
    //         '#prefix' => '<div id="ahah-wrapper">',
    //         '#suffix' => '</div>',
    //         '#options' => xml_form_builder_get_title_options($form_name),
    //       ),
    //     ),
    //   );
    if (!\Drupal::config('xml_form_builder.settings')->get('xml_form_builder_use_default_dc_xslts')) {
      $form['fieldset']['transform'] = [
        '#type' => 'select',
        '#title' => t('XSL Transform'),
        '#options' => xml_form_builder_get_transforms(),
        '#default_value' => 'No Transform',
        '#description' => t("An XSL transform for setting the Fedora object's Dublin Core metadata datastream."),
      ];
      $form['fieldset']['self_transform'] = [
        '#type' => 'select',
        '#title' => t('Self XSL Transform'),
        '#options' => xml_form_builder_get_self_transforms(),
        '#default_value' => 'No Transform',
        '#description' => t('An optional transformation applied to form data prior to ingestion.'),
      ];
    }
    $form['fieldset']['file'] = [
      '#type' => 'file',
      '#title' => t('Upload Template Document'),
      '#description' => t('A sample metadata file used to prepopulate the form on ingest.'),
    ];
    $form['fieldset']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Add Association'),
    ];

    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $object_keys = [
      'content_model',
      'form_name',
      'dsid',
      'title_field',
      'transform',
      'self_transform',
    ];
    $object = array_intersect_key($form_state->getValues(), array_combine($object_keys, $object_keys));
    if (empty($object['title_field'])) {
      $object['title_field'] = NULL;
    }
    $object['template'] = '';
    $file_uploaded = $_FILES['files']['error']['file'] == 0;
    if ($file_uploaded) {
      $definition = new DOMDocument();
      $definition->load($_FILES['files']['tmp_name']['file']);
      $object['template'] = $definition->saveXML();
    }
    try {
      db_insert('xml_form_builder_form_associations')
        ->fields($object)
        ->execute();
      drupal_set_message(t('Successfully added association.'));
    }

      catch (Exception $e) {
      drupal_set_message(t('Failed to add association.'), 'error');
    }
  }

}
?>
