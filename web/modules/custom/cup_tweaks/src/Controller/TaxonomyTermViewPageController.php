<?php
 
/**
 * @file
 * Contains \Drupal\cup_tweaks\Controller\TaxonomyTermViewPageController.
 */

namespace Drupal\cup_tweaks\Controller;

use Drupal\views\Routing\ViewPageController;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\Views;

/**
 * Class TaxonomyTermViewPageController.
 *
 * @package Drupal\cup_tweaks\Controller
 */
class TaxonomyTermViewPageController extends ViewPageController {

  /**
   * {@inheritdoc}
   */
  public function handle($view_id, $display_id, RouteMatchInterface $route_match) {

    // Entity of the requested term.
    $term = $route_match->getParameter('taxonomy_term');
 
    // Get the vocabulary machine name of the current term.
    $vid = $term->get('vid')->first()->getValue()['target_id'];
    // Use vocabulary specific view if it exists
    $tax_view_id = 'cu_' . $vid;
    if ($view = Views::getView($tax_view_id)) {
      if ($view->access($display_id)) {
        $view_id = $tax_view_id;
      }
    }

    return parent::handle($view_id, $display_id, $route_match);
  }

}
