<?php

namespace Drupal\rng\Plugin\views\filter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\datetime\Plugin\views\filter\Date;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ViewsFilter("single_date_filter")
 */
class SingleDateFilter extends Date implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\EntityFieldManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Views Join Manager.
   *
   * @var \Drupal\views\Plugin\ViewsHandlerManager
   */
  protected $joinManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition,
      $container->get('date.formatter'),
      $container->get('request_stack')
    );
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityFieldManager = $container->get('entity_field.manager');
    $instance->joinManager = $container->get('plugin.manager.views.join');
    $instance->database = $container->get('database');
    return $instance;
  }

  public function query() {
    return parent::query();
    $alias = 'date_filter_single';

    $subquery = $this->database->select($this->table, $alias);
    $subquery->addField($alias, 'entity_id');
    $subquery->addExpression('MIN('. $alias . '.'. $this->realField .')', 'filter_min_date');
    $subquery->groupBy($alias . '.entity_id');

    $join_table = $this->options['entity_type'] . '_field_data';
    $id_field = $this->entityTypeManager->getDefinition($this->options['entity_type'])->getKey('id');
    $definition = [
      'table formula' => $subquery,
      'field' => 'entity_id',
      'left_table' => $join_table,
      'left_field' => $id_field,
      'adjust' => TRUE,
    ];
    $join = $this->joinManager->createInstance('standard', $definition);
    $this->query->addRelationship('filter_min_date_table', $join, $join_table);
    $field = 'filter_min_date_table.filter_min_date';

    $info = $this->operators();
    if (!empty($info[$this->operator]['method'])) {
      $this->{$info[$this->operator]['method']}($field);
    }
  }

}
