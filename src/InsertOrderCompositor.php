<?php
namespace Drupal\xml_forms;

/**
 * Models a generic compositor constraint (either All, Sequence or Choice).
 */
abstract class InsertOrderCompositor extends InsertOrderNode {

  /**
   * Child Insert Order Constraints. Compositors can repeat.
   *
   * @var InsertOrderNode[][]
   */
  public $children;

  /**
   * Create an InsertOrderCompositor.
   *
   * @param mixed $min
   *   The minimum bound as an integer, or the string 'unbounded' if none.
   * @param mixed $max
   *   The maximum bound as an integer, or the string 'unbounded' if none.
   * @param InsertOrderCompositor $parent
   *   The parent InsertOrderCompositor, if it exists.
   */
  public function __construct($min, $max, InsertOrderCompositor $parent = NULL) {
    parent::__construct($min, $max, $parent);
    $this->children = array();
    $this->children[0] = array();
  }

  /**
   * If a parent InsertOrderCompositor gets repeated, this will be called.
   */
  public function __clone() {
    $children = $this->children[0];
    $this->children = array();
    $this->children[0] = array();
    foreach ($children as $child) {
      $this->addChild(clone $child);
    }
  }

  /**
   * Add a child to the Compositor's definition.
   *
   * @param InsertOrderNode $child
   *   The child to add to the Compositor's definition.
   */
  public function addChild(InsertOrderNode $child) {
    $this->children[0][] = $child;
  }

  /**
   * Create new occurrence of this compositor.
   *
   * @return bool
   *   TRUE if the occurrence is created, FALSE otherwise.
   */
  public function createOccurrence() {
    if (!$this->maximumConstraintsMet()) {
      $new = clone $this->children[0];
      $this->children[] = $new;
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Determines if this has matched its max number of constraints.
   *
   * @return bool
   *   TRUE if it has, FALSE otherwise.
   */
  public function maximumConstraintsMet() {
    $count = count($this->children);
    return is_numeric($this->max) ? $count == $this->max : FALSE;
  }

  /**
   * Attempts to store the given DOMElement in a matched constraint.
   *
   * @param DOMElement $element
   *   The DOMElement to store.
   *
   * @return bool
   *   TRUE if the attempt was successful; FALSE otherwise.
   */
  public function storeMatch(DOMElement $element) {
    if ($this->constraintMet($element)) {
      // A child constraint matches this element.
      if ($this->attemptStore($element)) {
        return TRUE;
      }
      else {
        if ($this->createOccurrence()) {
          // Attempt to store.
          return $this->attemptStore($element);
        }
      }
    }
    return FALSE;
  }

  /**
   * A helper function used by InsertOrderCompositor::storeMatch().
   *
   * Attempts to store the element.
   *
   * @param DOMElement $element
   *   The element InsertOrderCompositor::storeMatch() should attempt to store.
   *
   * @return bool
   *   TRUE if the attempt was successful; FALSE otherwise.
   */
  protected function attemptStore(DOMElement $element) {
    $count = count($this->children);
    for ($i = 0; $i < $count; $i++) {
      foreach ($this->children[$i] as $child) {
        if ($child->storeMatch($element)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Gets an array of DOMElements in their correct insert order.
   *
   * @return DOMElement[]
   *   An array of DOMElements in their correct insert order.
   */
  public function getOrder() {
    $order = array();
    $count = count($this->children);
    for ($i = 0; $i < $count; $i++) {
      foreach ($this->children[$i] as $child) {
        $order = array_merge($order, $child->getOrder());
      }
    }
    return $order;
  }

  /**
   * Checks if this node or a child node meets this constraint.
   *
   * Does not account for occurrence constraints.
   *
   * @param DOMElement $element
   *   The element to check against.
   *
   * @return bool
   *   TRUE if the constraint is met; FALSE otherwise.
   */
  public function constraintMet(DOMElement $element) {
    foreach ($this->children[0] as $child) {
      if ($child->constraintMet($element)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
