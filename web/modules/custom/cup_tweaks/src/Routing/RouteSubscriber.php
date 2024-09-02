<?php

/**
 * @file
 * Contains \Drupal\cup_tweaks\Routing\RouteSubscriber.
 */

namespace Drupal\cup_tweaks\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RoutingEvents;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('entity.taxonomy_term.canonical')) {
      $route->setDefault('_controller', '\Drupal\cup_tweaks\Controller\TaxonomyTermViewPageController::handle');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events = parent::getSubscribedEvents();

    // Come after views.
    $events[RoutingEvents::ALTER] = array('onAlterRoutes', -180);

    return $events;
  }

}
