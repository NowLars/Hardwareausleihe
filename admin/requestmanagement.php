<!-- Websiete, auf der eine Übersiht über vorhanden sowie bearbeitete Anfragen durch die Schüler angezeigt wird -->
<?php
  require('../includes/function.inc.php');
  require('../header_login_admin.php');
  require('../database.php');

//Fehler abfangen und Fehlermeldung zur Anzeige im Modal erzeugen
  if(isset($_GET["error"])){
    if($_GET["error"] == "emptyinput"){
      $event = "Bitte alle Felder ausfüllen";
    }elseif($_GET["error"] == "none"){
      $event = "Hardware erfolgreich hinzugefügt";
    }elseif($_GET["error"] == "enone"){
      $event = "Hardware erfolgreich aktuallisiert";
    }else if($_GET["error"] == "stmtfailed"){
      $event = "INTERNAL SERVER ERROR";
    }else if($_GET["error"] == "doesntexist"){
      $event = "Dieses Gerät existiert nicht mehr";
    }else if($_GET["error"] == "alreadyexist"){
      $event = "Dieser Eintrag existiert bereits";
    }

  }


  // Delete Anweisung mit ID um Datenbankeintrag eindeutig zu identifizieren
  // Wird ausgeführt, sobald der Delete-Button gedrückt wird
  if (isset($_POST["deny"])) {
    // Ausgewählte Einträge holen (einzeln oder mehrfach)
    if (isset($_POST["auswahl"])) {
      // Löschfunktion aufrufen
      if (denyRequest($connect, $_POST["auswahl"], $_SESSION['id'])) {
          //Modal öffnen, um einen Erfolgreichen Abschluss zu signalisieren
        header('location: ' . $_SERVER['PHP_SELF'] . '#success');
        die();
      }//Wenn der Löschvorgang fehlschlägt, Umleitung auf Fehlermeldung
      else{
        header('location: ' . $_SERVER['PHP_SELF'] . '#error');
        die();
      }
    }else{
    header('location: ' . $_SERVER['PHP_SELF'] . '#');
    }
  }

?>


<html>

<head>
    <title>Anfragenmanagement</title>
    <link rel="stylesheet" href="../stylesheets/modal.css" />
  </head>
  <body>
    <div class="container">
      <h2>Anfragenmanagement: </h2>
      <div class="open-request-wrapper">
        <h3>Offene Anfragen:</h3>
          <table class='table'>
            <tr>
              <th>Anfragesteller</th>
              <th>Leihdatum</th>
              <th>Rückgabedatum</th>
              <th>Grund</th>
              <th>Zeitpunkt</th>
              <th></th>
              <th></th>
            </tr>


<?php
  // Request-Tabelle mit dem gesamten Offenen Anfragen aus der Datenbank erstellen
  $query = "SELECT r.id, r.timestamp, r.lend_out_date, r.return_date, r.reason
            , f.first_name AS requester_first_name
            , l.last_name AS requester_last_name
            FROM requests AS r
            LEFT JOIN user AS f ON f.id = r.requester
            LEFT JOIN user AS l ON l.id = r.requester
            WHERE request_status IS NULL
            ORDER BY r.timestamp";


    $result = mysqli_query($connect, $query);


  # looping durch jedes Ergebnis
    while($dsatz = mysqli_fetch_assoc($result)){
        $sinceWhen = date_diff(getNow(),date_create($dsatz['timestamp']));
        $sinceWhen = ($sinceWhen->format('vor %h Stunden'));
        $id = $dsatz['id'];
        echo "<tr>" .
                "<td>$dsatz[requester_first_name]" . " " . "$dsatz[requester_last_name]</td>
                <td>$dsatz[lend_out_date]</td>
                <td>$dsatz[return_date]</td>
                <td>$dsatz[reason]</td>
                <td>$sinceWhen</td>" .

    // Akzeptieren-Schaltfläche für jeden Eintrag hinzufügen
        "<td style='width:90px; margin-left:5px;'>
            <form id='accept-request' method='post'>
                <input type='hidden' name='auswahl' value=$id>
                <input type='submit' value='Annehmen' name='accept' class='accept-button'>
            </form>
        </td>" .

    //Ablehnen-Schaltfläche für jeden Eintrag hinzufügen
        "<td style='width:90px;'>
            <form id='deny-request' method='post'>
                <input type='hidden' name='auswahl' value=$id>
                <input type='submit' value='Ablehnen' name='deny' class='delete-button'>
            </form>
        </td>" .
        "</tr>";

    }

