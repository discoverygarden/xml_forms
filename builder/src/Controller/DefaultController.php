<?php /**
 * @file
 * Contains \Drupal\xml_form_builder\Controller\DefaultController.
 */

namespace Drupal\xml_form_builder\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the xml_form_builder module.
 */
class DefaultController extends ControllerBase {

  public function xml_form_builder_main() {

    module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
    $names = XMLFormRepository::GetNames();

    // No forms exist can only create.
    if (count($names) == 0) {
      return '<div>No forms are defined. Please create a new form.</div><br/>';
    }

    $table = [
      'header' => [
        ['data' => t('Title')],
        ['data' => t('Type')],
        [
          'data' => t('Operations'),
          'colspan' => 6,
        ],
      ],
      'rows' => [],
    ];

    foreach ($names as $form_info) {
      $name = $form_info['name'];
      if ($form_info['indb']) {
        $type = t('Custom');
        // @FIXME
        // l() expects a Url object, created from a route name or external URI.
        // $edit = l(t('Edit'), xml_form_builder_get_edit_form_path($name));

        // @FIXME
        // l() expects a Url object, created from a route name or external URI.
        // $delete = l(t('Delete'), xml_form_builder_get_delete_form_path($name));

      }
      else {
        $type = t('Built-in');
        $edit = '';
        $delete = '';
      }
      // @FIXME
      // l() expects a Url object, created from a route name or external URI.
      // $copy = l(t('Copy'), xml_form_builder_get_copy_form_path($name));

      // @FIXME
      // l() expects a Url object, created from a route name or external URI.
      // $view = l(t('View'), xml_form_builder_get_view_form_path($name));

      // @FIXME
      // l() expects a Url object, created from a route name or external URI.
      // $export = l(t('Export'), xml_form_builder_get_export_form_path($name));

      // @FIXME
      // l() expects a Url object, created from a route name or external URI.
      // $associate = l(t('Associate'), xml_form_builder_get_associate_form_path($name));


      $table['rows'][] = [
        $name,
        $type,
        $copy,
        $edit,
        $view,
        $export,
        $delete,
        $associate,
      ];
    }

    // @FIXME
    // theme() has been renamed to _theme() and should NEVER be called directly.
    // Calling _theme() directly can alter the expected output and potentially
    // introduce security issues (see https://www.drupal.org/node/2195739). You
    // should use renderable arrays instead.
    // 
    // 
    // @see https://www.drupal.org/node/2195739
    // return theme('table', $table);

  }

  public function xml_form_builder_list_associations() {

    module_load_include('inc', 'xml_form_builder', 'includes/associations');

    $associations_list = [
      '#theme' => 'item_list',
      '#items' => [],
    ];

    $associations = xml_form_builder_get_associations([], [], [], TRUE);
    $map = [];

    foreach ($associations as $association) {
      $cmodel = $association['content_model'];
      $form = $association['form_name'];
      if (!isset($map[$cmodel])) {
        $map[$cmodel] = [];
      }
      $map[$cmodel][] = $form;
    }
    ksort($map);

    /**
     * Returns a link to the edit associations form for form $form_name.
     */
    function create_form_association_link($form_name) {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// return array(l($form_name, xml_form_builder_get_associate_form_path($form_name)));

    }

    foreach ($map as $cmodel => $forms) {
      $form_table = [
        '#theme' => 'table',
        '#rows' => array_map('create_form_association_link', $forms),
      ];
      $object = islandora_object_load($cmodel);
      if ($object) {
        $label = $object->label . " ($cmodel)";
      }
      else {
        $label = $cmodel;
      }
      $associations_list['#items'][] = $label . \Drupal::service("renderer")->render($form_table);
    }

    return [$associations_list];
  }

  public function xml_form_builder_export($form_name) {
    module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
    header('Content-Type: text/xml', TRUE);
    header('Content-Disposition: attachment; filename="' . $form_name . '.xml"');
    $definition = XMLFormRepository::Get($form_name);
    $definition->formatOutput = TRUE;
    echo $definition->saveXML();
    exit();
  }

