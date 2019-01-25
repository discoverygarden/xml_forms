<?php

/**
 * @file
 * Interface for supporting actions.
 */

use Drupal\objective_forms\FormElement;

/**
 * Interface that all actions support.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
interface ActionInterface {
// @codingStandardsIgnoreEnd

  /**
   * Constants.
   */
  const UNDEFINED = 0;
  // Create a DOMNode.
  const CREATE = 1;
  // Read from a DOMNode.
  const READ = 2;
  // Update a DOMNode.
  const UPDATE = 3;
  // Delete a DOMNode.
  const DELETE = 4;

  // @deprecated Constants
  // @codingStandardsIgnoreStart
  const Undefined = self::UNDEFINED;
  const Create = self::CREATE;
  const Read = self::READ;
  const Update = self::UPDATE;
  const Delete = self::DELETE;
  // @codingStandardsIgnoreEnd

  /**
   * Perform the designated action.
   *
   * @param XMLDocument $document
   *   Document to perform execution on.
   * @param FormElement $element
   *   Element in document to execute on.
   * @param mixed $value
   *   Value to execute.
   */
  public function execute(XMLDocument $document, FormElement $element, $value = NULL);

  /**
   * Check to see if this action should be performed.
   *
   * @param XMLDocument $document
   *   Document to check execution on.
   * @param FormElement $element
   *   Form element to check execution on.
   * @param mixed $value
   *   Value to check execution for.
   */
  public function shouldExecute(XMLDocument $document, FormElement $element, $value = NULL);

  /**
   * Converts this object to a Drupal Form Property value.
   *
   * @return mixed
   *   Returns an array representation of this class, which can be used within a
   *   Drupal Form Property '#actions'.
   */
  public function toDrupalForm();

}
