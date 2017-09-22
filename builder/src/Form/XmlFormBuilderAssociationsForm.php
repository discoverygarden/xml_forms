<?php

namespace Drupal\xml_form_builder\Form;

use DOMDocument;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

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

  public function buildForm(array $form, FormStateInterface $form_state, $form_name = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/associations.form');
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/associations');

    $associations = xml_form_builder_get_associations([$form_name], [], [], FALSE);
    $create_table_rows = function($association) {
      if (is_array($association['title_field'])) {
        $association['title_field'] = "['" . implode("']['", $association['title_field']) . "']";
      }
      else {
        $association['title_field'] = $this->t('None');
      }
      $association['type'] = $association['in_db'] ? 'custom' : 'hook';
      return $association;
    };
/**


<div id="content-model-form-main">
  <div id="content-model-form-table">
    <table>
      <tr>
        <th><?php print t('Content model'); ?></th>
        <th><?php print t('Type'); ?></th>
        <th><?php print t('Datastream ID'); ?></th>
        <th><?php print t('Label field'); ?></th>

        <?php if (!$use_default_transforms): ?>
          <th><?php print t('Transform'); ?></th>
          <th><?php print t('Self Transform'); ?></th>
        <?php endif; ?>

        <th><?php print t('Has template'); ?></th>
        <th><?php print t('Operations'); ?></th>
      </tr>
      <?php foreach ($associations as $association) : ?>
        <tr>
          <td><?php print $association['content_model'] ?></td>
          <td><?php print ($association['type'] == 'hook') ? t('Built-in') : t('Custom') ?></td>
          <td><?php print $association['dsid'] ?></td>
          <td><?php print $association['title_field'] ?></td>

          <?php if (!$use_default_transforms): ?>
            <td><?php print $association['transform'] ?></td>
            <td><?php print (isset($association['self_transform'])) ? $association['self_transform'] : t("No Self Transform") ?></td>
          <?php endif; ?>

          <td><?php print ($association['template']) ? t('Yes') : t('No') ?></td>
          <td>
          <?php if ($association['type'] == 'hook'): ?>
          <?php if ($association['enabled']): ?>
          <?php // @FIXME
// l() expects a Url object, created from a route name or external URI.
// print l(t("Disable"), "admin/islandora/xmlform/forms/{$association['form_name']}/disassociate/{$association['id']}")
 ?>
          <?php else: ?>
          <?php // @FIXME
// l() expects a Url object, created from a route name or external URI.
// print l(t("Enable"), "admin/islandora/xmlform/forms/{$association['form_name']}/associate/{$association['id']}")
 ?>
          <?php endif; ?>
          <?php else: ?>
          <?php // @FIXME
// l() expects a Url object, created from a route name or external URI.
// print l(t("Delete"), "admin/islandora/xmlform/forms/{$association['form_name']}/disassociate/{$association['id']}")
 ?>
          <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>


*/
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
    //       '#type' => 'details',
    //       '#title' => $this->t('Current associations'),
    //       '#value' => theme('xml_form_builder_association_table', array(
    //         'associations' => array_map($create_table_rows, $associations),
    //         'use_default_transforms' => \Drupal::config('xml_form_builder.settings')->get('xml_form_builder_use_default_dc_xslts'),
    //       )),
    //     ),
    //     'fieldset' => array(
    //       '#type' => 'details',
    //       '#title' => $this->t('Add Association'),
    //       '#collapsible' => TRUE,
    //       'content_model' => array(
    //         '#type' => 'textfield',
    //         '#title' => $this->t('Content Model'),
    //         '#required' => TRUE,
    //         '#autocomplete_path' => ISLANDORA_CONTENT_MODELS_AUTOCOMPLETE,
    //         '#description' => $this->t('The content model to associate with a form. If the content model has no decendents it will not show up in autocomplete.'),
    //         '#default_value' => isset($form_state['values']['content_model']) ? $form_state['values']['content_model'] : NULL,
    //       ),
    //       'dsid' => array(
    //         '#type' => 'textfield',
    //         '#description' => $this->t("The datastream ID where the object's metadata is stored."),
    //         '#title' => $this->t('Metadata Datastream ID'),
    //         '#required' => TRUE,
    //         '#default_value' => isset($form_state['values']['dsid']) ? $form_state['values']['dsid'] : NULL,
    //       ),
    //       'form_name' => array(
    //         '#type' => 'value',
    //         '#title' => $this->t('Form Name'),
    //         '#value' => $form_name,
    //       ),
    //       'title_field' => array(
    //         '#type' => 'select',
    //         '#title' => $this->t('Title Field'),
    //         '#description' => $this->t("The form field for the object's label."),
    //         '#prefix' => '<div id="ahah-wrapper">',
    //         '#suffix' => '</div>',
    //         '#options' => xml_form_builder_get_title_options($form_name),
    //       ),
    //     ),
    //   );

    if (!\Drupal::config('xml_form_builder.settings')->get('xml_form_builder_use_default_dc_xslts')) {
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
