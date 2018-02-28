<?php

/**
 * @file
 * Code for creative commons form elements.
 */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;

/**
 * Creates a creative_commons form element.
 *
 * @param Drupal\Core\Form\FormStateInterface $form_state
 *   The form state.
 *
 * @return array
 *   The element definition.
 */
function xml_form_elements_creative_commons($element, FormStateInterface $form_state) {
  $storage = $form_state->getStorage();
  $modification_options = [
    'y' => t('Yes'),
    'n' => t('No'),
    'sa' => t('Yes, as long as others share alike'),
  ];

  $countries = [
    'international' => t('International'),
    'ar' => t('Argentina'),
    'au' => t('Australia'),
    'at' => t('Austria'),
    'be' => t('Belgium'),
    'br' => t('Brazil'),
    'bg' => t('Bulgaria'),
    'ca' => t('Canada'),
    'cl' => t('Chile'),
    'cn' => t('China Mainland'),
    'co' => t('Colombia'),
    'cr' => t('Costa Rica'),
    'hr' => t('Croatia'),
    'cz' => t('Czech Republic'),
    'dk' => t('Denmark'),
    'ec' => t('Ecuador'),
    'eg' => t('Egypt'),
    'ee' => t('Estonia'),
    'fi' => t('Finland'),
    'fr' => t('France'),
    'de' => t('Germany'),
    'gr' => t('Greece'),
    'gt' => t('Guatemala'),
    'hk' => t('Hong kong'),
    'hu' => t('Hungary'),
    'in' => t('India'),
    'ie' => t('Ireland'),
    'il' => t('Israel'),
    'it' => t('Italy'),
    'jp' => t('Japan'),
    'lu' => t('Luxembourg'),
    'mk' => t('Macedonia'),
    'my' => t('Malaysia'),
    'mt' => t('Malta'),
    'mx' => t('Mexico'),
    'nl' => t('Netherlands'),
    'nz' => t('New Zealand'),
    'no' => t('Norway'),
    'pe' => t('Peru'),
    'ph' => t('Philippines'),
    'pl' => t('Poland'),
    'pt' => t('Portugal'),
    'pr' => t('Puerto Rico'),
    'ro' => t('Romania'),
    'rs' => t('Serbia'),
    'sg' => t('Singapore'),
    'si' => t('Slovenia'),
    'za' => t('South Africa'),
    'kr' => t('South Korea'),
    'es' => t('Spain'),
    'se' => t('Sweden'),
    'ch' => t('Switzerland'),
    'tw' => t('Taiwan'),
    'th' => t('Thailand'),
    'uk' => t('UK: England & Wales'),
    'scotland' => t('Uk: Scotland'),
    'ug' => t('Uganda'),
    'us' => t('United states'),
    'vn' => t('Vietnam'),
  ];

  $commercial_options = [
    'y' => t('Yes'),
    'n' => t('No'),
  ];

  // Get an ID for ajax.
  if (isset($storage['xml_form_elements'][$element['#name']]['license_output_id'])) {
    $license_output_id = $storage['xml_form_elements'][$element['#name']]['license_output_id'];
  }
  else {
    $license_output_id = Html::getId('license_output');
    $storage['xml_form_elements'][$element['#name']]['license_output_id'] = $license_output_id;
  }

  // Figure out license state.
  if (!$form_state->getValue($element['#name']) && isset($element['#default_value'])) {
    // Reversing this array facilitates value_callback mangling by community.
    $default_value_array = array_reverse(explode('/', $element['#default_value']));
    $properties = explode('-', array_pop($default_value_array));

    $commercial = 'y';
    $derivatives = 'y';

    foreach ($properties as $property) {
      switch ($property) {
        case 'by':
          break;

        case 'nc':
          $commercial = 'n';
          break;

        case 'nd':
          $derivatives = 'n';
          break;

        case 'sa':
          $derivatives = 'sa';
          break;
      }
    }

    $jurisdiction = empty($default_value_array[1]) ? 'international' : $default_value_array[1];
  }
  else {
    $derivatives_value = $form_state->getValue(
      [$element['#name'], 'license_fieldset', 'allow_modifications']
    );
    $commercial_value = $form_state->getValue(
      [$element['#name'], 'license_fieldset', 'allow_commercial']
    );
    $jurisdiction_value = $form_state->getValue(
      [$element['#name'], 'license_fieldset', 'license_jurisdiction']
    );
    $derivatives = $derivatives_value ? $derivatives_value : 'y';
    $commercial = $commercial_value ? $commercial_value : 'y';
    $jurisdiction = $jurisdiction_value ? $jurisdiction_value : 'ca';
  }

  // Form elements.
  $element['license_fieldset'] = [
    '#type' => 'fieldset',
    '#collapsed' => FALSE,
    '#collapsible' => TRUE,
    '#title' => t('License'),
  ];

  $element['license_fieldset']['allow_modifications'] = [
    '#type' => 'select',
    '#title' => t('Allow modifications of your work?'),
    '#options' => $modification_options,
    '#default_value' => $derivatives,
    '#ajax' => [
      'wrapper' => $license_output_id,
      'callback' => 'xml_form_elements_creative_commons_ajax',
    ],
  ];

  $element['license_fieldset']['allow_commercial'] = [
    '#type' => 'select',
    '#title' => t('Allow commercial uses of your work?'),
    '#options' => $commercial_options,
    '#default_value' => $commercial,
    '#ajax' => [
      'wrapper' => $license_output_id,
      'callback' => 'xml_form_elements_creative_commons_ajax',
    ],
  ];

  $element['license_fieldset']['license_jurisdiction'] = [
    '#type' => 'select',
    '#title' => t('License Jurisdiction'),
    '#options' => $countries,
    '#default_value' => $jurisdiction,
    '#ajax' => [
      'wrapper' => $license_output_id,
      'callback' => 'xml_form_elements_creative_commons_ajax',
    ],
  ];
  // Value gets populated if default value is populated.
  if (\Drupal::request()->isXmlHttpRequest() || !$element['#value'] ||
    (isset($element['#default_value']) && $element['#value'] == $element['#default_value'])) {
    $element['#tree'] = TRUE;
  }
  else {
    // I win form builder.
    $element['#tree'] = FALSE;
  }

  $response = xml_form_elements_get_creative_commons($commercial, $derivatives, $jurisdiction);
  if ($response) {
    $storage['xml_form_elements'][$element['#name']]['license_uri'] = (string) $response->{'license-uri'};
    $element['license_fieldset']['license_output'] = [
      '#type' => 'item',
      '#id' => $license_output_id,
      '#markup' => '<strong>' . t('Selected license:') . '</strong><div>' . $response->html->asXml() . '</div>',
    ];
  }
  else {
    $storage['xml_form_elements'][$element['#name']]['license_uri'] = xml_form_elements_creative_commons_value($commercial, $derivatives, $jurisdiction);
    $element['license_fieldset']['license_output'] = [];
  }

  $form_state->getStorage($storage);
  return $element;
}

