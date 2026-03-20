<?php

namespace Drupal\commerce_orders\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CommerceOrdersFilterForm.
 */
class CommerceOrdersFilterForm extends FormBase {

  /**
   * The EntityTypeManager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * CommerceOrdersFilterForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  final public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_orders_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#theme'] = 'commerce_orders_filter_form';

    $form['date_from'] = [
      '#type' => 'date',
      '#title' => $this->t('From Date'),
      '#date_date_format' => 'd/m/Y',
      '#default_value' => $this->getRequest()->query->get('from'),
    ];

    $form['date_to'] = [
      '#type' => 'date',
      '#title' => $this->t('From To'),
      '#date_date_format' => 'd/m/Y',
      '#default_value' => $this->getRequest()->query->get('to'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('Apply'),
    ];

    $form['reset'] = [
      '#type' => 'submit',
      '#name' => 'reset',
      '#value' => $this->t('Reset'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $triggering_element_name = $form_state->getTriggeringElement()['#name'];
    $date_from = $form_state->getValue('date_from');
    $date_to = $form_state->getValue('date_to');

    if ($triggering_element_name != 'reset' && !empty($date_from) && !empty($date_to)) {
      $start_date_object = DrupalDateTime::createFromFormat('Y-m-d', $date_from);
      $end_date_object = DrupalDateTime::createFromFormat('Y-m-d', $date_to);

      if ($start_date_object->diff($end_date_object)->invert == 1) {
        $form_state->setErrorByName(
          'date_to', $this->t('"Date to" can`t be earlier than "Date from"')
        );
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element_name = $form_state->getTriggeringElement()['#name'];

    if ($triggering_element_name == 'submit') {
      $parameters['to'] = $form_state->getValue('date_to');
      $parameters['from'] = $form_state->getValue('date_from');
    }
    if ($triggering_element_name == 'reset') {
      $parameters['to'] = '';
      $parameters['from'] = '';
    }

    $url = Url::fromRoute($this->getRouteMatch()->getRouteName());
    $url->setOptions(['query' => $parameters]);
    $form_state->setRedirectUrl($url);
  }


}
