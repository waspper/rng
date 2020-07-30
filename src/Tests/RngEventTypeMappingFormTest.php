<?php

namespace Drupal\rng\Tests;

use Drupal\field\Entity\FieldConfig;
use Drupal\rng\EventManagerInterface;

/**
 * Tests RNG event type mapping form.
 *
 * @group rng
 */
class RngEventTypeMappingFormTest extends RngWebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['entity_test'];

  /**
   * The event type for testing.
   *
   * @var \Drupal\rng\Entity\EventTypeInterface
   */
  public $eventType;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $user = $this->drupalCreateUser(['administer event types']);
    $this->drupalLogin($user);
    $this->eventType = $this->createEventType('entity_test', 'entity_test');
  }

  /**
   * Test default state of the mapping form with a fresh event type.
   */
  public function testMappingForm() {
    $this->drupalGet($this->eventType->toUrl('field-mapping'));
    //$this->removeWhiteSpace();
    $this->assertSession()->responseContains('Select which registration types are valid for this event.');
    $this->assertSession()->responseContains('<td>Registration groups</td><td>New registrations will be added to these groups.</td><td>Exists</td>');
    $this->assertSession()->responseContains('<td>Accept new registrations</td><td></td><td>Exists</td><td></td>');
    $this->assertSession()->responseContains('<td>Maximum registrations</td><td>Maximum amount of registrations for this event.</td><td>Exists</td><td></td>');
    $this->assertSession()->responseContains('<td>Reply-to e-mail address</td><td>E-mail address that appears as reply-to when emails are sent from this event. Leave empty to use site default.</td><td>Exists</td>');
    $this->assertSession()->responseContains('<td>Allow duplicate registrants</td><td>Allows a registrant to create more than one registration for this event.</td><td>Exists</td>');
    $this->assertSession()->responseContains('Minimum number of registrants per registration.');
    $this->assertSession()->responseContains('<td>Maximum registrants</td><td>Maximum number of registrants per registration.</td><td>Exists</td><td></td>');
  }

  /**
   * Test mapping form when a field does not exist.
   */
  public function testMappingFormDeleted() {
    $url = $this->eventType->toUrl('field-mapping');
    $this->drupalGet($url);
    //$this->removeWhiteSpace();

    $this->assertSession()->responseContains('<td>Minimum registrants</td><td>Minimum number of registrants per registration.</td><td>Does not exist</td>');
    $this->assertFieldById('edit-table-rng-registrants-minimum-operations-create', 'Create', "Create button exists for 'minimum registrants' field");

    // Test the field is added back.
    $this->drupalPostForm($url, [], t('Create'));
    // $this->removeWhiteSpace();
    $this->assertSession()->responseContains('<td>Minimum registrants</td><td>Minimum number of registrants per registration.</td><td>Exists</td>');
    $this->assertText('Field Minimum registrants added.');
  }

}
