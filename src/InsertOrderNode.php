<?php
namespace Drupal\xml_forms;

/**
 * Models Insert order constraints.
 */
abstract class InsertOrderNode {

  /**
   * The parent of this InsertOrderNode.
   *
   * @var InsertOrderNode
   */
  public $parent;

  /**
   * The minimum bound as an integer, or the string 'unbounded' if none.
   *
   * @var mixed
   */
  public $min;

  /**
   * The maximum bound as an integer, or the string 'unbounded' if none.
   *
   * @var mixed
   */
  public $max;

  /**
   * Create an InsertOrderNode.
   *
   * @param mixed $min
   *   The minimum bound as an integer, or the string 'unbounded' if none.
   * @param mixed $max
   *   The maximum bound as an integer, or the string 'unbounded' if none.
   * @param InsertOrderNode $parent
   *   The parent InsertOrderNode, if it exists.
   */
  public function __construct($min, $max, InsertOrderNode $parent = NULL) {
    $this->min = $min;
    $this->max = $max;
    $this->parent = $parent;
  }

  /**
   * Attempts to store the given DOMElement in a matched constraint.
   *
   * @param DOMElement $element
   *   The DOMElement to store.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  abstract public function storeMatch(DOMElement $element);

  /**
   * Gets an array of DOMElements in their correct insert order.
   *
   * @return DOMElement[]
   *   The array of DOMElements in the correct order.
   */
  abstract public function getOrder();

  /**
   * Checks if this node or a child node meets this constraint.
   *
   * Does not account for occurrence constraints.
   *
   * @param DOMElement $element
   *   The DOMElement to check.
   *
   * @return bool
   *   TRUE if it meets the constraints, FALSE otherwise.
   */
  abstract public function constraintMet(DOMElement $element);

  /**
   * Checks if this constraint matched its max number of occurrences.
   *
   * @return bool
   *   TRUE if it has, FALSE otherwise.
   */
  abstract public function maximumConstraintsMet();

  // @TODO: create two more abstract public functions - matchesConstraint() and
  // matches(), each with an argument of the DOMElement $element.
}
