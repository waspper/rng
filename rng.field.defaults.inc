<?php

/**
 * @file
 * Creates field config if they do not exist.
 *
 * Call rng_add_event_field_config() directly.
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\rng\EventManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;

/**
 *
 */
function rng_add_event_field_storage($field_name, $entity_type) {
  if ($field_storage = FieldStorageConfig::loadByName($entity_type, $field_name)) {
    return NULL;
  }

  $definition = [];
  switch ($field_name) {
    case EventManagerInterface::FIELD_REGISTRATION_TYPE:
      $definition = [
        'type' => 'entity_reference',
        'settings' => [
          'target_type' => 'registration_type',
        ],
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATION_GROUPS:
      $definition = [
        'type' => 'entity_reference',
        'settings' => [
          'target_type' => 'registration_group',
        ],
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      ];
      break;

    case EventManagerInterface::FIELD_STATUS:
      $definition = [
        'type' => 'boolean',
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRANTS_CAPACITY:
      $definition = [
        'type' => 'integer',
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATIONS_CAPACITY:
      $definition = [
        'type' => 'integer',
      ];
      break;

    case EventManagerInterface::FIELD_WAIT_LIST:
      $definition = [
        'type' => 'boolean',
      ];
      break;

    case EventManagerInterface::FIELD_EMAIL_REPLY_TO:
      $definition = [
        'type' => 'email',
      ];
      break;

    case EventManagerInterface::FIELD_ALLOW_DUPLICATE_REGISTRANTS:
      $definition = [
        'type' => 'boolean',
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATION_REGISTRANTS_MINIMUM:
      $definition = [
        'type' => 'integer',
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATION_REGISTRANTS_MAXIMUM:
      $definition = [
        'type' => 'integer',
      ];
      break;
  }

  if (!empty($definition)) {
    $field_storage = entity_create('field_storage_config', [
      'field_name' => $field_name,
      'entity_type' => $entity_type,
    ] + $definition
    );
    $field_storage->save();
  }
}

/**
 *
 */
function rng_event_field_config_definition($field_name) {
  $definition = [];

  switch ($field_name) {
    case EventManagerInterface::FIELD_REGISTRATION_TYPE:
      $definition = [
        'label' => 'Registration type',
        'settings' => ['handler' => 'default'],
        'description' => t('Select which registration types are valid for this event.'),
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATION_GROUPS:
      $definition = [
        'label' => 'Registration groups',
        'settings' => ['handler' => 'default'],
        'description' => t('New registrations will be added to these groups.'),
      ];
      break;

    case EventManagerInterface::FIELD_STATUS:
      $definition = [
        'label' => 'Accept new registrations',
        'settings' => [
          'on_label' => t('Accepting new registrations'),
          'off_label' => t('Not accepting new registrations'),
        ],
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRANTS_CAPACITY:
      $definition = [
        'label' => 'Maximum registrants',
        'description' => t('Maximum amount of registrants for this event.'),
        'settings' => [
          'min' => 1,
        ],
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATIONS_CAPACITY:
      $definition = [
        'label' => 'Maximum registrations',
        'description' => t('Maximum amount of registrations for this event.'),
        'settings' => [
          'min' => 1,
        ],
      ];
      break;

    case EventManagerInterface::FIELD_WAIT_LIST:
      $definition = [
        'label' => 'Allow a wait list',
        'description' => t('Allow a waiting list for the event.'),
        'settings' => [
          'on_label' => t('Allow a waiting list for this event'),
          'off_label' => t('Do not allow a waiting list for this event'),
        ],
      ];
      break;

    case EventManagerInterface::FIELD_EMAIL_REPLY_TO:
      $definition = [
        'label' => t('Reply-to e-mail address'),
        'description' => t('E-mail address that appears as reply-to when emails are sent from this event. Leave empty to use site default.'),
      ];
      break;

    case EventManagerInterface::FIELD_ALLOW_DUPLICATE_REGISTRANTS:
      $definition = [
        'label' => 'Allow duplicate registrants',
        'description' => t('Allows a registrant to create more than one registration for this event.'),
        'settings' => [
          'on_label' => t('Allow duplicate registrants for this event'),
          'off_label' => t('Do not allow duplicate registrants for this event'),
        ],
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATION_REGISTRANTS_MINIMUM:
      $definition = [
        'label' => 'Minimum registrants per registration',
        'description' => t('Minimum number of registrants per registration.'),
        'settings' => [
          'min' => 0,
        ],
        'default_value' => [
          ['value' => 1],
        ],
      ];
      break;

    case EventManagerInterface::FIELD_REGISTRATION_REGISTRANTS_MAXIMUM:
      $definition = [
        'label' => 'Maximum registrants per registration',
        'description' => t('Maximum number of registrants per registration.'),
        'settings' => [
          'min' => -1,
        ],
        'default_value' => [
          ['value' => -1],
        ],
      ];
      break;
  }

  return $definition;
}

/**
 * @return \Drupal\Core\Field\FieldConfigInterface
 */
function rng_add_event_field_config($field_name, $entity_type, $bundle = NULL) {
  if ($field = FieldConfig::loadByName($entity_type, $bundle, $field_name)) {
    return NULL;
  }

  $definition = rng_event_field_config_definition($field_name);

  if (!empty($definition)) {
    $field = entity_create('field_config', [
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
    ] + $definition
    );
    $field->save();
  }

  return $field;
}

/**
 * Add field form defaults to a display entity.
 *
 * @param \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display
 *   A form display.
 * @param string $field_name
 *   The field name.
 *
 * @return \Drupal\Core\Entity\Display\EntityFormDisplayInterface
 *   The modified form display.
 */
function rng_add_event_form_display_defaults(EntityFormDisplayInterface $form_display, $field_name = '') {
  switch ($field_name) {
    case EventManagerInterface::FIELD_REGISTRATION_TYPE:
      $form_display->setComponent($field_name, [
        'type' => 'rng_registration_type',
      ]);
      break;

    case EventManagerInterface::FIELD_REGISTRATION_GROUPS:
      $form_display->setComponent($field_name, [
        'type' => 'rng_registration_group',
      ]);
      break;

    case EventManagerInterface::FIELD_STATUS:
      $form_display->setComponent($field_name, [
        'type' => 'boolean_checkbox',
      ]);
      break;

    case EventManagerInterface::FIELD_REGISTRANTS_CAPACITY:
      $form_display->setComponent($field_name, [
        'type' => 'unlimited_number',
      ]);
      break;

    case EventManagerInterface::FIELD_REGISTRATIONS_CAPACITY:
      $form_display->setComponent($field_name, [
        'type' => 'unlimited_number',
      ]);
      break;

    case EventManagerInterface::FIELD_WAIT_LIST:
      $form_display->setComponent($field_name, [
        'type' => 'boolean_checkbox',
      ]);
      break;

    case EventManagerInterface::FIELD_EMAIL_REPLY_TO:
      $form_display->setComponent($field_name, [
        'type' => 'email_default',
        'settings' => [
          'placeholder' => t('Leave empty to use site default.'),
        ],
      ]);
      break;

    case EventManagerInterface::FIELD_ALLOW_DUPLICATE_REGISTRANTS:
      $form_display->setComponent($field_name, [
        'type' => 'boolean_checkbox',
      ]);
      break;

    case EventManagerInterface::FIELD_REGISTRATION_REGISTRANTS_MINIMUM:
      $form_display->setComponent($field_name, [
        'type' => 'unlimited_number',
        'settings' => [
          'value_unlimited' => 0,
          'label_unlimited' => t('No registrants required'),
          'label_number' => t('Minimum'),
        ],
      ]);
      break;

    case EventManagerInterface::FIELD_REGISTRATION_REGISTRANTS_MAXIMUM:
      $form_display->setComponent($field_name, [
        'type' => 'unlimited_number',
        'settings' => [
          'value_unlimited' => -1,
          'label_unlimited' => t('No maximum'),
          'label_number' => t('Maximum'),
        ],
      ]);
      break;
  }
  return $form_display;
}
