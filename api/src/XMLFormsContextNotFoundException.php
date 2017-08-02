<?php
namespace Drupal\xml_form_api;

/**
 * The given context DOMNode could not be found. In some cases, this is
 * acceptable; in others, it is not.
 */
class XMLFormsContextNotFoundException extends XMLFormsContextException {

  /**
   * Constructor function for the XMLFormsContextNotFoundException class.
   *
   * @param ContextType $type
   *   The context type to build an exception for.
   * @param FormElement $element
   *   The form element being referred to when the exception is thrown.
   */
  public function __construct(ContextType $type, FormElement $element) {
    $message = "The DOMNode associated with the context {$type->val} was not found.";
    parent::__construct($type, $element, $message);
  }

}
