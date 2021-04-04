<?php
  // Daten für Datenbankverbindung
  $servername = 'server6.febas.net';
  $dbname = 'schule_db';
  $username = 'schule_db';
  $passwort = 'ProjketFES123';

  // Datenbankverbindung aufbauen
  #$connect = new mysqli($servername, $username, $passwort, $dbname);
  $connect = mysqli_connect($servername, $username, $passwort, $dbname);
  if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>
