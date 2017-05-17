<?php

namespace Drupal\attribute_access_policies\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides attribute-based condition plugin definitions for entities.
 *
 * @see \Drupal\attribute_access_policies\Plugin\entity_access_policies\AttributeBasedConditionPolicy
 */
class AttributeBasedConditionPolicy extends DeriverBase implements ContainerDeriverInterface {

  /**
   * A config factory instance.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $configStorage;

  /**
   * A typed config manager instance.
   *
   * @var \Drupal\Core\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * Constructs new AttributeBasedConditionPolicy deriver.
   */
  public function __construct(EntityStorageInterface $config_storage, TypedDataManagerInterface $typed_data_manager) {
    $this->configStorage = $config_storage;
    $this->typedDataManager = $typed_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')->getStorage('attribute_based_condition_policy'),
      $container->get('typed_data_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_definition) {
    foreach ($this->getPolicyConfigurations() as $name => $config) {
      $this->derivatives[$name] = $this->deriveDefinition($base_definition, $config);
    }

    return $this->derivatives;
  }

  /**
   * Helper method to load configuration as typed data.
   */
  protected function getPolicyConfigurations() {
    $policy_configs = $this->configStorage->loadMultiple();
    return $policy_configs;
  }

  /**
   * Helper method to define a new plugin definition.
   */
  protected function deriveDefinition($base_definition, $config) {
    $entity_condition = $this->toTypedData($config->getEntityCondition());
    $user_condition = $this->toTypedData($config->getUserCondition());

    $definition = $base_definition + [
      'entity_types' => $config->getEntityTypeIds(),
      'operations' => $config->getOperations(),
      'entity_condition' => $entity_condition,
      'user_condition' => $user_condition,
    ];

    return $definition;
  }

  /**
   * Helper method to create TypedData Conditions and ConditionGroups.
   *
   * Recursively turns config into TypedData Conditions and ConditionGroups.
   */
  protected function toTypedData($config) {
    $config['members'] = array_map(function ($member) {
      if ($member['type'] == 'condition') {
        return $this->createTypedData('condition', $member);
      }
      else {
        return $this->toTypedData($member);
      }
    }, $config['members']);

    return $this->createTypedData('condition_group', $config);
  }

  /**
   * Helper function to create TypedData instances.
   */
  protected function createTypedData($type, $data) {
    $definition = $this->typedDataManager->createDataDefinition($type);
    return $this->typedDataManager->create($definition, $data);
  }

}
