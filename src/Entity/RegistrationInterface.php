<?php

namespace Drupal\rng\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface defining a Registration entity.
 */
interface RegistrationInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Get associated event.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   An entity, or NULL if the event does not exist.
   */
  public function getEvent();

  /**
   * Get the event meta object for this event.
   *
   * @return \Drupal\rng\EventMetaInterface
   *   The event meta entity.
   */
  public function getEventMeta();
  /**
   * Returns the registration creation timestamp.
   *
   * @return int
   *   Creation timestamp of the registration.
   */
  public function getCreatedTime();

  /**
   * Sets the registration creation timestamp.
   *
   * @param int $timestamp
   *   The registration creation timestamp.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function setCreatedTime($timestamp);

  /**
   * Set associated event.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   An entity that has an associated event.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function setEvent(ContentEntityInterface $entity);

  /**
   * Check to see if this registration is confirmed.
   *
   * @return bool
   *   Whether or not this registration is confirmed.
   */
  public function isConfirmed();

  /**
   * Set the registration to confirmed (or unconfirmed).
   *
   * @param bool $confirmed
   *   Whether to set confirmed or unconfirmed.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function setConfirmed($confirmed);

  /**
   * Get the User object that owns this registration.
   *
   * @return \Drupal\user\UserInterface
   *   The User object.
   */
  public function getOwner();

  /**
   * Set the owner of the registration to object.
   *
   * @param \Drupal\user\UserInterface $account
   *   The user account object.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function setOwner(UserInterface $account);

  /**
   * Get the owner uid of this registration.
   *
   * @return int
   *   The uid for the owner.
   */
  public function getOwnerId();

  /**
   * Set the owner of this registration by UID.
   *
   * @param int $uid
   *   The User ID.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function setOwnerId($uid);

  /**
   * Get a string representing the date of the event.
   *
   * @return string
   *   Date string to summarize the event date.
   */
  public function getDateString();

  /**
   * Get registrants IDs for the registration.
   *
   * @return integer[]
   *   An array of registrant IDs.
   */
  public function getRegistrantIds();

  /**
   * Get registrants for the registration.
   *
   * @return \Drupal\rng\Entity\RegistrantInterface[]
   *   An array of registrant entities.
   */
  public function getRegistrants();

  /**
   * Get the number of registrants assigned to this registration.
   *
   * Returns the number of registrants with or without identities.
   *
   * @return int
   *   The value of the RegistrantQty field.
   */
  public function getRegistrantQty();

  /**
   * Set the RegistrantQty field.
   *
   * This is the maximum number of registrants allowed to be attached to this
   * registration, or 0 if unlimited.
   *
   * @param int $qty
   *   Number of registrants for this registration, or 0 for unlimited.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   *
   * @throws \Drupal\rng\Exception\MaxRegistrantsExceededException
   */
  public function setRegistrantQty($qty);

  /**
   * Check to determine whether all registrants have been set on a registration.
   *
   * @return bool
   *   Whether a registration can add new registrants.
   */
  public function canAddRegistrants();

  /**
   * Searches registrants on this registration for an identity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $identity
   *   The identity to search.
   *
   * @return bool
   *   Whether the identity is a registrant.
   */
  public function hasIdentity(EntityInterface $identity);

  /**
   * Shortcut to add a registrant entity.
   *
   * Take care to ensure the identity is not already on the registration.
   *
   * @param \Drupal\Core\Entity\EntityInterface $identity
   *   The identity to add.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function addIdentity(EntityInterface $identity);

  /**
   * Get groups for the registration.
   *
   * @return \Drupal\rng\Entity\GroupInterface[]
   *   An array of registration_group entities.
   */
  public function getGroups();

  /**
   * Add a group to the registration.
   *
   * @param \Drupal\rng\Entity\GroupInterface $group
   *   The group to add.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function addGroup(GroupInterface $group);

  /**
   * Remove a group from the registration.
   *
   * @param int $group_id
   *   The ID of a registration_group entity.
   *
   * @return \Drupal\rng\Entity\RegistrationInterface
   *   Returns registration for chaining.
   */
  public function removeGroup($group_id);

}
