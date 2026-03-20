<?php

namespace Drupal\ap_task92\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a 'HelloWorld' block.
 */
#[Block(
  id: "hello_world",
  admin_label: new TranslatableMarkup("Hello world"),
)]
class HelloWorld extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme' => 'hello_world',
      '#message' => 'Hello World!',
    ];
  }

}
