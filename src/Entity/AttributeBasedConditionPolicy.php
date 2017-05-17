<?php

namespace Drupal\attribute_access_policies\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\attribute_access_policies\AttributeBasedConditionPolicyInterface;

/**
 * @ConfigEntityType(
 *   id = "attribute_based_condition_policy",
 *   config_prefix = "policy",
 *   label = @Translation("Attribute-based Condition Policy"),
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   config_export = {
 *     "id",
 *     "entity_types",
 *     "operations",
 *     "entity_condition",
 *     "user_condition",
 *   },
 * )
 */
class AttributeBasedConditionPolicy extends ConfigEntityBase implements AttributeBasedConditionPolicyInterface {

  /**
   * The entity types to which the policy should apply.
   *
   * @var string[]
   */
  protected $entity_types;

  /**
   * The entity operations to which the policy should apply.
   *
   * @var string[]
   */
  protected $operations;

  /**
   * The condition to evaluate against the entity under access control.
   *
   * @see \Drupal\typed_data_conditions\Plugin\DataType\Condition
   * @see \Drupal\typed_data_conditions\Plugin\DataType\ConditionGroup
   *
   * @var \Drupal\typed_data_conditions\EvaluatorInterface
   */
  protected $entity_condition;

  /**
   * The condition to evaluate against the user seeking access.
   *
   * @see \Drupal\typed_data_conditions\Plugin\DataType\Condition
   * @see \Drupal\typed_data_conditions\Plugin\DataType\ConditionGroup
   *
   * @var \Drupal\typed_data_conditions\EvaluatorInterface
   */
  protected $user_condition;

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds() {
    return $this->entity_types;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations() {
    return $this->operations;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityCondition() {
    return $this->entity_condition;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserCondition() {
    return $this->user_condition;
  }

}
