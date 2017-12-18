<?php

/**
 * @file
 * Models an <xs:union> element.
 */

module_load_include('inc', 'xml_schema_api', 'Node');

/**
 * Models an <xs:union> DOMElement.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLSchemaApi.info" instead of just "XMLSchemaApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XMLSchemaUnion extends XMLSchemaNode {
// @codingStandardsIgnoreEnd

  /**
   * Constants.
   */
  const LOCAL_NAME = 'union';

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
    return self::LOCAL_NAME;
  }

  /**
   * Included because it's in the abstraction, but (currently?) unused.
   *
   * @param InsertOrderNode $parent
   *   Mainly there because it's in the abstraction. Won't do a thing.
   *
   * @return null
   *   Always null.
   */
  public function asInsertOrderNode(InsertOrderNode $parent = NULL) {
    return NULL;
  }

}
