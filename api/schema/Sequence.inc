<?php

/**
 * @file
 * Models an <xs:sequence> element.
 */

module_load_include('inc', 'xml_schema_api', 'AbstractNode');

/**
 * Models an <xs:sequence> element.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLSchemaApi.info" instead of just "XMLSchemaApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XMLSchemaSequence extends XMLSchemaApiAbstractSchemaNode {
// @codingStandardsIgnoreEnd

  /**
   * Constants.
   */
  const LOCAL_NAME = 'sequence';

  // @deprecated Constants
  // @codingStandardsIgnoreStart
  const LocalName = self::LOCAL_NAME;
  // @codingStandardsIgnoreEnd

  /**
   * {@inheritdoc}
   */
  protected function getClassName() {
    return 'InsertOrderSequence';
  }

}
