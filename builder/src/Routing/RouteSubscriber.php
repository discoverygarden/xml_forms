<?php

namespace Drupal\xml_form_builder\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Override the add datastream route for metadata.
   */
  public function alterRoutes(RouteCollection $collection) {
    $add_datastream_route = $collection->get('islandora.add_datastream_form');
    if ($add_datastream_route) {
      $add_datastream_route->setDefault('_form', NULL);
      $add_datastream_route->setDefault('_controller', '\Drupal\xml_form_builder\Controller\DefaultController::xml_form_builder_add_datastream_page');
    }
  }

}
