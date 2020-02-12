<?php

namespace Drupal\rng\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a event config entity.
 */
interface EventTypeInterface extends ConfigEntityInterface {

  /**
   * Get event entity type ID.
   *
   * @return string
   *   An entity type ID.
   */
  public function getEventEntityTypeId();

  /**
   * Sets the event entity type ID.
   *
   * @param string $entity_type
   *   An entity type ID.
   */
  public function setEventEntityTypeId($entity_type);

  /**
   * Get event bundle.
   *
   * @return string
   *   A bundle name.
   */
  public function getEventBundle();

  /**
   * Sets the event bundle.
   *
   * @param string $bundle
   *   A bundle name.
   */
  public function setEventBundle($bundle);

  /**
   * Gets which permission on event entity grants 'event manage' permission.
   */
  public function getEventManageOperation();

  /**
   * Sets operation to mirror from the event entity.
   *
   * @param string $permission
   *   The operation to mirror.
   *
   * @return static
   *   Return this event type for chaining.
   */
  public function setEventManageOperation($permission);

  /**
   * Gets whether anonymous registrants should be created/used.
   *
   * @return bool
   *   The setting.
   */
  public function getAllowAnonRegistrants();

  /**
   * Set whether or not to allow anonymous registrants.
   *
   * @param bool $allow_anon_registrants
   *
   * @return static
   *   Return this event type for chaining.
   */
  public function setAllowAnonRegistrants($allow_anon_registrants);

  /**
   * Gets whether registrants should automatically sync with their identities.
   *
   * @return bool
   *   The setting.
   */
  public function getAutoSyncRegistrants();

  /**
   * Set whether or not to automatically sync identity data with registrant data.
   *
   * @param bool $auto_sync_registrants
   *
   * @return static
   *   Return this event type for chaining.
   */
  public function setAutoSyncRegistrants($auto_sync_registrants);

  /**
   * Gets whether existing users should be added as identities when email matches.
   *
   * @return bool
   *   The setting.
   */
  public function getAutoAttachUsers();

  /**
   * Set whether or not to automatically add user identities that match by email.
   *
   * @param bool $auto_attach_users
   *
   * @return static
   *   Return this event type for chaining.
   */
  public function setAutoAttachUsers($auto_attach_users);

  /**
   * Gets the machine name of field containing email on registrant to use for sync.
   *
   * @return bool
   *   The setting.
   */
  public function getRegistrantEmailField();

  /**
   * Set the machine name of an email field on the registrant to use for sync.
   *
   * @param string $registrant_email_field
   *
   * @return static
   *   Return this event type for chaining.
   */
  public function setRegistrantEmailField($registrant_email_field);

  /**
   * Whether to allow event managers to customize default rules.
   *
   * @return bool
   *   Whether event managers are allowed to customize default rules.
   */
  public function getAllowCustomRules();

  /**
   * Set whether event managers can customize default rules.
   *
   * @param bool $allow
   *   Whether event managers are allowed to customize default rules.
   *
   * @return $this
   *   Return this event type for chaining.
   */
  public function setAllowCustomRules($allow);

  /**
   * Registrant type for new registrants associated with this event type.
   *
   * @return string|null
   *   The Registrant type used for new registrants associated with this event
   *   type.
   */
  public function getDefaultRegistrantType();

  /**
   * Default messages configured for this event type.
   *
   * @return array
   */
  public function getDefaultMessages();

  /**
   * Set default messages for this event type.
   *
   * @param array $messages
   *   Default messages array.
   */
  public function setDefaultMessages($messages);

  /**
   * Whether a identity type can be created.
   *
   * @param string $entity_type
   *   The identity entity type ID.
   * @param string $bundle
   *   The identity bundle.
   *
   * @return bool
   *   Whether a identity type can be created.
   */
  public function canIdentityTypeCreate($entity_type, $bundle);

  /**
   * Set whether an identity type can be created.
   *
   * @param string $entity_type
   *   The identity entity type ID.
   * @param string $bundle
   *   The identity bundle.
   * @param bool $enabled
   *   Whether the identity type can be created.
   *
   * @return $this
   *   Return this event type for chaining.
   */
  public function setIdentityTypeCreate($entity_type, $bundle, $enabled);

  /**
   * Get the form display mode used when the identity is created inline.
   *
   * @param string $entity_type
   *   The identity entity type ID.
   * @param string $bundle
   *   The identity bundle.
   *
   * @return string
   *   The form display mode used when the identity is created inline.
   */
  public function getIdentityTypeEntityFormMode($entity_type, $bundle);

  /**
   * Get the form display modes for creating identities inline.
   *
   * @return array
   *   An array keyed as follows: [entity_type][bundle] = form_mode.
   */
  public function getIdentityTypeEntityFormModes();

  /**
   * Set the form display mode used when the identity is created inline.
   *
   * @param string $entity_type
   *   The identity entity type ID.
   * @param string $bundle
   *   The identity bundle.
   * @param string $form_mode
   *   The form mode ID.
   *
   * @return $this
   *   Return this event type for chaining.
   */
  public function setIdentityTypeEntityFormMode($entity_type, $bundle, $form_mode);

  /**
   * Whether an existing identity type can be referenced.
   *
   * @param string $entity_type
   *   The identity entity type ID.
   * @param string $bundle
   *   The identity bundle.
   *
   * @return bool
   *   Whether an existing identity type can be referenced.
   */
  public function canIdentityTypeReference($entity_type, $bundle);

  /**
   * Set whether existing identity type can be referenced.
   *
   * @param string $entity_type
   *   The identity entity type ID.
   * @param string $bundle
   *   The identity bundle.
   * @param bool $enabled
   *   Whether existing identity type can be referenced.
   *
   * @return $this
   *   Return this event type for chaining.
   */
  public function setIdentityTypeReference($entity_type, $bundle, $enabled);

  /**
   * Set registrant type for new registrants associated with this event type.
   *
   * @param string|null $registrant_type_id
   *   The Registrant type used for new registrants associated with this event
   *   type.
   *
   * @return $this
   *   Return this event type for chaining.
   */
  public function setDefaultRegistrantType($registrant_type_id);

  /**
   * Create or clean up courier_context if none exist for an entity type.
   *
   * @param string $entity_type
   *   Entity type of the event type.
   * @param string $operation
   *   An operation: 'create' or 'delete'.
   */
  public static function courierContextCC($entity_type, $operation);

}
