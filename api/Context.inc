<?php

/**
 * @file
 * Defines several Context related classes.
 *
 * Defines the three types of XPath context that can be used when defining forms
 * (document, parent, self). Also it defines a Class for determining which
 * DOMNode a given context refers to.
 */

use Drupal\php_lib\Enum;
use Drupal\objective_forms\FormElement;

/**
 * Enumerated types for Context.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class ContextType extends Enum {
// @codingStandardsIgnoreEnd

  const __DEFAULT = 'document';
  const DOCUMENT = 'document';
  const PARENT = 'parent';
  const SELF = 'self';

  // @deprecated Constants
  // @codingStandardsIgnoreStart
  const __default = self::__DEFAULT;
  const Document = self::DOCUMENT;
  const Parent = self::PARENT;
  const Self = self::SELF;
  // @codingStandardsIgnoreEnd

}

/**
 * Represent the contenxt and facilitate evaluation of the referred node.
 *
 * Stores the type of context a Path uses, and retrieve the DOMNode a context
 * refers to.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class Context {
// @codingStandardsIgnoreEnd

  /**
   * The type of context this class represents.
   *
   * @var ContextType
   */
  protected $type;

  /**
   * Create XPath Context Object.
   *
   * @param ContextType $type
   *   The context for the XPath object as defined by ContextType.
   */
  public function __construct(ContextType $type) {
    $this->type = $type;
  }

  /**
   * Checks if the DOMNode for this context exists for the given FormElement.
   *
   * @param XMLDocument $document
   *   Document to check.
   * @param \Drupal\objective_forms\FormElement $element
   *   Element to check.
   *
   * @return bool
   *   TRUE/FALSE based on its existence.
   */
  public function exists(XMLDocument $document, FormElement $element) {
    try {
      $this->getNode($document->registry, $element);
      return TRUE;
    }
    catch (XMLFormsContextException $e) {
      return FALSE;
    }
  }

  /**
   * Gets the node defined by this context for the given form element.
   *
   * @param XMLDocument $document
   *   The document to get the node for.
   * @param \Drupal\objective_forms\FormElement $element
   *   The node to grab.
   *
   * @return DOMNode
   *   If the context node is found it is returned, NULL otherwise.
   */
  public function getNode(XMLDocument $document, FormElement $element) {
    switch ($this->type->val) {
      case ContextType::DOCUMENT:
        return NULL;

      case ContextType::PARENT:
        return $this->getParent($document, $element);

      case ContextType::SELF:
        return $this->getSelf($document, $element);
    }
  }

  /**
   * Gets the parent context node of the provided FormElement.
   *
   * @param XMLDocument $document
   *   The document to get the element from.
   * @param \Drupal\objective_forms\FormElement $element
   *   The element to get parent context from.
   *
   * @throws XMLFormsContextNotFoundException
   *   Does so if no parent form element context is found.
   * @throws XMLFormsContextDefinitionException
   *   Does so if no parent form element defines a create or read action.
   *
   * @return DOMNode
   *   If found the parent node is returned, otherwise NULL.
   */
  protected function getParent(XMLDocument $document, FormElement $element) {
    $parent = $element->parent;
    while ($parent) {
      $selected_node = isset($parent->actions->read);
      $created_node = isset($parent->actions->create);
      if ($selected_node || $created_node) {
        // We've found a parent that should have a node registered.
        if ($document->registry->isRegistered($parent->hash)) {
          return $document->registry->get($parent->hash);
        }
        throw new XMLFormsContextNotFoundException($this->type, $element);
      }
      // Check next Parent.
      $parent = $parent->parent;
    }
    throw new XMLFormsContextDefinitionException($this->type, $element);
  }

  /**
   * Gets the self context node of the provided Form Element.
   *
   * @param XMLDocument $document
   *   The document to grab self context from.
   * @param \Drupal\objective_forms\FormElement $element
   *   The element to grab self context from within that document.
   *
   * @throws XMLFormsContextNotFoundException
   *   Does so if no context is found for the element.
   *
   * @return DOMNode
   *   The DOMNode for the provided Form Element's 'self' context.
   */
  protected function getSelf(XMLDocument $document, FormElement $element) {
    if ($document->registry->isRegistered($element->hash)) {
      return $document->registry->get($element->hash);
    }
    throw new XMLFormsContextNotFoundException($this->type, $element);
  }

  /**
   * Returns a string describing this context.
   *
   * @return string
   *   String describing the context.
   */
  public function __toString() {
    return (string) $this->type;
  }

}

/**
 * Represents exceptions that can occur when looking for the context DOMNode.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XMLFormsContextException extends Exception {
// @codingStandardsIgnoreEnd

  /**
   * Constructor function for the XMLFormsContextException class.
   *
   * @param ContextType $type
   *   The context type to build an exception for.
   * @param \Drupal\objective_forms\FormElement $element
   *   The form element being referred to when the exception is thrown.
   * @param string $message
   *   The error message to throw.
   */
  public function __construct(ContextType $type, FormElement $element, $message) {
    $variable_description = "The Form Element<br/>";
    $variable_description .= "&nbsp;Location: '{$element->getLocation()}'<br/>";
    $variable_description .= "&nbsp;Title: '{$element['#title']}'</br>";
    $variable_description .= "&nbsp;Type: '{$element['#type']}'</br>";
    $variable_description .= "&nbsp;Context: '$type->val'</br>";
    $message = $variable_description . 'Error: ' . $message;
    parent::__construct($message, 0);
  }

  /**
   * String to describe the error.
   *
   * @return string
   *   String to return.
   */
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

}

/**
 * Exception for a mis-configured element context definition.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XMLFormsContextDefinitionException extends XMLFormsContextException {
// @codingStandardsIgnoreEnd

  /**
   * Constructor function for the XMLFormsContextDefinitionException class.
   *
   * @param ContextType $type
   *   The context type to build an exception for.
   * @param \Drupal\objective_forms\FormElement $element
   *   The form element being referred to when the exception is thrown.
   */
  public function __construct(ContextType $type, FormElement $element) {
    $message = "Specifies an XPath context of {$type} but none is defined. Check the form defintion";
    parent::__construct($type, $element, $message);
  }

}

/**
 * The given context DOMNode could not be found.
 *
 * In some cases, this is acceptable; in others, it is not.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XMLFormsContextNotFoundException extends XMLFormsContextException {
// @codingStandardsIgnoreEnd

  /**
   * Constructor function for the XMLFormsContextNotFoundException class.
   *
   * @param ContextType $type
   *   The context type to build an exception for.
   * @param \Drupal\objective_forms\FormElement $element
   *   The form element being referred to when the exception is thrown.
   */
  public function __construct(ContextType $type, FormElement $element) {
    $message = "The DOMNode associated with the context {$type->val} was not found.";
    parent::__construct($type, $element, $message);
  }

}
