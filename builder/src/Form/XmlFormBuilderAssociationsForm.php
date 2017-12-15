<?php

namespace Drupal\xml_form_builder\Form;

use DOMDocument;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;

/**
 * Form association form.
 *
 * This form lists all the associations of the given XML form, allowing the
 * user to delete/disable those associations, as well as adding new
 * associations to the given XML form.
 */
class XmlFormBuilderAssociationsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_associations_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state, $form_name = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/associations.form');
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/associations');

    $associations = xml_form_builder_get_associations([$form_name], [], [], FALSE);
    $create_table_rows = function ($association) {
      if (is_array($association['title_field'])) {
        $association['title_field'] = "['" . implode("']['", $association['title_field']) . "']";
      }
      else {
        $association['title_field'] = $this->t('None');
      }
      $association['type'] = $association['in_db'] ? 'custom' : 'hook';
      return $association;
    };

    $associations = array_map($create_table_rows, $associations);
    $use_default_transforms = \Drupal::config('xml_form_builder.settings')->get('xml_form_builder_use_default_dc_xslts');

    $header = [
      $this->t('Content model'),
      $this->t('Type'),
      $this->t('Datastream ID'),
      $this->t('Label field'),
    ];
    if (!$use_default_transforms) {
      $header[] = $this->t('Transform');
      $header[] = $this->t('Self Transform');
    }
    $header[] = $this->t('Has template');
    $header[] = $this->t('Operations');

    $rows = [];
    foreach ($associations as $association) {
      $row = [
        $association['content_model'],
        ($association['type'] == 'hook') ? $this->t('Built-in') : $this->t('Custom'),
        $association['dsid'],
        $association['title_field'],
      ];

      if (!$use_default_transforms) {
        $row[] = $association['transform'];
        $row[] = (isset($association['self_transform'])) ? $association['self_transform'] : $this->t("No Self Transform");
      }

      $row[] = ($association['template']) ? $this->t('Yes') : $this->t('No');
      $operations = NULL;
      if ($association['type'] == 'hook') {
        if ($association['enabled']) {
          $operations = Link::createFromRoute($this->t("Disable"), 'xml_form_builder.disable_association', ['form_name' => $association['form_name'], 'id' => $association['id']]);
        }
        else {
          $operations = Link::createFromRoute($this->t("Enable"), 'xml_form_builder.enable_association', ['form_name' => $association['form_name'], 'id' => $association['id']]);
        }
      }
      else {
        $operations = Link::createFromRoute($this->t("Delete"), 'xml_form_builder.disable_association', ['form_name' => $association['form_name'], 'id' => $association['id']]);
      }
      $row[] = $operations;
      $rows[] = $row;
    }

    $form += [
      'list' => [
        '#type' => 'table',
        '#caption' => $this->t('Current Associations:'),
        '#header' => $header,
        '#rows' => $rows,
      ],
      'fieldset' => [
        '#type' => 'details',
        '#open' => TRUE,
        '#title' => $this->t('Add Association'),
        '#collapsible' => TRUE,
        'content_model' => [
          '#type' => 'textfield',
          '#title' => $this->t('Content Model'),
          '#required' => TRUE,
          '#autocomplete_route_name' => 'islandora.content_model_autocomplete',
          '#description' => $this->t('The content model to associate with a form. If the content model has no decendents it will not show up in autocomplete.'),
          '#default_value' => $form_state->getValue('content_model') ? $form_state->getValue('content_model') : NULL,
        ],
        'dsid' => [
          '#type' => 'textfield',
          '#description' => $this->t("The datastream ID where the object's metadata is stored."),
          '#title' => $this->t('Metadata Datastream ID'),
          '#required' => TRUE,
          '#default_value' => $form_state->getValues('dsid') ? $form_state->getValues('dsid') : NULL,
        ],
        'form_name' => [
          '#type' => 'value',
          '#title' => $this->t('Form Name'),
          '#value' => $form_name,
        ],
        'title_field' => [
          '#type' => 'select',
          '#title' => $this->t('Title Field'),
          '#description' => $this->t("The form field for the object's label."),
          '#prefix' => '<div id="ahah-wrapper">',
          '#suffix' => '</div>',
          '#options' => xml_form_builder_get_title_options($form_name),
        ],
      ],
    ];

    if (!$use_default_transforms) {
      $form['fieldset']['transform'] = [
        '#type' => 'select',
        '#title' => $this->t('XSL Transform'),
        '#options' => xml_form_builder_get_transforms(),
        '#default_value' => 'No Transform',
        '#description' => $this->t("An XSL transform for setting the Fedora object's Dublin Core metadata datastream."),
      ];
      $form['fieldset']['self_transform'] = [
        '#type' => 'select',
        '#title' => $this->t('Self XSL Transform'),
        '#options' => xml_form_builder_get_self_transforms(),
        '#default_value' => 'No Transform',
        '#description' => $this->t('An optional transformation applied to form data prior to ingestion.'),
      ];
    }
    $form['fieldset']['file'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload Template Document'),
      '#description' => $this->t('A sample metadata file used to prepopulate the form on ingest.'),
    ];
    $form['fieldset']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Association'),
    ];

    return $form;
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
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
      drupal_set_message($this->t('Successfully added association.'));
    }
    catch (Exception $e) {
      drupal_set_message($this->t('Failed to add association.'), 'error');
    }
  }

}