/**
 * Ajax callback to render the CC license.
 */
function xml_form_elements_creative_commons_ajax(array $form, FormStateInterface $form_state) {
  $parents = $form_state->getTriggeringElement()['#parents'];
  array_pop($parents);
  $creative_commons_element = NestedArray::getValue($form, $parents);

  return $creative_commons_element['license_output'];
}

/**
 * Ask the creative commons REST API for a license.
 *
 * Left as a function for use by community modules.
 *
 * @param string $commercial
 *   String toggle 'y' to allow 'n' to disallow.
 * @param string $derivatives
 *   String toggle 'y' to allow 'n' to disallow.
 * @param string $jurisdiction
 *   Legal jurisdiction code.
 *
 * @return mixed
 *   SimpleXMLElement the return from the REST API.
 *   FALSE if failed request.
 */
function xml_form_elements_get_creative_commons($commercial, $derivatives, $jurisdiction) {
  $url = Url::fromUri(
    'http://api.creativecommons.org/rest/1.5/license/standard/get',
    [
      'query' => [
        'commercial' => $commercial,
        'derivatives' => $derivatives,
        'jurisdiction' => $jurisdiction,
      ],
    ]
  )->toString();
  $client = \Drupal::httpClient();
  $response = $client->request('GET', $url);
  if ($response->getStatusCode() != 200) {
    return FALSE;
  }
  return simplexml_load_string($response->getBody()->getContents());
}

/**
 * Gets the value for the element.
 *
 * @param string $commercial
 *   String toggle 'y' to allow 'n' to disallow.
 * @param string $derivatives
 *   String toggle 'y' to allow 'n' to disallow.
 * @param string $jurisdiction
 *   Legal jurisdiction code.
 *
 * @return string
 *   The value of the element.
 */
function xml_form_elements_creative_commons_value($commercial, $derivatives, $jurisdiction) {
  $arguments = '';
  if ($commercial == 'n') {
    $arguments = "$arguments-nc";
  }
  if ($derivatives == 'n') {
    $arguments = "$arguments-nd";
  }
  elseif ($derivatives == 'sa') {
    $arguments = "$arguments-sa";
  }
  return "http://creativecommons.org/licenses/by$arguments/2.5/$jurisdiction/";
}
