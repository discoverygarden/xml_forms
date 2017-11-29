<?php

/**
 * @file
 * Contains \Drupal\xml_form_builder\Form\XmlFormBuilderSettingsForm.
 */

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class XmlFormBuilderSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('xml_form_builder.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['xml_form_builder.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [
      'xml_form_builder_use_default_dc_xslts' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Use Default DC XSLTs'),
        '#description' => $this->t('Enable the use of default metadata datastream to DC transforms.'),
        '#default_value' => $this->config('xml_form_builder.settings')->get('xml_form_builder_use_default_dc_xslts'),
      ]
      ];
    return parent::buildForm($form, $form_state);
  }

}
?>
