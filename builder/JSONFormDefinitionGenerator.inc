<?php

/**
 * @file
 * Provides a class that generates the required JSON for Form Builder GUI.
 */

use Drupal\Core\Render\Element;

module_load_include('inc', 'php_lib', 'String');

/**
 * Generates two JSON strings that can be used by the Form Builder GUI.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormBuilder.info" instead of just "XMLFormBuilder"... ideally, this
 * should move somewhere under the Drupal\xml_form_builder namespace at
 * somepoint... because of this, we've left it with only the original
 * "XMLForm" prefix, to avoid having to carry through the change.
 */
class JSONFormDefinitionGenerator {
// @codingStandardsIgnoreEnd

  /**
   * Creates an array that contains two JSON strings.
   *
   * These define the form properties and the form.
   *
   * @param array $properties
   *   The form properties.
   * @param array $form
   *   A Drupal form.
   *
   * @return array
   *   An array where the first value is a JSON string that describes the form
   *   properties and the second value is a JSON string that describes the
   *   Drupal form, e.g. array($properties, $form).
   */
  public static function create(array &$properties, array &$form) {
    $properties = static::getProperties($properties);
    $form = static::getForm($form);
    return [$properties, $form];
  }

  /**
   * Gets the Form Properties as a JSON string.
   *
   * @param array $properties
   *   The Form Properties.
   *
   * @return string
   *   The JSON representation of the Form Properties.
   */
  public static function getProperties(array $properties) {
    return json_encode(static::getPropertiesArray($properties));
  }

  /**
   * Gets the Form Properties as an array.
   */
  public static function getPropertiesArray(array $properties) {
    $namespaces = isset($properties['namespaces']) ? $properties['namespaces'] : [];
    $properties = [
      'localName' => $properties['root_name'],
      'uri' => isset($properties['default_uri']) ? $properties['default_uri'] : NULL,
      'namespaces' => $namespaces,
      'schema' => isset($properties['schema_uri']) ? $properties['schema_uri'] : NULL,
    ];
    return [$properties];
  }

  /**
   * Gets the Drupal Form as a JSON string.
   *
   * @param array $form
   *   The Drupal Form.
   *
   * @return string
   *   The JSON representation of the Drupal Form.
   */
  public static function getForm(array $form) {
    return json_encode(static::getFormArray($form));
  }

  /**
   * Gets the Drupal Form as an array.
   */
  public static function getFormArray(array $form) {
    $elements = [
      'key' => 'Root',
      'text' => 'Elements',
      'children' => [],
    ];
    $elements = static::getElement($form);
    $elements['key'] = empty($elements['key']) ? 'Root' : $elements['key'];
    $elements['text'] = "{$elements['key']} ({$elements['type']})";
    return ['expanded' => TRUE, 'children' => $elements];
  }

  /**
   * Gets a Drupal Form Element as a JSON string.
   *
   * @param array $element
   *   A Drupal Form Element.
   * @param string $identifier
   *   An optional identifier for the element.
   *
   * @return array
   *   An array that represents a ExtJS JSON object that represents the Drupal
   *   Form Element.
   */
  protected static function getElement(array $element, $identifier = NULL) {
    $json = [];
    foreach (Element::properties($element) as $key) {
      // Remove the #.
      $name = trim($key, '#');
      $json[$name] = $element[$key];
    }
    $json['children'] = [];
    foreach (Element::children($element) as $key) {
      $json['children'][] = static::getElement($element[$key], $key);
    }
    // These two are ExtJS specific.
    $json['key'] = $identifier;
    $json['text'] = "{$identifier} ({$element['#type']})";
    return $json;
  }

}
