<?php
namespace Drupal\xml_schema_api;

/**
 * A tree-like structure that models where elements can be insert into.
 */
class InsertOrderMap {

  /**
   * The schema.
   *
   * @var XMLSchema
   */
  protected $schema;

  /**
   * Create an InsertOrderMap.
   *
   * @param XMLSchema $schema
   *   The schema to use for this InsertOrderMap.
   */
  public function __construct(XMLSchema $schema) {
    $this->schema = $schema;
  }

  /**
   * Gets a tree of InsertOrderNodes defining how to arrange the DOMElements.
   *
   * @param string $path
   *   The XPath query representing the node to use for the InsertOrderMap.
   *
   * @return InsertOrderNode
   *   An InsertOrderNode generated from the node returned from $path.
   */
  public function getInsertOrderMap($path) {
    $element = new XMLSchemaElement($this->schema, $this->schema->getUniqueNode($path));
    if (isset($element->complexType)) {
      return $element->complexType->asInsertOrderNode();
    }
  }

  /**
   * Inserts a given child element into the right position of its parent.
   *
   * @param string $path
   *   XPath query to use when grabbing the InsertOrderMap for this operation.
   * @param DOMElement $parent
   *   The parent element to insert the child into.
   * @param DOMElement $new
   *   The child to insert into the parent element.
   */
  public function insert($path, DOMElement $parent, DOMElement $new) {
    $map = $this->getInsertOrderMap($path);
    $remaining_children = $this->populateInsertOrderMap($map, $this->getChildrenToReorder($parent, $new));
    $insert_order = $this->getChildrenInsertOrder($map, $remaining_children);
    $this->removeChildElements($parent);
    $this->appendChildElements($parent, $insert_order);
  }

  /**
   * Stores the child DOMElements' insert order.
   *
   * Any remaining DOMElements are returned.
   *
   * @param InsertOrderNode $map
   *   The insertOrderNode used to attempt to store the children using matched
   *   constraints.
   * @param DOMElement[] $children
   *   An array of DOMElements to attempt to store.
   *
   * @return DOMElement[]
   *   An array of child elements.
   */
  protected function populateInsertOrderMap(InsertOrderNode $map, array $children) {
    do {
      $matches = FALSE;
      foreach ($children as $key => $child) {
        if ($map->storeMatch($child)) {
          unset($children[$key]);
          $matches = TRUE;
        }
      }
    } while ($matches == TRUE);
    return $children;
  }

  /**
   * Gets a list of child DOMElements to reorder.
   *
   * This ... actually seems to also append a child DOMElement to the parent,
   * which ... huh? Maybe it should be named something different?
   *
   * @param DOMElement $parent
   *   The parent element under which get the children.
   * @param DOMElement $new
   *   The new child to add to the parent.
   *
   * @return DOMElement[]
   *   An array of child DOMElements, including the newly-appended one.
   */
  protected function getChildrenToReorder(DOMElement $parent, DOMElement $new) {
    // Existing child elements.
    $children = dom_node_children($parent, 'DOMElement');
    // Add the new child to the set of children.
    $children[] = $new;
    return $children;
  }

  /**
   * Get an array of child DOMElements in their correct insert order.
   *
   * @param InsertOrderNode $map
   *   The parent element under which to get the children.
   * @param array $remaining_children
   *   The remaining children to merge into the insert order.
   *
   * @return DOMElement[]
   *   An array of DOMElements in the correct insert order.
   */
  protected function getChildrenInsertOrder(InsertOrderNode $map, array $remaining_children) {
    // Now use the map to generate the new order for elements. This doesn't work
    // with mixed content!
    $insert_order = $map->getOrder();
    // Allows for out of order composition, when the final element is
    // added this block should not be entered.
    if (count($remaining_children) > 0) {
      $insert_order = array_merge($insert_order, $remaining_children);
    }
    return $insert_order;
  }

  /**
   * Remove all child elements from a parent element.
   *
   * @param DOMElement $parent
   *   The parent element to remove the child elements from.
   */
  protected function removeChildElements(DOMElement $parent) {
    // Child Elements.
    $children = dom_node_children($parent, 'DOMElement');
    foreach ($children as $child) {
      if (isset($child->parentNode)) {
        $child->parentNode->removeChild($child);
      }
    }
  }

  /**
   * Re-insert the child elements in the correct order.
   *
   * @param DOMElement $parent
   *   The parent element to re-insert the child elements into.
   * @param DOMElement[] $children
   *   An array of child elements to re-insert into the parent element.
   */
  protected function appendChildElements(DOMElement $parent, array $children) {
    foreach ($children as $child) {
      $parent->appendChild($child);
    }
  }

}
