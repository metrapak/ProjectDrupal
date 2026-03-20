<?php

namespace Drupal\commerce_orders;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommerceOrdersService.
 */
class CommerceOrdersService implements CommerceOrdersInterface{

  /**
   * CommerceOrdersService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The EntityTypeManager service.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection.
   */
  public function __construct(
      /**
       * The entity type manager service instance.
       */
      protected EntityTypeManagerInterface $entityTypeManager,
      /**
       * Database connection.
       */
      protected Connection $connection
  )
  {
  }

  /**
   * {@inheritdoc}
   */
  public function getOrdersStatistics(Request $request): array {
    $date_from_string = $request->query->get('from');
    $date_to_string = $request->query->get('to');

    $date_from_timestamp = $date_from_string ? $this->getDateFromTimestamp($date_from_string) : NULL;
    $date_to_timestamp = $date_to_string ? $this->getDateToTimestamp($date_to_string) : NULL;

    $orders_statistics = [];
    $orders_statistics['orders_count'] = $this->getOrdersCount($date_from_timestamp, $date_to_timestamp);
    $orders_statistics['average_orders_value'] = $this->getAverageOrdersSum($date_from_timestamp, $date_to_timestamp);
    // Here to print user id of object.
    $orders_statistics['most_purchased_entity'] = $this->getPurchasedEntity($this->getMostPurchasedEntityId($date_from_timestamp, $date_to_timestamp))->id();

    return $orders_statistics;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrdersCount($date_from_timestamp, $date_to_timestamp): int {
    $query = $this->entityTypeManager->getStorage('commerce_order')->getQuery();
    $query->accessCheck(FALSE);
    $query->condition('state', 'completed');

    if (!empty($date_from_timestamp)) {
      $query->condition('completed', $date_from_timestamp, '>=');
    }

    if (!empty($date_to_timestamp)) {
      $query->condition('completed', $date_to_timestamp, '<=');
    }
    $query->count();

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getAverageOrdersSum($date_from_timestamp, $date_to_timestamp): float {
    $query = $this->connection->select('commerce_order');
    $query->condition('state', 'completed');

    if (!empty($date_from_timestamp)) {
      $query->condition('completed', $date_from_timestamp, '>=');
    }

    if (!empty($date_to_timestamp)) {
      $query->condition('completed', $date_to_timestamp, '<=');
    }

    $query->addExpression('AVG(total_price__number)');
    $result = $query->execute()->fetchField();

    return round($result, 2);
  }

  /**
   * {@inheritdoc}
   */
  public function getMostPurchasedEntityId($date_from_timestamp, $date_to_timestamp): int {
    $query = $this->connection->select('commerce_order_item', 'coi');
    $query->innerJoin('commerce_order', 'co', 'coi.order_id = co.order_id');
    $query->condition('co.state', 'completed');
    if (!empty($date_from_timestamp)) {
      $query->condition('co.completed', $date_from_timestamp, '>=');
    }
    if (!empty($date_to_timestamp)) {
      $query->condition('co.completed', $date_to_timestamp, '<=');
    }
    $query->addField('coi', 'purchased_entity');
    $query->addExpression('COUNT(coi.purchased_entity)', 'count');
    $query->groupBy('purchased_entity');
    $query->orderBy('count', 'DESC');
    $query->range(0, 1);

    return $query->execute()->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchasedEntity($id): EntityInterface {
    return $this->entityTypeManager->getStorage('commerce_product_variation')->load($id);
  }

  /**
   * Gets date from timestamp.
   *
   * @param $date_from_string
   *   The date from string.
   *
   * @return int
   *   Date from timestamp
   */
  public function getDateFromTimestamp($date_from_string): int {
    $date_from = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $date_from_string);
    return $date_from->setTime(0, 0, 0)->getTimestamp();
  }

  /**
   * Gets date to timestamp.
   *
   * @param $date_to_string
   *   The date to string.
   *
   * @return int
   *   Date to timestamp
   */
  public function getDateToTimestamp($date_to_string): int {
    $date_to = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $date_to_string);
    return $date_to->setTime(23, 59, 59)->getTimestamp();
  }

}
