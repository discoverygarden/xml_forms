<?php

/**
 * @file
 * Models an <xs:sequence> element.
 */

module_load_include('inc', 'php_lib', 'DOMHelpers');
module_load_include('inc', 'xml_schema_api', 'Node');
module_load_include('inc', 'xml_schema_api', 'Element');

/**
 * Models an <xs:sequence> element.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLSchemaApi.info" instead of just "XMLSchemaApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint...
 */
abstract class XMLSchemaApiAbstractSchemaNode extends XMLSchemaNode {
// @codingStandardsIgnoreEnd

  /**
   * Constructor function for the XMLSchemaSequence class.
   *
   * @param XMLSchema $schema
   *   The schema to use for the DOMElement.
   * @param DOMElement $node
   *   The node to use for this instance.
   */
  public function __construct(XMLSchema $schema, DOMElement $node) {
    parent::__construct($schema, $node);
    $this->protected->addMembers([
      'element' => $this->createChildren(XMLSchemaElement::LOCAL_NAME),
      'choice' => $this->createChildren(XMLSchemaChoice::LOCAL_NAME),
      'sequence' => $this->createChildren(XMLSchemaSequence::LOCAL_NAME),
      'group' => $this->createChildren(XMLSchemaGroup::LOCAL_NAME),
      // All children in the correct order.
      'children' => $this->createChildren(),
    ]);
  }

  /**
   * Returns the local name of this object.
   *
   * @return string
   *   The local name of this object.
   */
  protected function getLocalName() {
    return static::LOCAL_NAME;
  }

  /**
   * Get the name of the class used to map.
   *
   * @return string
   *   The name of the class.
   */
  abstract protected function getClassName();

  /**
   * Adds children using the specified parent InsertOrderNode.
   *
   * @param InsertOrderNode $parent
   *   The parent, with an insert order applied, as an InsertOrderNode.
   *
   * @return InsertOrderSequence
   *   The mapped node, with children appended.
   */
  public function asInsertOrderNode(InsertOrderNode $parent = NULL) {
    $class_name = $this->getClassName();

    list($min, $max) = $this->getMinMaxOccurs();
    $map_node = new $class_name($min, $max, $parent);
    foreach ($this->children as $child) {
      $map_node->addChild($child->asInsertOrderNode($map_node));
    }
    return $map_node;
  }

}
