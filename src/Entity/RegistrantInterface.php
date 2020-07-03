<?php

namespace Drupal\rng\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides an interface defining a Registrant entity.
 */
interface RegistrantInterface extends ContentEntityInterface {

  /**
   * Get associated registration.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface|null
   *   The parent registration, or NULL if it does not exist.
   */
  public function getRegistration();

  /**
   * Set associated registration.
   *
   * @param \Drupal\rng\Entity\RegistrationInterface $registration
   *   The new associated registration.
   *
   * @return \Drupal\rng\Entity\RegistrantInterface
   *   Returns registrant for chaining.
   */
  public function setRegistration(RegistrationInterface $registration);

  /**
   * Get associated content entity.
   *
   * @return ContentEntityInterface|null
   *   The parent event, if set.
   */
  public function getEvent();

  /**
   * Set the event for this registrant.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $event
   *
   * @return \Drupal\rng\Entity\RegistrantInterface
   *   Returns registratant for chaining.
   */
  public function setEvent(ContentEntityInterface $event);


  /**
   * Get associated identity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   An entity, or NULL if the identity does not exist.
   */
  public function getIdentity();

  /**
   * Get associated identity entity keys.
   *
   * @return array|null
   *   An array with the keys entity_type and entity_id, or NULL if the identity
   *   does not exist.
   */
  public function getIdentityId();

  /**
   * Set associated identity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The identity to set.
   *
   * @return \Drupal\rng\Entity\RegistrantInterface
   *   Returns registrant for chaining.
   */
  public function setIdentity(EntityInterface $entity);

  /**
   * Removes identity associated with this registrant.
   *
   * @return \Drupal\rng\Entity\RegistrantInterface
   *   Returns registrant for chaining.
   */
  public function clearIdentity();

  /**
   * Checks if the identity is the registrant.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The identity to check is associated with this registrant.
   *
   * @return bool
   *   Whether the identity is the registrant.
   */
  public function hasIdentity(EntityInterface $entity);

  /**
   * Get registrants belonging to an identity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $identity
   *   An identity entity.
   *
   * @return int[]
   *   An array of registrant entity IDs.
   */
  public static function getRegistrantsIdsForIdentity(EntityInterface $identity);

}
