<?php

namespace Drupal\users_data_import_from_csv;
use Drupal\user\Entity\User;

class ImportUsersFromCsvBatch  {
  public static function importUsersDataFromCsv($file, &$context): void {
    $skip_first_line = TRUE;
    if (($handle = fopen($file,  'r')) ) {
      if ($skip_first_line) {
        fgetcsv($handle, 0);
      }
      while (($data = fgetcsv($handle, 0 )) !== FALSE) {
        $user = User::create();
        $user->setEmail($data[0]);
        $user->setUsername($data[0]);
        $user->setPassword($data[1]);
        $user->save();
      }
      fclose($handle);
    }
  }

  public static function importUsersDataFromCsvFinishedCallback($success, $results, $operations): void{
    if ($success) {
      $message = t('Export finished successfully');
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addMessage($message);
  }

}
