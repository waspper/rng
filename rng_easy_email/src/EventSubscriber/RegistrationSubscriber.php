<?php

namespace Drupal\rng_easy_email\EventSubscriber;

use Drupal\rng\Event\RegistrationEvent;
use Drupal\rng_easy_email\DispatchService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\rng\EventManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class RegistrationSubscriber.
 */
class RegistrationSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\rng\EventManagerInterface definition.
   *
   * @var \Drupal\rng\EventManagerInterface
   */
  protected $rngEventManager;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Dispatch Service.
   *
   * @var \Drupal\rng_easy_email\DispatchService
   */
  protected $dispatchService;

  /**
   * Constructs a new RegistrationSubscriber object.
   */
  public function __construct(EventManagerInterface $rng_event_manager,
                              EntityTypeManagerInterface $entity_type_manager,
                DispatchService $dispatchService) {
    $this->rngEventManager = $rng_event_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->dispatchService = $dispatchService;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['rng.registration.insert'] = ['RegistrationSend'];
    $events['rng.registration.registration'] = ['RegistrationSend'];

    return $events;
  }

  /**
   * This method is called when the rng.registration.insert is dispatched.
   * This method is called when the rng.registration.registration is dispatched.
   *
   * @param RegistrationEvent $event
   *   The dispatched event.
   */
  public function RegistrationSend(RegistrationEvent $event) {
    $registration = $event->getRegistration();
    if ($registration->isConfirmed()) {
      $this->dispatchService->sendRegistration('attendee_registered', $registration);
    }
  }

}
