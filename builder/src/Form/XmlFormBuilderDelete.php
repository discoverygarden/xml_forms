<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Get the Delete Page Form.
 */
class XmlFormBuilderDelete extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_delete';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state, $form_name = NULL) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'XMLFormDatabase');
    if (!\XMLFormDatabase::Exists($form_name)) {
      drupal_set_message($this->t('Form "%name" does not exist.', [
        '%name' => $form_name,
      ]), 'error');
      throw new NotFoundHttpException();
    }
    return [
      'form_name' => [
        '#type' => 'hidden',
        '#value' => $form_name,
      ],
      'description' => [
        '#prefix' => '<div>',
        '#markup' => $this->t('Are you sure you want to delete the form <strong>%name</strong> and all related form associations? This action is irreversible.', [
          '%name' => $form_name,
        ]),
        '#suffix' => '</div>',
      ],
      'delete' => [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#name' => 'delete',
      ],
      'cancel' => [
        '#type' => 'submit',
        '#value' => $this->t('Cancel'),
        '#name' => 'cancel',
      ],
    ];
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'XMLFormDatabase');
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/associations');
    if ($form_state->getTriggeringElement()['#name'] == 'delete') {
      $form_name = $form_state->getValue('form_name');
      if (\XMLFormDatabase::Delete($form_name)) {
        foreach (xml_form_builder_get_associations([$form_name]) as $assoc) {
          db_delete('xml_form_builder_form_associations')
            ->condition('id', intval($assoc['id']))
            ->execute();
          drupal_set_message($this->t('Deleted the association ID:%id with the form %form_name.', [
            '%id' => $assoc['id'],
            '%form_name' => $form_name,
          ]));
        }
        drupal_set_message($this->t('Successfully deleted form "%name".', [
          '%name' => $form_name,
        ]));
      }
      else {
        drupal_set_message($this->t('Failed to delete form "%name".', [
          '%name' => $form_name,
        ]), 'error');
      }
    }
    $form_state->setRedirect('xml_form_builder.main');
  }

}