  public function xml_form_builder_edit($form_name) {
    module_load_include('inc', 'xml_form_builder', 'XMLFormDatabase');

    if (!XMLFormDatabase::Exists($form_name)) {
      drupal_set_message(t('Form "%name" does not exist.', [
        '%name' => $form_name
        ]), 'error');
      drupal_not_found();
      exit();
    }

    xml_form_builder_edit_include_css();
    xml_form_builder_edit_include_js();
    xml_form_builder_create_element_type_store();
    xml_form_builder_create_element_store($form_name);
    xml_form_builder_create_properties_store($form_name);
    return '<div id="xml-form-builder-editor"></div>';
  }

  public function xml_form_builder_edit_save($form_name) {
    module_load_include('inc', 'xml_form_builder', 'JSONFormDefinition');
    module_load_include('inc', 'xml_form_builder', 'XMLFormDatabase');
    module_load_include('inc', 'xml_form_api', 'XMLFormDefinition');
    try {
      // @TODO: this data needs to be sanitized. Can we get this data through the
    // form API?
      $definition = new JSONFormDefinition($_POST['data']);
      list($properties, $form) = $definition->getPropertiesAndForm();
      $definition = XMLFormDefinitionGenerator::Create($properties, $form);
      XMLFormDatabase::Update($form_name, $definition);
    }
    
      catch (Exception $e) {
      $msg = "File: {$e->getFile()}<br/>Line: {$e->getLine()}<br/>Error: {$e->getMessage()}";
      drupal_set_message(\Drupal\Component\Utility\Xss::filter($msg), 'error');
    }
  }

  public function xml_form_builder_disable_association($form_name, $id) {
    module_load_include('inc', 'xml_form_builder', 'includes/associations');
    $association = xml_form_builder_get_association($id);
    if (!isset($association)) {
      drupal_set_message(t('Specified association does not exist.'), 'error');
      drupal_goto(xml_form_builder_get_associate_form_path($form_name));
      return;
    }
    // Database defined association.
    if ($association['in_db']) {
      db_delete('xml_form_builder_form_associations')
        ->condition('id', intval($id))
        ->execute();
      drupal_set_message(t('Deleted the association ID:%id from %form_name.', [
        '%id' => $id,
        '%form_name' => $form_name,
      ]));
    }
    else {
      // Hook defined association.
      $num_results = db_select('xml_form_builder_association_hooks', 'fa')
        ->fields('fa')
        ->condition('id', $id)
        ->countQuery()
        ->execute()
        ->fetchField();
      if ($num_results == 1) {
        db_update('xml_form_builder_association_hooks')
          ->fields(['enabled' => (int) FALSE])
          ->condition('id', $id)
          ->execute();
      }
      else {
        db_insert('xml_form_builder_association_hooks')
          ->fields([
          'id' => $id,
          'enabled' => (int) FALSE,
        ])
          ->execute();
      }
      drupal_set_message(t('Successfully disabled association.'));
    }
    drupal_goto(xml_form_builder_get_associate_form_path($form_name));
  }

  public function xml_form_builder_enable_association($form_name, $id) {
    module_load_include('inc', 'xml_form_builder', 'includes/associations');
    $association = xml_form_builder_get_association($id);
    if (!isset($association)) {
      drupal_set_message(t('Specified association does not exist.'), 'error');
      drupal_goto(xml_form_builder_get_associate_form_path($form_name));
      return;
    }
    // Hook defined association, can't enable non hook associations.
    if (!$association['in_db']) {
      $num_results = db_select('xml_form_builder_association_hooks', 'fa')
        ->fields('fa')
        ->condition('id', $id)
        ->countQuery()
        ->execute()
        ->fetchField();
      if ($num_results == 1) {
        db_update('xml_form_builder_association_hooks')
          ->fields(['enabled' => (int) TRUE])
          ->condition('id', $id)
          ->execute();
      }
      else {
        db_insert('xml_form_builder_association_hooks')
          ->fields([
          'id' => $id,
          'enabled' => (int) TRUE,
        ])
          ->execute();
      }
    }
    drupal_set_message(t('Successfully enabled association.'));
    drupal_goto(xml_form_builder_get_associate_form_path($form_name));
  }

}
