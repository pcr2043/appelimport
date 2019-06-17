<?php
require('../bootstrap.php');
require('../src/App.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>

<body>

  <div class="container">
    <div class="columns">
      <div class="column is-full">
        <h1 class="is-size-3">APPEL IMPORT <button onclick="stats()" class="button is-primary  is-pulled-right is-small">RELOAD STATS</button></h1>
      </div>
    </div>

    <div class="columns">
      <div class="column is-one-fifth">
        <form onsubmit="return submitLogFile()">
          <div class="field">
            <label class="label">Log File</label>
            <div class="control">
              <input type="file" name="log">
            </div>
          </div>
          <div class="field is-grouped">
            <div class="control">
              <button type="submit" id="btn-import" class="button is-link is-small">START IMPORT</button>
            </div>
          </div>
        </form>
      </div>
      <div class="column is-three-fifths">
        <div class="field">
          <label class="label">Top 10 Calls Hors Service</label>
        </div>
        <div id="callsListUI" class="list is-hoverable">
        </div>
      </div>

      <div class="column is-one-fifth">
        <div class="field">
          <label class="label">Total SMS</label>
        </div>

        <div class="sms"></div>
        <hr>
        <div class="field">
          <label class="label">Total Calls Volume After 15/02/2012 </label>
        </div>
        <div class="calls-volume"></div>
      </div>
    </div>
  </div>


</body>


<style>
  .container {
    margin-top: 2rem;
  }
</style>

<script>
  function stats() {
    $.get('/', {
      'action': 'stats'
    }).done(function(data) {

      var data = JSON.parse(data);
      //empty call list UI element
      let callsListUI = $('#callsListUI')
      callsListUI.empty();

      data.topCallsAfterWork.forEach(function(call) {
        // create a tempalte
        let template = $('<a class="list-item"></a>');
        // set text to the template
        template.text('Abonné :' + call.numero_abonnement + ' --  volumne: ' + call.duration);
        // append element
        callsListUI.append(template)

      })

      //set total sms
      $('.sms').text(data.totalSMS[0]['sms'] + ' SMS')

      //set total volumne
      $('.calls-volume').text(data.totalCallsSince[0]['total'])


    })
  }



  function submitLogFile() {

    // Cree un varialle pour ler fom
    var form = $('form')[0];

    //set button loading
    $('#btn-import').text('PLEASE WAIT...')

    // Ajoutez l'Object FormData pour retenir les donnes du formularie
    var data = new FormData(form);

    // Creation de la requete Ajax.
    $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
      url: "/index.php",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      timeout: 600000,
      success: function(data) {

        stats();
        $('#btn-import').text('IMPORT AGAIN')
      },
      error: function(e) {
        $('#btn-import').text('IMPORT AGAIN')
      }
    });

    return false;
  }

  //call statts to load data on th dashboard
  stats();
</script>

</html>