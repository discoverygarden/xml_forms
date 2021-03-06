<?php

/**
 * @file
 * Action class.
 */

module_load_include('inc', 'xml_form_api', 'Create');
module_load_include('inc', 'xml_form_api', 'Read');
module_load_include('inc', 'xml_form_api', 'Update');
module_load_include('inc', 'xml_form_api', 'Delete');

use Drupal\objective_forms\FormPropertyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Container Class for all actions.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLFormApi.info" instead of just "XMLFormApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class Actions implements FormPropertyInterface {
// @codingStandardsIgnoreEnd

  use StringTranslationTrait;

  /**
   * Create one or more DOMNodes in the Document.
   *
   * @var Create
   */
  public $create;

  /**
   * Read one or more DOMNodes in the Document. Used to populate the form.
   *
   * @var Read
   */
  public $read;

  /**
   * Update one or more DOMNodes values in the Document.
   *
   * @var Update
   */
  public $update;

  /**
   * Delete one or more DOMNodes in the Document.
   *
   * @var Delete
   */
  public $delete;

  /**
   * Creates and Actions instance.
   *
   * @param mixed $value
   *   Array of property values that can be set.
   */
  public function __construct($value) {
    if (!is_array($value)) {
      throw new InvalidArgumentException($this->t('The value parameter to Actions must be an array.'));
    }
    if (isset($value['create']) && is_array($value['create'])) {
      $this->create = new Create($value['create']);
    }
    if (isset($value['read']) && is_array($value['read'])) {
      $this->read = new Read($value['read']);
    }
    if (isset($value['update']) && is_array($value['update'])) {
      $this->update = new Update($value['update']);
    }
    if (isset($value['delete']) && is_array($value['delete'])) {
      $this->delete = new Delete($value['delete']);
    }
  }

  /**
   * Get the appropriate ActionInterface to perform.
   *
   * This is called by the FormProcessor, depending on the state of certain
   * variables one of the following actions will be returned.
   *
   * Note that these cases are the default, depending on other settings within
   * the action element the return ActionInterface may differ.
   *
   * Create
   *  The FormElement in question doesn't have a registered DOMNode.
   * Update
   *  The FormElement that corresponds to the Node in question is still part of
   *   the definition.
   * Delete
   *  The FormElement in question, no longer exists in the form and it has a
   *   registered DOMNode.
   *
   * @param XMLDocument $document
   *   The document to get the action for.
   * @param FormElement $element
   *   The element to get the action for.
   * @param mixed $value
   *   The value to get the action for.
   *
   * @return array
   *   Gets the action type and the ActionInterface object, such as (string,
   *   ActionInterface).
   */
  public function getAppropriateAction(XMLDocument $document, FormElement $element, $value = NULL) {
    if ($this->shouldDelete($document, $element, $value)) {
      return [ActionInterface::DELETE, $this->delete];
    }
    elseif ($this->shouldUpdate($document, $element, $value)) {
      return [ActionInterface::UPDATE, $this->update];
    }
    // If node is not registered.
    elseif ($this->shouldCreate($document, $element, $value)) {
      return [ActionInterface::CREATE, $this->create];
    }
    return [ActionInterface::UNDEFINED, NULL];
  }

  /**
   * Determine if we should remove this FormElement.
   *
   * Checks the Delete ActionInterface's properties to do so.
   *
   * @param XMLDocument $document
   *   The document to check.
   * @param FormElement $element
   *   The element to check.
   * @param mixed $value
   *   The value to check deletion for.
   *
   * @return bool
   *   TRUE if it should be removed; FALSE otherwise.
   */
  protected function shouldDelete(XMLDocument $document, FormElement $element, $value = NULL) {
    if (isset($this->delete)) {
      return $this->delete->shouldExecute($document, $element, $value);
    }
    return FALSE;
  }

  /**
   * Determine if we should update this FormElement.
   *
   * Checks the Update ActionInterface's properties to do so.
   *
   * @param XMLDocument $document
   *   The document to check.
   * @param FormElement $element
   *   The element to check.
   * @param mixed $value
   *   The value to check updating for.
   *
   * @return bool
   *   TRUE if it should be updated; FALSE otherwise.
   */
  protected function shouldUpdate(XMLDocument $document, FormElement $element, $value = NULL) {
    if (isset($this->update)) {
      return $this->update->shouldExecute($document, $element, $value);
    }
    return FALSE;
  }

  /**
   * Determine if we should create this FormElement.
   *
   * Checks the Create ActionInterface's properties to do so.
   *
   * @param XMLDocument $document
   *   The document to check.
   * @param FormElement $element
   *   The element to check.
   * @param mixed $value
   *   The value to check creation for.
   *
   * @return bool
   *   TRUE if it should be created; FALSE otherwise.
   */
  protected function shouldCreate(XMLDocument $document, FormElement $element, $value = NULL) {
    if (isset($this->create)) {
      return $this->create->shouldExecute($document, $element, $value);
    }
    return FALSE;
  }

  /**
   * Converts this object to a Drupal Form Property value.
   *
   * @return mixed
   *   Returns the array representation of this class, which can be used within
   *   a Drupal Form.
   */
  public function toDrupalForm() {
    return [
      'create' => isset($this->create) ? $this->create->toDrupalForm() : NULL,
      'read' => isset($this->read) ? $this->read->toDrupalForm() : NULL,
      'update' => isset($this->update) ? $this->update->toDrupalForm() : NULL,
      'delete' => isset($this->delete) ? $this->delete->toDrupalForm() : NULL,
    ];
  }

}
