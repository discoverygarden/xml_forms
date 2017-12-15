<?php

namespace Drupal\xml_forms\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the xml_forms module.
 */
class DefaultController extends ControllerBase {

  /**
   *
   */
  public function xml_forms_get_schema($filename) {
    $path = drupal_get_path('module', 'xml_forms') . '/schema';
    $full_filename = "$path/$filename";
    if (file_exists($full_filename)) {
      drupal_goto($full_filename);
    }
    else {
      drupal_not_found();
    }
  }

}
