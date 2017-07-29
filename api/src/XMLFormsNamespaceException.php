<?php
namespace Drupal\xml_form_api;

/**
 * Exceptions that may occur when building.
 */
class XMLFormsNamespaceException extends Exception {

  /**
   * Constructor function for the XMLFormsNamespaceException class.
   *
   * @param string $message
   *   A message to pass through from the exception.
   * @param int $code
   *   The exception code number.
   * @param null $previous
   *   The previous exception in the exception chain.
   */
  public function __construct($message = "", $code = 0, $previous = NULL) {
    parent::__construct($message, $code, $previous);
  }

}
