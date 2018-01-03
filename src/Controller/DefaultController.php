<?php

namespace Drupal\xml_forms\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Default controller for the xml_forms module.
 */
class DefaultController extends ControllerBase {

  /**
   * Serve the indicated schema file.
   */
  public function getSchema($filename) {
    $path = drupal_get_path('module', 'xml_forms') . '/schema';
    $full_filename = "$path/$filename";
    return BinaryFileResponse::create($full_filename);
  }

}
