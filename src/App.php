
<?php

use App\Contact;



if (isset($_FILES['log'])) {

  // Get the File Contents
  $csvFile = file_get_contents($_FILES['log']['tmp_name']);

  $memoryBlocks = ceil(mb_strlen($csvFile) / 1042 / 1024);

  // Parse the contacts
  $contacts = Contact::parseCSV($csvFile);

  // split contacts into blocks of 10000 records to not charge to much memory
  $contactBlocks = array_chunk($contacts, ceil(count($contacts) / 10000));

  foreach ($contactBlocks as $contactBlock) {

    try {
      // Bulk insert in blocks of 10000 records
      if (count($contactBlock) > 0)
        Contact::insert($contactBlock);
    } catch (\Illuminate\Database\QueryException $ex) { }
  }
  // print  statistics in the response
  echo json_encode(Contact::Stats());
}


if (isset($_GET['action'])) {

  switch ($_GET['action']) {
    case 'stats':
      // print  statistics in the response
      echo json_encode(Contact::Stats());

      break;

    case 'clear':
      // print  statistics in the response
      Contact::truncate();
      echo json_encode(['result' => true]);

      break;
  }

  die();
}
