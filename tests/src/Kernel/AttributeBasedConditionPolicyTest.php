<?php

namespace Drupal\Tests\attribute_access_policies\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;

/**
 * @coversDefaultClass \Drupal\attribute_access_policies\Plugin\entity_access_policies\Policy\AttributeBasedConditionPolicy
 * @group attribute_access_policies
 */
class AttributeBasedConditionPolicyTest extends KernelTestBase {

  /**
   * The PolicyManager service.
   *
   * @var Drupal\entity_access_policies\PolicyManagerInterface
   */
  protected $policyManager;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_access_policies',
    'attribute_access_policies',
    'example_attribute_policy',
    'typed_data_conditions',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
    $this->policyManager = $this->container->get('plugin.manager.entity_access_policy');
  }

  /**
   * @covers ::applies
   */
  public function testPluginRegistration() {
    $config_storage = $this->container->get('entity.query')->get('attribute_based_condition_policy');
    $results = $config_storage->execute();
    $this->assertTrue(!empty($results));

    $policy_id = array_pop($results);
    $policy = $this->policyManager->createInstance("attribute_based_condition_policy:{$policy_id}");

    $this->assertNotNull($policy);
  }

  /**
   * @covers ::applies
   */
  public function testApplies() {
    $pass_entity = $this->prophesize(EntityInterface::class);
    $pass_entity->getEntityTypeId()->willReturn('taxonomy_term');

    $fail_entity = $this->prophesize(EntityInterface::class);
    $fail_entity->getEntityTypeId()->willReturn('should_be_false');

    $blue_team = $this->policyManager->createInstance('attribute_based_condition_policy:blue_team');

    $this->assertTrue($blue_team->applies($pass_entity->reveal()));
    $this->assertFalse($blue_team->applies($fail_entity->reveal()));
  }

  /**
   * @covers ::getLocks
   */
  public function testGetLocks() {
    $pass_entity = $this->prophesize(EntityInterface::class);
    $pass_entity->getEntityTypeId()->willReturn('taxonomy_term');

    ///$fail_entity = $this->prophesize(EntityInterface::class);
    ///$fail_entity->getEntityTypeId()->willReturn('should_be_false');
    $blue_team = $this->policyManager->createInstance('attribute_based_condition_policy:blue_team');

    $this->assertFalse(empty($blue_team->getLocks($pass_entity->reveal())));
  }

  /**
   * @covers ::getKeys
   */
  public function testGetKeys() {
    $this->markTestIncomplete();
  }

  /**
   * Helper method to mock a config factory.
   */
  protected function setUpConfigFactory($config_names) {
    $configs = array_map(function ($name) {
      $config = $this->prophesize(ImmutableConfig::class);
      $config->getName()->willReturn('test_policy');
      return $config->reveal();
    }, $config_names);

    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $config_factory->listAll('attribute_access_policies')->willReturn($config_names);
    $config_factory->loadMultiple($config_names)->willReturn(
      array_combine($config_names, $configs)
    );

    return $config_factory->reveal();
  }

}
