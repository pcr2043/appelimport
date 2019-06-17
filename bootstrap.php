<?php
require "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Contact;
use Carbon\Carbon;

$capsule = new Capsule;

$capsule->addConnection([
  'driver'    => 'mysql',
  'host'      => 'localhost',
  'database'  => 'appelsimport',
  'username'  => 'root',
  'password'  => '',
  'charset'   => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

// check if schema exists
if (Capsule::schema()->hasTable('contacts') === false) {

  Capsule::schema()->create('contacts', function ($table) {
    $table->increments('id');
    $table->string('compte_facture')->nullable();
    $table->string('numero_facture')->nullable();
    $table->string('numero_abonnement')->nullable();
    $table->string('date')->nullable();
    $table->string('heure')->nullable();
    $table->string('duree')->nullable();
    $table->string('dureeV2')->nullable();
    $table->string('type')->nullable();
  });
}
