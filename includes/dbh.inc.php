  <?php
  // Daten für Datenbankverbindung
  $servername = 'server6.febas.net';
  $dbname = 'schule_db';
  $username = 'schule_db';
  $passwort = 'ProjketFES123';

	// Datenbankverbindung aufbauen
	$connect = new mysqli($servername, $username, $passwort, $dbname);
	mysqli_query($connect, "SET NAMES 'utf8'");
?>
