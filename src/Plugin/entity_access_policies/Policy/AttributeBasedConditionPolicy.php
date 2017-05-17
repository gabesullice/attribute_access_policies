<?php

namespace Drupal\attribute_access_policies\Plugin\entity_access_policies\Policy;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\entity_access_policies\Policy\PolicyBase;
use Drupal\entity_access_policies\Lock\DefaultLock;
use Drupal\typed_data_conditions\EvaluatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Policy(
 *   id = "attribute_based_condition_policy",
 *   label = @Translation("Access policy plugin based on configured attribute-based conditions"),
 *   deriver = "Drupal\attribute_access_policies\Plugin\Derivative\AttributeBasedConditionPolicy",
 * )
 */
class AttributeBasedConditionPolicy extends PolicyBase implements ContainerFactoryPluginInterface {

  /**
   * The default lock id.
   */
  const LOCK_ID = 1;

  /**
   * The default lock id.
   */
  const KEY_ID = 1;

  /**
   * The plugin definition as provided by the plugin deriver.
   *
   * @see \Drupal\attribute_access_policies\Plugin\Derivative\AttributeBasedConditionPolicy
   *
   * @var array
   */
  protected $pluginDefinition;

  /**
   * The entity condition.
   *
   * @var \Drupal\typed_data_conditions\EvaluatorInterface
   */
  protected $entityCondition;

  /**
   * Creates an instance of an AttributeBasedConditionPolicy.
   *
   * @var array $plugin_definition
   *   The plugin definition provided by the plugin deriver.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    $this->pluginDefinition = $plugin_definition;
    $this->entityCondition = $this->pluginDefinition['entity_condition'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function applies(EntityInterface $entity) {
    $applicable_types = $this->pluginDefinition['entity_types'];
    return in_array($entity->getEntityTypeId(), $applicable_types);
  }

  /**
   * {@inheritdoc}
   */
  public function getLocks(EntityInterface $entity) {
    if ($this->entityCondition->evaluate($entity->getTypedData())) {
      $operations = $this->pluginDefinition['operations'];
      $language = $entity->language();
      $lock = DefaultLock::create(static::LOCK_ID, $operations, $language);
      return [$lock];
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getKeys(AccountInterface $account) {
    return [];
  }

  /**
   * Set the entity condition.
   */
  public function setEntityCondition(EvaluatorInterface $condition) {
    $this->entityCondition = $condition;
  }

}
