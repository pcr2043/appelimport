<?php
namespace App;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

class Contact extends Model
{
  protected $guarded = ['id'];
  protected $fillable = [];
  public $timestamps = false;

  public static function parseCSV($csvFile)
  {
    // seperate data line by line 
    $lines = explode(PHP_EOL, $csvFile);

    //remove the header from the cvs file
    $lines = array_slice($lines, 3);

    //convert into Contacts array
    $lines =  array_map(function ($line) {
      $contact =  explode(';', $line);

      if (count($contact) == 8) {
        $dateTimeCall = implode('-', array_reverse(explode('/', $contact[3])));
        // check hours minutes
        if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $contact[0]))
          $dateTimeCall = $dateTimeCall . ' ' .  $contact[4];
        else
          $dateTimeCall = $dateTimeCall . ' 00:00:00';

        // some dates was not working so i add the validation
        if (Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeCall) !== false)
          $dateTimeCall = date("Y-m-d H:i:s");


        $contact =  [
          'compte_facture' => $contact[0],
          'numero_facture' => $contact[1],
          'numero_abonnement' => $contact[2],
          'date' =>  $contact[3],
          'heure' => $contact[4],
          'duree' =>  $contact[5],
          'dureeV2' =>  $contact[6],
          'type' => utf8_encode($contact[7])
        ];

        if ($contact != null)
          return $contact;
      }
    }, $lines);



    return $lines;
  }

  public function  scopeTotalCallTimeSince($query, $date)
  {
    return $query->select(Capsule::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(duree))) as total'))
      ->whereRaw("DATE(STR_TO_DATE(contacts.date,'%d/%m/%Y')) > DATE(STR_TO_DATE('15/02/2012','%d/%m/%Y'))")
      ->whereRaw("type like'%appel%'")->get();
  }

  public function  scopeTotalSms($query)
  {
    return $query->select(Capsule::raw('COUNT(type) as sms'))
      ->whereRaw("type like'%sms%'")->get();
  }

  public function  scopeTopCallsAfterWork($query)
  {
    return $query->select(Capsule::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(duree))) as duration'), 'numero_abonnement')
      ->whereRaw("heure > '18:00' and heure < '8:00'")
      ->whereRaw("type like '%appel%'")
      ->groupBy('numero_abonnement')
      ->orderBy('duration', 'desc')->take(10)->get();
  }

  public static function Stats()
  {
    return [
      'result' => true,
      'totalCallsSince' => Contact::TotalCallTimeSince('2012-02-12'),
      'totalSMS' => Contact::TotalSms(),
      'topCallsAfterWork' => Contact::TopCallsAfterWork()
    ];
  }
}
