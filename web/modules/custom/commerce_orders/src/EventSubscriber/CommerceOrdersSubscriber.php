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
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * CommerceOrdersSubscriber constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
//      ThemeHookEvents::THEME => 'theme',
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
