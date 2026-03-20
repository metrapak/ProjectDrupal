<?php

namespace Drupal\commerce_orders\EventSubscriber;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\core_event_dispatcher\Event\Theme\ThemeEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CommerceOrdersSubscriber.
 */
class CommerceOrdersSubscriber implements EventSubscriberInterface {

  /**
   * CommerceOrdersSubscriber constructor.
   *
   * @param ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   */
  public function __construct(
      protected ModuleHandlerInterface $moduleHandler
  )
  {
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ThemeHookEvents::THEME => 'theme',
    ];
  }

  /**
   * Hook theme.
   *
   * @param ThemeEvent $event
   *   The event.
   */
  public function theme(ThemeEvent $event) {
    $path = $this->moduleHandler->getModule('commerce_orders')->getPath() . '/templates';
    $event->addNewThemes([
      'commerce_orders' => [
        'template' => 'commerce-orders',
        'variables' => [
          'title' => NULL,
          'filter' => NULL,
          'view' => NULL,
          'orders_statistics' => NULL,
        ],
        'path' => $path,
      ],
      'commerce_orders_filter_form' => [
        'render element' => 'form',
        'path' => $path,
      ],
    ]);
  }

}
