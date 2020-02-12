<?php

namespace Drupal\rng\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\rng\Entity\RegistrantInterface;
use Drupal\rng\Exception\InvalidEventException;
use Drupal\rng\RegistrantsElementUtility;
use Drupal\user\Entity\User;

/**
 * Class RegistrantFields
 *
 * Helper to assemble both an individual Registrant Entity form, and individual
 * registrant elements on a Registrants element.
 *
 * @package Drupal\rng\Form
 */
class RegistrantFields {

  /**
   * @var array
   */
  protected $form;

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Drupal\rng\Entity\RegistrantInterface
   */
  protected $registrant;

  public function __construct(array $form, FormStateInterface $form_state, RegistrantInterface $registrant = NULL) {
    $this->form = $form;
    $this->formState = $form_state;
    $this->registrant = $registrant;
  }

  /**
   * GetFields returns the standard fields for a Registrant.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param \Drupal\rng\Entity\RegistrantInterface $registrant
   *
   * @return array
   * @throws \Drupal\rng\Exception\InvalidEventException
   */
  public function getFields(array $form, FormStateInterface $form_state, RegistrantInterface $registrant) {
    $parents = $form['#parents'] ?? [];
    /** @var \Drupal\rng\Entity\RegistrationInterface $registration */
    $registration = $registrant->getRegistration();

    if ($registration) {
      $event = $registration->getEvent();
      $event_meta = $registration->getEventMeta();
      $event_type = $event_meta->getEventType();
    }
    if (empty($event)) {
      throw new InvalidEventException('No event found for registrant.');
    }

    $form['identity_types'] = [
      '#type' => 'details',
      '#title' => 'Registrant Type',
      '#tree' => TRUE,
      '#open' => TRUE,
    ];
    $form['details'] = [
      '#type' => 'details',
      '#title' => 'Registrant Info',
      '#tree' => TRUE,
      '#open' => TRUE,
    ];
    // Set #parents to 'top-level' by default.
    $form['#parents'] = $parents;
    $form['#type'] = 'container';
    $form['#event'] = $event;
    $form['#value'] = [$registrant];
    $form['#attributes']['class'][] = 'registrant-grid';
    $form['#wrapper_attributes']['class'][] = 'registrant-grid';
    $form['#attached']['library'][] = 'rng/rng.elements.registrants';
    $form['#element_validate'] = [
      [get_class($this), 'validateForm']
    ];
    $form['#tree'] = TRUE;

    if ($identity = $registrant->getIdentity()) {
      // show identity type
      // show identity display
      // if fields, show edit button
      // show remove button
      $type = $identity->getEntityType()->getLabel();
      $form['identity_types']['type'] = [
        '#type' => 'markup',
        '#markup' => $type,
      ];
      $form['identity_types']['remove_identity'] = [
        '#type' => 'submit',
        '#value' => t('Change Registrant'),
        '#submit' => ['\Drupal\rng\Form\RegistrantFields::removeIdentity', '::save'],
      ];

      $form['details']['registrant'] = [
        '#type' => 'markup',
        '#markup' => $registrant->label(),
      ];

    } else {
      // show register options buttons -
      // me
      // user - email address
      // profile - select/add widget
      $form_fields = [
        '#parents' => array_merge($parents, ['details']),
      ];
      $form_display = EntityFormDisplay::collectRenderDisplay($registrant, 'compact');
      $form_display->buildForm($registrant, $form_fields, $form_state);
      $entity_fields = Element::children($form_fields);
      $form['details'] += $form_fields;


      $form['#allow_creation'] = $event_meta->getCreatableIdentityTypes();
      $form['#allow_reference'] = $event_meta->getIdentityTypes();
      // $element = RegistrantsElementUtility::findElement($form, $form_state);
      $utility = new RegistrantsElementUtility($form, $form_state);
      $types = $utility->peopleTypeOptions();

      $default_type = array_keys($types)[0];
      $form['identity_types']['for_bundle'] = [
        '#type' => 'radios',
        '#title' => t('Person type'),
        '#options' => $types,
        '#default_value' => $default_type,
        '#parents' => array_merge($parents, ['identity_types']),
        '#tree' => TRUE,
        //'#ajax' => [
        //  'callback' => [static::class, 'ajaxRegistrantType'],
        //  'wrapper' => '',
        //  'progress' => [
        //    'type' => 'throbber',
        //    'message' => 'waiting',
        //  ],
        //],
      ];
      $selectors = $form['identity_types']['for_bundle']['#parents'];
      $item = array_shift($selectors);
      if (count($selectors)) {
        $item .= '[' . implode('][', $selectors) . ']';
      }
      $for_selector = ':input[name="'. $item .'"]';
      foreach ($entity_fields as $element) {
        $form['details'][$element]['#states'] = [
          'visible' => [
            [$for_selector => ['value' => 'anon:']],
          ]
        ];
      }
      if ($event_type->getAutoAttachUsers()) {
        $form['details'][$event_type->getRegistrantEmailField()]['#states']
        ['visible'][] = [$for_selector => ['value' => 'user:user']];
      }

      $form['details']['myself'] = [
        '#type' => 'item',
        '#title' => t('Registration will be associated with your user account on save.'),
        '#states' => [
          'visible' => [
            [$for_selector => ['value' => 'myself:']],
          ]
        ],
      ];
    }


    if ($registrant && !$registrant->isNew()) {
      $form['#title'] = t('Edit registrant');
    }

    return $form;
  }

  /**
   * Attach an identity if selected in subform.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var RegistrantInterface $registrant */
    $registrant = reset($form['#value']);
    $types = $form_state->getValue(array_merge($form['#parents'], ['identity_types']));
    if (!empty($types)) {
      switch ($types) {
        case 'myself:':
          $account = User::load(\Drupal::currentUser()->id());
          $registrant->setIdentity($account);
          break;
      }
    }
    $parents = array_merge($form['#parents'],['details']);
    $values = $form_state->getValue($parents);
    if (!empty($values)) {
      foreach ($values as $name => $value) {
        if ($registrant->hasField($name)) {
          $registrant->set($name, $value);
        }
      }
    }
  }

  public static function removeIdentity(array $form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $parents = $trigger['#array_parents'];
    // Trim off last two elements - identity_types, remove_element
    array_pop($parents);
    array_pop($parents);

    $element = NestedArray::getValue($form, $parents);

    /** @var RegistrantInterface $registrant */
    $registrant = reset($element['#value']);
    $registrant->clearIdentity();
    $event_type = $registrant->getRegistration()->getEventMeta()->getEventType();
    if ($event_type->getAutoAttachUsers()) {
      $email_field = $event_type->getRegistrantEmailField();
      $registrant->set($email_field, null);
    }
  }
}
