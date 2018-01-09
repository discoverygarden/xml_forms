<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Select association form.
 *
 * This is used as part of the ingest process.
 */
class SelectAssociationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_select_association_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, array $associations = []) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/select_association.form');
    $get_default = function ($name, $default) use ($form_state) {
      return $form_state->getValue($name) ? $form_state->getValue($name) : $default;
    };
    $form_state->set('associations', $associations);
    $models = xml_form_builder_select_association_form_get_models($associations);
    $selected_model = $get_default('models', key($models));
    $forms = xml_form_builder_select_association_form_get_forms($associations, $selected_model);
    $selected_form = $get_default('available_forms', key($forms));
    // We check if the form exists in case the model was changed.
    $selected_form = in_array($selected_form, $forms) ? $selected_form : $forms[current($forms)];

    $multiple_forms = count($forms) > 1;
    $multiple_models = count($models) > 1;
    return [
      'models' => [
        '#title' => $this->t('Select a Content Model'),
        '#type' => 'select',
        '#access' => $multiple_models,
        '#options' => $models,
        '#default_value' => $selected_model,
        '#ajax' => [
          'callback' => 'xml_form_builder_select_association_form_ajax_callback',
          'wrapper' => 'forms_wrapper',
          'method' => 'replace',
          'effect' => 'fade',
        ],
      ],
      'available_forms' => [
        '#access' => $multiple_forms,
        '#title' => $this->t('Select a Form'),
        '#prefix' => '<div id="forms_wrapper">',
        '#suffix' => '</div>',
        '#type' => 'radios',
        '#options' => $forms,
        '#default_value' => $selected_form,
      ],
      'association' => [
        '#type' => 'value',
        '#default_value' => current($associations),
        '#value_callback' => 'xml_form_builder_select_association_form_value_callback',
      ],
    ];
  }

  /**
   * Set the selected association in both the association/metadata steps.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $association_step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_association_step');
    $association_step_storage['association'] = $form_state->getValue('association');
  }

  /**
   * Undoes any changes the regular submit handler did.
   */
  public function undoSubmit(array $form, FormStateInterface $form_state) {
    $association_step_storage = &islandora_ingest_form_get_step_storage($form_state, 'xml_form_builder_association_step');
    unset($association_step_storage['association']);
  }

}
