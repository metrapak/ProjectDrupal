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
   * CommerceOrdersController constructor.
   *
   * @param \Drupal\Core\Render\Renderer $renderer
   *   Renderer.
   * @param EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param CommerceOrdersService $commerceOrdersService
   *   Commerce orders service.
   */
  final public function __construct(
    protected Renderer $renderer,
    protected EntityTypeManagerInterface $entity_type_manager,
    protected CommerceOrdersService $commerceOrdersService) {
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
    $form = $this->formBuilder()->getForm(\Drupal\commerce_orders\Form\CommerceOrdersFilterForm::class);

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
