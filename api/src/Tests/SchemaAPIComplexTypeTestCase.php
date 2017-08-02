<?php
namespace Drupal\xml_form_api\Tests;

/**
 * Unit tests for ComplexType.inc
 *
 * @group xml_form_api
 */
class SchemaAPIComplexTypeTestCase extends \Drupal\simpletest\WebTestBase {

  protected $profile = 'standard';

  /**
   * Get test info.
   *
   * @return array
   *   Properties that are displayed in the test selection form.
   */
  public static function getInfo() {
    return [
      'name' => 'ComplexType Unit Tests.',
      'description' => 'Unit tests for ComplexType.inc',
      'group' => 'Islandora XML Forms Schema API',
    ];
  }

  /**
   * Performs any prerequisite tasks that need to happen.
   */
  public function setUp() {
    // Enable any modules required for the test.
    parent::setUp('xml_schema_api');
    module_load_include('inc', 'xml_schema_api', 'Schema');
    module_load_include('inc', 'xml_schema_api', 'Element');
  }

  /**
   * The actual tests.
   */
  public function test() {
    $path = drupal_get_path('module', 'xml_schema_api');
    $schema = new XMLSchema($path . '/tests/data/schemas/fgdc-std-001-1998.xsd');
    $this->assertNotNull($schema);
    // Path to the idinfo element.
    $path = '/xsd:schema/xsd:element[@name=\'idinfo\']';
    $node = $schema->getUniqueNode($path);
    $this->assertNotNull($node);
    $element = new XMLSchemaElement($schema, $node);
    $this->assertTrue(is_object($element));
    // Element has a complex type.
    $this->assertTrue(is_object($element->complexType));
    $complex_type = $element->complexType;
    $this->assertTrue(is_object($complex_type->sequence));
  }

}
