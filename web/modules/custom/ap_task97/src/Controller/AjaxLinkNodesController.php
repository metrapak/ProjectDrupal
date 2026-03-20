<?php

namespace Drupal\ap_task97\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
/**
 * Class AjaxLinkNodesController.
 */
class AjaxLinkNodesController extends ControllerBase  {

  /**
   * AjaxlinkAction.
   *
   * @return string
   *   Return Hello string.
   */
  public function ajaxLinkAction() {

    $query = \Drupal::entityQuery('node')->accessCheck(FALSE)->count();
    $count = $query->execute();

    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $content['#markup'] = $count;

    $title = 'amount of nodes';
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand($title, $content,
      ['width' => '400', 'height' => '400']));

    return $response;
  }

}
