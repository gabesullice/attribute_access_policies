<?php

namespace Drupal\attribute_access_policies;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface AttributeBasedConditionPolicyInterface extends ConfigEntityInterface {

  /**
   * The entity types to which the policy should apply.
   *
   * @return string[]
   */
  public function getEntityTypeIds();

  /**
   * The entity operations to which the policy should apply.
   *
   * @return string[]
   */
  public function getOperations();

  /**
   * The condition to evaluate against the entity under access control.
   *
   * @return \Drupal\typed_data_conditions\EvaluatorInterface
   */
  public function getEntityCondition();

  /**
   * The condition to evaluate against the user seeking access.
   *
   * @return \Drupal\typed_data_conditions\EvaluatorInterface
   */
  public function getUserCondition();

}
