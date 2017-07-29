<?php
namespace Drupal\xml_forms\Tests;

/**
 * Unit tests for Schema.inc
 *
 * @group xml_forms
 */
class SchemaAPISchemaTestCase extends \Drupal\simpletest\WebTestBase {

  protected $profile = 'standard';

  /**
   * Get test info.
   *
   * @return array
   *   Properties that are displayed in the test selection form.
   */
  public static function getInfo() {
    return [
      'name' => 'Schema Unit Tests.',
      'description' => 'Unit tests for Schema.inc',
      'group' => 'Islandora XML Forms Schema API',
    ];
  }

  /**
   * Performs any pre-requisite tasks that need to happen.
   */
  public function setUp() {
    parent::setUp('xml_schema_api');
    module_load_include('inc', 'xml_schema_api', 'Schema');
  }

  /**
   * The actual tests. Well, test. It's apparently just the one.
   */
  public function test() {
    // Basic Create.
    $path = drupal_get_path('module', 'xml_schema_api');
    $schema = new XMLSchema($path . '/tests/data/schemas/fgdc-std-001-1998.xsd');
    $this->assertNotNull($schema);
  }

}
