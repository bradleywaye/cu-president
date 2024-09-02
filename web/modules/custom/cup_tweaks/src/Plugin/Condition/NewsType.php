<?php

namespace Drupal\cup_tweaks\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;
        
/**
 * Provides a 'News Type' condition.
 *
 * @Condition(
 *   id = "news_type",
 *   label = @Translation("News Type"),
 *   context = {}
 * )
 */
class NewsType extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('news_types', 0, NULL, TRUE);
    foreach ($terms as $term) {
      $options[$term->id()] = $term->getName();
    }
    $form['type'] = [
      '#title' => $this->t('Type'),
      '#type' => 'select',
      '#options' => $options,  
      '#default_value' => $this->configuration['type'],
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['type'] = $form_state->getValue('type');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('The news node is of type @type', ['@type' => $this->configuration['type']]);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $return = FALSE;
    $route_name = \Drupal::routeMatch()->getRouteName(); 
    if ($route_name == 'entity.node.canonical') {
      $node = $route_parameters = \Drupal::routeMatch()->getParameter('node');
      if ($node->hasField('field_news_type') && !$node->field_news_type->isEmpty()) {
        if ($node->field_news_type->first()->getValue()['target_id'] == $this->configuration['type']) {
          $return = TRUE;
        }
      }
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['type' => NULL] + parent::defaultConfiguration();
  }

}
