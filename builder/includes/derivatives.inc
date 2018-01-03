<?php

/**
 * @file
 * Callbacks used in derivatives hook.
 */

use Drupal\Core\Logger\RfcLogLevel;

/**
 * Derives the DC datastream using a default XSLT.
 *
 * Since DC is always created we are effectively always forced.
 */
function xml_form_builder_derive_dc($object, $force = FALSE, $hook = []) {
  module_load_include('inc', 'xml_form_builder', 'includes/db');
  if (!isset($object[$hook['source_dsid']])) {
    return [
      'success' => FALSE,
      'messages' => [
        [
          'message' => t('DC creation failed: source DSID missing.'),
          'type' => 'watchdog',
          'severity' => RfcLogLevel::ERROR,
        ],
      ],
    ];
  }
  try {
    $model = array_diff($object->models, ['fedora-system:FedoraObject-3.0']);
    $xslt = xml_form_builder_get_default_transform(reset($model), $hook['source_dsid']);
    $xslt_dom = new DOMDocument();
    $xslt_dom->loadXML($xslt);

    $source_dom = new DOMDocument();
    $source_dom->loadXML($object[$hook['source_dsid']]->content);

    xml_form_builder_update_dc_datastream($object, $xslt_dom, $source_dom);
  }
  catch (Exception $e) {
    return [
      'success' => FALSE,
      'messages' => [
        [
          'message' => t('DC creation failed.<br/> With stack: @trace'),
          'message_sub' => ['@trace' => $e->getTraceAsString()],
          'type' => 'watchdog',
          'severity' => RfcLogLevel::ERROR,
        ],
      ],
    ];
  }

  return [
    'success' => TRUE,
    'messages' => [
      [
        'message' => t('Created DC derivative for (@pid).'),
        'message_sub' => ['@pid' => $object->id],
        'type' => 'dsm',
        'severity' => 'status',
      ],
    ],
  ];
}
