<?php

namespace Drupal\users_data_import_from_csv\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Class UsersImportForm.
 */
class UsersImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'users_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['users_export'] = ['#type' => 'submit', '#value' => $this->t('Import data and create users ')];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file = 'users_data.csv';
    $batch = [
      'title' => t('Importing data and creating users'),
      'operations' => [
        [
          '\Drupal\users_data_import_from_csv\ImportUsersFromCsvBatch::importUsersDataFromCsv' , [$file],
        ],
      ],
      'finished' => '\Drupal\users_data_import_from_csv\ImportUsersFromCsvBatch::importUsersDataFromCsvFinishedCallback',
    ];
    batch_set($batch);
  }
}
