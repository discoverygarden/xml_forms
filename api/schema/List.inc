<?php

/**
 * @file
 * Models an <xs:list> element.
 */

module_load_include('inc', 'xml_schema_api', 'Node');

/**
 * Models an <xs:list> DOMElement.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLSchemaApi.info" instead of just "XMLSchemaApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XMLSchemaList extends XMLSchemaNode {
// @codingStandardsIgnoreEnd
  /**
   * Constants.
   */
  const LOCAL_NAME = 'list';

  // @deprecated Constants
  // @codingStandardsIgnoreStart
  const LocalName = self::LOCAL_NAME;
  // @codingStandardsIgnoreEnd

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
   * Doesn't do anything, as <xs:list> shouldn't return an InsertOrderNode.
   *
   * @param InsertOrderNode $parent
   *   The parent, with an insert order applied, as an InsertOrderNode.
   *
   * @return null
   *   What it says on the tin.
   */
  public function asInsertOrderNode(InsertOrderNode $parent = NULL) {
    return NULL;
  }

}
