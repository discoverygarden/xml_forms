<?php

/**
 * @file
 * Callbacks and functions for the Main Page.
 */

/**
 * Show the Main page.
 *
 * Here, the user can select which action they would like to do.
 *
 * @return array
 *   The table to display.
 */
function xml_form_builder_main() {

  module_load_include('inc', 'xml_form_builder', 'XMLFormRepository');
  $names = XMLFormRepository::GetNames();

  // No forms exist can only create.
  if (count($names) == 0) {
    return '<div>No forms are defined. Please create a new form.</div><br/>';
  }

  $table = array(
    'header' => array(
      array(
        'data' => t('Title'),
      ),
      array(
        'data' => t('Type'),
      ),
      array(
        'data' => t('Operations'),
        'colspan' => 6,
      ),
    ),
    'rows' => array(),
  );

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


    $table['rows'][] = array(
      $name,
      $type,
      $copy,
      $edit,
      $view,
      $export,
      $delete,
      $associate,
    );
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
