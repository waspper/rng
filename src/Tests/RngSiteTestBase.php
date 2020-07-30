<?php

namespace Drupal\rng\Tests;

use Drupal\rng\Form\EventTypeForm;

/**
 * Sets up page and article content types.
 */
abstract class RngSiteTestBase extends RngWebTestBase {

  public static $modules = ['rng', 'node'];

  /**
   * @var \Drupal\rng\Entity\RegistrationTypeInterface
   */
  public $registration_type;

  /**
   * @var \Drupal\rng\Entity\EventTypeInterface
   */
  public $event_bundle;

  /**
   * @var \Drupal\rng\Entity\EventTypeInterface
   */
  public $event_type;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->event_bundle = $this->drupalCreateContentType();
    $this->event_type = $this->createEventType('node', $this->event_bundle->id());
    EventTypeForm::createDefaultRules('node', $this->event_bundle->id());
    $this->registration_type = $this->createRegistrationType();
  }

}
