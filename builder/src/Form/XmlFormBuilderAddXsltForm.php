<?php

namespace Drupal\xml_form_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Add an XSLT form.
 */
class XmlFormBuilderAddXsltForm extends FormBase {

  protected $fileEntityStorage;

  /**
   * Constructor.
   */
  public function __construct(EntityStorageInterface $file_entity_storage) {
    $this->fileEntityStorage = $file_entity_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('file')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_form_builder_add_xslt_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');

    $form['name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Name'),
    ];
    $form['xslt'] = [
      '#type' => 'managed_file',
      '#required' => TRUE,
      '#title' => $this->t('XSLT'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          'xsl xslt'
          ]
        ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->loadInclude('xml_form_builder', 'inc', 'includes/db');
    $file = $this->fileEntityStorage->load(reset($form_state->getValue('xslt')));
    xml_form_builder_add_xslt(file_get_contents($file->getFileUri()), $form_state->getValue('name'));
    $file->delete();
    $form_state->setRedirect('xml_form_builder.xslts_form');
  }

}
