<?php

namespace Drupal\page_notifier\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\NodeInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Hook implementations for the Page Notifier module.
 */
class PageNotifierHooks implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new PageNotifierHooks object.
   */
  public function __construct(
    protected MessengerInterface $messenger,
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected MailManagerInterface $mailManager,
    protected ConfigFactoryInterface $configFactory,
    protected AccountProxyInterface $currentUser,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('logger.factory'),
      $container->get('plugin.manager.mail'),
      $container->get('config.factory'),
      $container->get('current_user'),
    );
  }

  /**
   * Implements hook_node_insert().
   */
  #[Hook('node_insert')]
  public function nodeInsert(NodeInterface $node): void {
    if ($node->bundle() === 'page') {
      // 1. Messenger.
      $this->messenger->addStatus($this->t('A new page has been created: %title', [
        '%title' => $node->label(),
      ]));

      // 2. Logging.
      $this->loggerFactory->get('page_notifier')->info('A new page node (ID: @id) with title "@title" was created.', [
        '@id' => $node->id(),
        '@title' => $node->label(),
      ]);

      // 3. Email Notification.
      $to = $this->configFactory->get('system.site')->get('mail');
      $params = [
        'node_title' => $node->label(),
        'node_url' => $node->toUrl('canonical', ['absolute' => TRUE])->toString(),
      ];
      $langcode = $this->currentUser->getPreferredLangcode();

      $result = $this->mailManager->mail('page_notifier', 'page_created', $to, $langcode, $params, NULL, TRUE);
      if ($result['result'] !== TRUE) {
        $this->loggerFactory->get('page_notifier')->error('Problem sending email for node @id.', [
          '@id' => $node->id(),
        ]);
      }
    }
  }

  /**
   * Implements hook_node_update().
   */
  #[Hook('node_update')]
  public function nodeUpdate(NodeInterface $node): void {
    if ($node->bundle() === 'page') {
      $this->messenger->addStatus($this->t('The page %title has been updated.', [
        '%title' => $node->label(),
      ]));

      $this->loggerFactory->get('page_notifier')->info('Page node (ID: @id) "@title" was updated.', [
        '@id' => $node->id(),
        '@title' => $node->label(),
      ]);
    }
  }

  /**
   * Implements hook_node_delete().
   */
  #[Hook('node_delete')]
  public function nodeDelete(NodeInterface $node): void {
    if ($node->bundle() === 'page') {
      $this->messenger->addStatus($this->t('The page %title has been deleted.', [
        '%title' => $node->label(),
      ]));

      $this->loggerFactory->get('page_notifier')->info('Page node (ID: @id) "@title" was deleted.', [
        '@id' => $node->id(),
        '@title' => $node->label(),
      ]);
    }
  }

  /**
   * Implements hook_mail().
   */
  #[Hook('mail')]
  public function mail($key, &$message, $params): void {
    $options = [
      'langcode' => $message['langcode'],
    ];

    switch ($key) {
      case 'page_created':
        $site_mail = $this->configFactory->get('system.site')->get('mail');
        $message['from'] = $site_mail;
        $message['subject'] = $this->t('New page created: @title', ['@title' => $params['node_title']], $options);
        $message['body'][] = $this->t('A new page has been created on the site.');
        $message['body'][] = $this->t('Title: @title', ['@title' => $params['node_title']], $options);
        $message['body'][] = $this->t('Link: @url', ['@url' => $params['node_url']], $options);
        break;
    }
  }

}
