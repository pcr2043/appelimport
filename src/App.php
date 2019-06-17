
<?php

use App\Contact;



if (isset($_FILES['log'])) {

  // Get the File Contents
  $csvFile = file_get_contents($_FILES['log']['tmp_name']);

  $memoryBlocks = ceil(mb_strlen($csvFile) / 1042 / 1024);

  // Parse the contacts
  $contacts = Contact::parseCSV($csvFile);

  // split contacts into blocks of 1000 records to not charge to much memory
  // $contactBlocks = array_chunk($contacts, ceil(count($contacts) / 1000));

  foreach ($contacts as $dataContact) {

    try {
      // tried bulk insertt but didint work, need more time to check taht
      // if ($blockofContacts != null)
      //   Contact::insert($blockofContacts);

      //so let insert one at time
      if (is_array($dataContact)) {
        $contact = new Contact();
        $contact->fill($dataContact);
        $contact->save();
      }
    } catch (\Illuminate\Database\QueryException $ex) { }
  }
  // print  statistics in the response
  echo json_encode(Contact::Stats());
}

if (isset($_GET['action']) && $_GET['action'] === 'stats') {
  // print  statistics in the response
  echo json_encode(Contact::Stats());

  die();
}
