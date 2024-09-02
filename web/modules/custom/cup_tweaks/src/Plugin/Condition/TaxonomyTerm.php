<?php

namespace Drupal\cup_tweaks\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Taxonomy Term' condition.
 *
 * @Condition(
 *   id = "taxonomy_term",
 *   label = @Translation("Taxonomy Term"),
 *   context = {}
 * )
 */
class TaxonomyTerm extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $options = [];
    $vocabularies = \Drupal\taxonomy\Entity\Vocabulary::loadMultiple();
    foreach ($vocabularies as $machine_name => $vocabulary) {
      $options[$machine_name] = $vocabulary->label();
    }
    $form['vocabs'] = [
      '#title' => $this->t('Vocabularies'),
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $this->configuration['vocabs'],
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['vocabs'] = array_filter($form_state->getValue('vocabs'));
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    if (count($this->configuration['vocabs']) > 1) {
      $vocabs = $this->configuration['vocabs'];
      $last = array_pop($vocabs);
      $vocabs = implode(', ', $vocabs);
      return $this->t('The vocabulary is @vocabs or @last', ['@vocabs' => $vocabs, '@last' => $last]);
    }
    $vocab = reset($this->configuration['vocabs']);
    return $this->t('The vocabulary is @vocab', ['@vocab' => $vocab]);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);
    if ($path_args[1] == 'taxonomy' && $path_args[2] == 'term' && is_numeric($path_args[3])) {
      $term = \Drupal\taxonomy\Entity\Term::load($path_args[3]);
      if (in_array($term->bundle(), $this->configuration['vocabs'])) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['vocabs' => []] + parent::defaultConfiguration();
  }

}