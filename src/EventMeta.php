<?php

/**
 * @file
 * Contains \Drupal\rng\EventMeta.
 */

namespace Drupal\rng;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\courier\IdentityChannelManager;
use Drupal\Core\Entity\EntityInterface;

/**
 * Meta event wrapper for RNG.
 */
class EventMeta implements EventMetaInterface {

  /**
   * The entity query factory.
   *
   * @var EntityInterface
   */
  protected $entity;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The selection plugin manager.
   *
   * @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface
   */
  protected $selectionPluginManager;

  /**
   * The identity channel manager.
   *
   * @var \Drupal\courier\IdentityChannelManager
   */
  protected $identityChannelManager;

  /**
   * Constructs a new EventMeta object.
   *
   * @param \Drupal\Core\Entity\EntityManager $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface $selection_plugin_manager
   *   The selection plugin manager.
   * @param \Drupal\courier\IdentityChannelManager $identity_channel_manager
   *   The identity channel manager.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The event entity.
   */
  public function __construct(EntityManager $entity_manager, ConfigFactoryInterface $config_factory, SelectionPluginManagerInterface $selection_plugin_manager, IdentityChannelManager $identity_channel_manager, EntityInterface $entity) {
    $this->entityManager = $entity_manager;
    $this->configFactory = $config_factory;
    $this->selectionPluginManager = $selection_plugin_manager;
    $this->identityChannelManager = $identity_channel_manager;
    $this->entity = $entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityInterface $entity) {
    return new static(
      $container->get('entity.manager'),
      $container->get('config.factory'),
      $container->get('plugin.manager.entity_reference_selection'),
      $container->get('plugin.manager.identity_channel'),
      $entity
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEvent() {
    return $this->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function isAcceptingRegistrations() {
    return !empty($this->getEvent()->{EventManagerInterface::FIELD_STATUS}->value);
  }

  /**
   * {@inheritdoc}
   */
  public function duplicateRegistrantsAllowed() {
    return !empty($this->getEvent()->{EventManagerInterface::FIELD_ALLOW_DUPLICATE_REGISTRANTS}->value);
  }

  /**
   * {@inheritdoc}
   */
  public function getRegistrationTypeIds() {
    return array_map(function ($element) {
      return $element['target_id'];
    }, $this->getEvent()->{EventManagerInterface::FIELD_REGISTRATION_TYPE}->getValue());
  }

  /**
   * {@inheritdoc}
   */
  public function getRegistrationTypes() {
    return $this->entityManager->getStorage('registration_type')->loadMultiple($this->getRegistrationTypeIds());
  }

  /**
   * {@inheritdoc}
   */
  public function registrationTypeIsValid(RegistrationTypeInterface $registration_type) {
    return in_array($registration_type->id(), $this->getRegistrationTypeIds());
  }

  /**
   * {@inheritdoc}
   */
  public function getCapacity() {
    $capacity = (int) $this->getEvent()->{EventManagerInterface::FIELD_CAPACITY}->value;
    if ($capacity != '' && is_numeric($capacity) && $capacity >= 0) {
      return $capacity;
    }
    return EventMetaInterface::CAPACITY_UNLIMITED;
  }

  /**
   * {@inheritdoc}
   */
  public function remainingCapacity() {
    $capacity = $this->getCapacity();
    if ($capacity == EventMetaInterface::CAPACITY_UNLIMITED) {
      return $capacity;
    }
    $remaining = $capacity - $this->countRegistrations();
    return $remaining > 0 ? $remaining : 0;
  }

  /**
   * {@inheritdoc}
   */
  function getDefaultGroups() {
    $groups = [];
    foreach ($this->getEvent()->{EventManagerInterface::FIELD_REGISTRATION_GROUPS} as $group) {
      $groups[] = $group->entity;
    }
    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  function buildQuery($entity_type) {
    return $this->entityManager->getStorage($entity_type)->getQuery('AND')
      ->condition('event__target_type', $this->getEvent()->getEntityTypeId(), '=')
      ->condition('event__target_id', $this->getEvent()->id(), '=');
  }

  /**
   * {@inheritdoc}
   */
  function buildRegistrationQuery() {
    return $this->buildQuery('registration');
  }

  /**
   * {@inheritdoc}
   */
  function getRegistrations() {
    $query = $this->buildRegistrationQuery();
    return $this->entityManager->getStorage('registration')->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  function countRegistrations() {
    return $this->buildRegistrationQuery()->count()->execute();
  }

  /**
   * {@inheritdoc}
   */
  function buildRuleQuery() {
    return $this->buildQuery('rng_rule');
  }

  /**
   * {@inheritdoc}
   */
  function getRules($trigger = NULL) {
    $query = $this->buildRuleQuery();

    if ($trigger) {
      $query->condition('trigger_id', $trigger, '=');
    }

    return $this->entityManager->getStorage('rng_rule')->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function trigger($trigger, $context = array()) {
    $context['event'] = $this->getEvent();
    foreach ($this->getRules($trigger) as $rule) {
      foreach ($rule->getActions() as $action) {
        // @todo: get contexts for $rule; ensure they exist on $context.
        $action->execute($context);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  function buildGroupQuery() {
    return $this->buildQuery('registration_group');
  }

  /**
   * {@inheritdoc}
   */
  function getGroups() {
    $query = $this->buildGroupQuery();
    return $this->entityManager->getStorage('registration_group')->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function buildRegistrantQuery($entity_type_id = NULL) {
    $query = $this->entityManager->getStorage('registrant')->getQuery('AND')
      ->condition('registration.entity.event__target_type', $this->getEvent()->getEntityTypeId(), '=')
      ->condition('registration.entity.event__target_id', $this->getEvent()->id(), '=');

    if ($entity_type_id) {
      $query->condition('identity__target_type', $entity_type_id, '=');
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegistrants($entity_type_id = NULL) {
    $query = $this->buildRegistrantQuery($entity_type_id);
    return $this->entityManager->getStorage('registrant')->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function countProxyIdentities() {
    $total = 0;

    foreach ($this->getIdentityTypes() as $entity_type_id) {
      $count = $this
        ->selectionPluginManager
        ->getInstance([
          'target_type' => $entity_type_id,
          'handler' => 'rng_register',
          'handler_settings' => ['event_entity_type' => $this->getEvent()->getEntityTypeId(), 'event_entity_id' => $this->getEvent()->id()],
        ])
        ->countReferenceableEntities();
      if (is_numeric($count)) {
        $total += $count;
      }
    }

    return $total;
  }

  /**
   * {@inheritdoc}
   */
  public function getIdentityTypes() {
    $config = $this->configFactory->get('rng.settings');
    $identity_types = $config->get('identity_types');
    $allowed_identity_types = is_array($identity_types) ? $identity_types : [];
    $available_identity_types = array_keys($this->identityChannelManager->getChannels());
    return array_intersect($allowed_identity_types, $available_identity_types);
  }

  /**
   * {@inheritdoc}
   */
  function addDefaultAccess() {
    // Allow any user to create a registration on the event.
    $rules['user_role']['conditions']['rng_user_role'] = ['roles' => []];
    $rules['user_role']['actions']['registration_operations'] = ['operations' => ['create' => TRUE]];

    // Allow registrants to edit their registrations.
    $rules['registrant']['conditions']['rng_registration_identity'] = [];
    $rules['registrant']['actions']['registration_operations'] = ['operations' => ['view' => TRUE, 'update' => TRUE]];

    // Give event managers all rights.
    $rules['event_operation']['conditions']['rng_event_operation'] = ['operations' => ['manage event' => TRUE]];
    $rules['event_operation']['actions']['registration_operations'] = ['operations' => ['create' => TRUE, 'view' => TRUE, 'update' => TRUE, 'delete' => TRUE]];

    foreach ($rules as $rule) {
      $rng_rule = $this->entityManager->getStorage('rng_rule')->create(array(
        'event' => array('entity' => $this->getEvent()),
        'trigger_id' => 'rng_event.register',
      ));
      $rng_rule->save();
      foreach ($rule['conditions'] as $plugin_id => $configuration) {
        $this->entityManager->getStorage('rng_rule_component')->create([])
          ->setRule($rng_rule)
          ->setType('condition')
          ->setPluginId($plugin_id)
          ->setConfiguration($configuration)
          ->save();
      }
      foreach ($rule['actions'] as $plugin_id => $configuration) {
        $this->entityManager->getStorage('rng_rule_component')->create([])
          ->setRule($rng_rule)
          ->setType('action')
          ->setPluginId($plugin_id)
          ->setConfiguration($configuration)
          ->save();
      }
    }
  }

}