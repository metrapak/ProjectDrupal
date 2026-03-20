<?php

namespace Drupal\ap_task92\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\Entity\Node;

/**
 * Provides a 'NodesTable' block.
 */
#[Block(
  id: "nodes_table",
  admin_label: new TranslatableMarkup("Nodes table"),
)]
class NodesTable extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $header = ['id', 'title', 'created'];

    $nids = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->range(0, 100)
      ->execute();

    $nodes = Node::loadMultiple($nids);
    $rows = [];

    foreach ($nodes as $node) {
      $rows[] = [
        $node->id(),
        $node->label(),
        $node->getCreatedTime()
      ];
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
  }

}