?>

    <!-- Tabelle schließen -->
    <table>
    </br>
  <!-- open request wrapper schließen-->
  </div>

<!-- Request-Tabelle mit dem gesamten bearbeiteten Anfragen aus der Datenbank erstellen -->
  <div class="all-requests-wrapper">
    <h3>Bearbeitete Anfragen:</h3>
    <table class='table'>
        <tr>
            <th>Anfragesteller</th>
            <th>Hardware</th>
            <th>Leihdatum</th>
            <th>Rückgabedatum</th>
            <th>Grund</th>
            <th>Bearbeiter</th>
            <th>Anfragestatus</th>
            <th>Zeitpunkt</th>
        </tr>
<?php
    $query = "SELECT r.timestamp, r.lend_out_date, r.return_date, r.reason, r.request_status
                , f.first_name AS requester_first_name
                , l.last_name AS requester_last_name
                , x.first_name AS processor_first_name
                , y.last_name AS processor_last_name
                , m.name AS manufacturer
                , mo.name AS model
                FROM requests AS r
                LEFT JOIN user AS f ON f.id = r.requester
                LEFT JOIN user AS l ON l.id = r.requester
                LEFT JOIN user AS x ON x.id = r.processor
                LEFT JOIN user AS y ON y.id = r.processor
                LEFT JOIN hardware AS h ON h.id = r.hardware_id
                LEFT JOIN device AS d ON d.id = h.device
                LEFT JOIN manufacturer AS m ON m.id = d.manufacturer
                LEFT JOIN model AS mo ON mo.id = d.model
                WHERE request_status IS NOT NULL
                ORDER BY r.timestamp";

    $result = mysqli_query($connect, $query);

    # looping durch jedes Ergebnis
    while($dsatz = mysqli_fetch_assoc($result)){
        $sinceWhen = date_diff(getNow(),date_create($dsatz['timestamp']));
        $sinceWhen = ($sinceWhen->format('vor %h Stunden'));
        $status = $dsatz['request_status'];

        if ($status == 1){
            $status = "Akzeptiert";
            $color = "style='background-color: #1588004a;'";
        }else{
            $status = "Abgelehnt";
            $color = "style='background-color: #ffd2d2;'";
        }
        echo "<tr>" .
                "<td $color>$dsatz[requester_first_name]" . " " . "$dsatz[requester_last_name]</td>
                <td $color>$dsatz[manufacturer]" . " " . "$dsatz[model]</td>
                <td $color>$dsatz[lend_out_date]</td>
                <td $color>$dsatz[return_date]</td>
                <td $color>$dsatz[reason]</td>
                <td $color>$dsatz[processor_first_name]" . " " . "$dsatz[processor_last_name]</td>
                <td $color>$status</td>
                <td $color>$sinceWhen</td>" .

            "</tr>";
    }

?>
  </div><!-- allrequests wrapper schlließen-->


  <!--Modals -->
<!-- Modal zur Anzeige der erfolgreichen Duschführung einer Operation  -->
  <div id="success" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" style="color:green;" href="#">&#935;</a>
      </div>
      <h2 class="status-icon" style="color:green; font-size:80px;">&check;</h2>
      <h2 style="color:green; text-align:center;">Erfolgreich</h2>
      <div class="content">
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Anfrage erfolgreich abgelehnt";}?></div>
        <a href="#"><button type='button' name="accept" class="accept-button">Super!</button></a>
      </div>
    </div>
  </div>

<!-- Modal zur Anzeige eines Fehlers bei der Duschführung einer Operation  -->
  <div id="error" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" style="color:red;" href="#">&#935;</a>
      </div>
      <h2 class="status-icon" style="color:red; font-size:80px;">&cross;</h2>
      <h2 style="color:red; text-align:center;">Fehler!</h2>
      <div class="content">
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Fehler beim ablehnen der Anfrafe";}?> </div>
        <a href="#"><button type='button' name="deny" class="deny-button" >Shit!</button></a>
      </div>
    </div>
  </div>
 <!-- Wrapper schließend-->
 </div>
</body>
</html>
