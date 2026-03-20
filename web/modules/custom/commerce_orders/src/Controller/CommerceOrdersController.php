<?php

namespace Drupal\commerce_orders\Controller;

use Drupal\commerce_orders\CommerceOrdersService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommerceOrdersController.
 */
class CommerceOrdersController extends ControllerBase {

  /**
   * Drupal\Core\Render\RendererInterface definition.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected Renderer $renderer;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The commerce orders service.
   *
   * @var \Drupal\commerce_orders\CommerceOrdersService
   */
  protected CommerceOrdersService $commerceOrdersService;

  /**
   * CommerceOrdersController constructor.
   *
   * @param \Drupal\Core\Render\Renderer $renderer
   *   Renderer.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\commerce_orders\CommerceOrdersService $commerceOrdersService
   *   Commerce orders service.
   */
  final public function __construct(
    Renderer $renderer,
    EntityTypeManagerInterface $entity_type_manager,
    CommerceOrdersService $commerce_orders_service) {
    $this->renderer = $renderer;
    $this->entityTypeManager = $entity_type_manager;
    $this->commerceOrdersService = $commerce_orders_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('entity_type.manager'),
      $container->get('commerce_orders.commers_order_service')
    );
  }

  /**
   * Get orders statistics.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request.
   *
   * @return array
   *   The orders' statistics page ready for output.
   */
  public function getOrdersStatistics(Request $request): array {
    $view = views_embed_view('list_of_products', 'block_1');
    $view = $this->renderer->render($view);
    $form = $this->formBuilder()->getForm('Drupal\commerce_orders\Form\CommerceOrdersFilterForm');

    $orders_statistics = $this->commerceOrdersService->getOrdersStatistics($request);

    return [
      '#theme' => 'commerce_orders',
      '#title' => 'Products',
      '#filter' => $form,
      '#view' => $view,
      '#orders_statistics' => $orders_statistics,
    ];
  }

}
