<!-- Diese Webseite dient dazu, eine Übersicht über das gesamte Inventar der Schule zu bekommen und dieses zu verwalten,
des Weiteren besteht hier die Möglichkeit neue Geräte hinzuzufügen, zu bearbeiten und zu läschen -->
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
  if (isset($_POST["del"])) {
      // Ausgewählte Einträge holen (einzeln oder mehrfach)
    if (isset($_POST["auswahl"])) {
      // Löschfunktion aufrufen
      if (delItem($connect, $_POST["auswahl"])) {
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
    <link rel="stylesheet" href="../stylesheets/modal.css" />
    <title>Hardwaremanagement</title>
  </head>
  <body>
    <div class="container">
      <h2>Hardwaremanagement: </h2>
      <br>

        <div class='buttons'>
          <a href='#addhardware'><button class='add'>&#43;</button></a>
          <a href='#confirm'><button class='delete'>Markierte Einträge löschen</button></a>
        </div></br>

<?php
// Hardware-Tabelle mit dem gesamten Inventar aus der Datenbank erstellen
  echo "<form id='hardware' method='post'>";
    echo "<table class='table'>
      <tr>
        <th>Markierung</th>
        <th>Gerät</th>
        <th>Seriennummer</th>
        <th>Zugewiesen</th>
        <th>Zuletzt geändert</th>
  	    <th>Status</th>
        <th>Leihdatum</th>
        <th>Rückgabedatum</th>
  	    <th>Notizen</th>
        <th>Letzte Änderung</th>
        <th></th>
        <th></th>
      </tr>
    </form>";

// Alle Daten nach Namen sortiert in die Datenbank übertragen
  $abfrage = "SELECT h.id, h.status, h.serialnumber, h.last_edited, h.lend_out_date, h.return_date, h.notes
  , f.first_name AS assigned_user_first_name
  , l.last_name AS assigned_user_last_name
  , x.first_name AS last_edited_by_first_name
  , y.last_name AS last_edited_by_last_name
  , m.name AS manufacturer
  , mo.name AS model
  , s.name AS status_name

  FROM hardware AS h
  LEFT JOIN user AS f ON f.id = h.assigned_user
  LEFT JOIN user AS l ON l.id = h.assigned_user
  LEFT JOIN user AS x ON x.id = h.last_edited_by
  LEFT JOIN user AS y ON y.id = h.last_edited_by
  INNER JOIN device AS d ON d.id = h.device
  INNER JOIN manufacturer AS m ON m.id = d.manufacturer
  INNER JOIN model AS mo ON mo.id = d.model
  INNER JOIN status AS s ON s.id = h.status
  ORDER BY mo.name";


  $result = mysqli_query($connect, $abfrage);


//looping durch jedes Ergebnis
  while($dsatz = mysqli_fetch_assoc($result)){
    echo "<tr>";
    $id = $dsatz["id"];

// Füllen der Tabelle mit den Daten aus der Datenbank
  echo "<td><input form='hardware' type='checkbox' name='auswahl[]' value=$id></td>
      <td>$dsatz[manufacturer]" . " " . "$dsatz[model]</td>
      <td>$dsatz[serialnumber]</td>
      <td>$dsatz[assigned_user_first_name]" . " " . "$dsatz[assigned_user_last_name]</td>
      <td>$dsatz[last_edited_by_first_name]" . " " . "$dsatz[last_edited_by_last_name]</td>
      <td>$dsatz[status_name]</td>
      <td>$dsatz[lend_out_date]</td>
      <td>$dsatz[return_date]</td>
      <td>$dsatz[notes]</td>
      <td>$dsatz[last_edited]</td>" .

  // Bearbeiten-Schaltfläche für jeden Eintrag hinzufügen
      "<td style='width:90px; margin-left:5px;'><form id='edit-entry' method='post'>
              <input type='hidden' name='auswahl' value=$id>
              <input type='submit' value='Editieren' name='edit' class='edit-button'>
            </form>
      </td>" .

  // Löschtaste für jeden Eintrag hinzufügen
      "<td style='width:90px;'><form id='delete-entry' method='post'>
              <input type='hidden' name='auswahl[]' value=$id>
              <input type='submit' value='Löschen' name='del' class='delete-button'>
            </form>
      </td>" .
	"</tr>";

}

?>
  <!-- Tabelle schließen -->
  </table>
  </br>


  <!-- Formular zum Senden von angekreuzten Kästchen in der Hardware-Tabelle -->
  <form id='hardware' method='post'></form>

<!--Modals -->
<!-- Modal zum Bestätigen des Löschvorgangs -->
  <div id="confirm" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" style="color:red;" href="#">&#935;</a>
      </div>
        <h2 class="status-icon" style="color:#2196F3; font-size:80px;">&#63;</h2>
        <h2 style="color:#2196F3; text-align:center;">Bestätige</h2>
        <div class="content">
          <div class="notice-text">Möchtest du wirklich löschen?</div>
          <input style="margin-bottom: 15px;" type='submit' form="hardware" value="Ja" name='del' class="accept-button" />
          <a href="#"><button type='button' name="deny" class="deny-button" >Nein</button></a>
      </div>
    </div>
  </div>

<!-- Modal zur Anzeige der erfolgreichen Duschführung einer Operation  -->
  <div id="success" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" style="color:green;" href="#">&#935;</a>
      </div>
      <h2 class="status-icon" style="color:green; font-size:80px;">&check;</h2>
      <h2 style="color:green; text-align:center;">Erfolgreich</h2>
      <div class="content">
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Hardware erfolgreich gelöscht";}?></div>
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
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Fehler beim Löschen der Hardware";}?> </div>
        <a href="#"><button type='button' name="deny" class="deny-button" >Shit!</button></a>
      </div>
    </div>
  </div>


<!-- Modal um neue Hardwre hinzuzufügen -->
  <div id="addhardware" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="../img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Hardware hinzufügen</h2>
      <div class="content">
        <form class="inputdropdown" action="../includes/hardware.inc.php" method="post">
          <div class="wrapperinandadd">
            <select class='input' required name='manufacturer'>
              <option value="" disabled selected hidden>Hersteller Auswählem ...</option>
              <?php $result = mysqli_query($connect, "SELECT name,id FROM manufacturer order by name");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value=$row[id]>$row[name]</option>";
                }
              ?>
            </select>
            <a href='#addmanufacturer'><div class='add'>&#43;</div></a>
          </div>
          <div class="wrapperinandadd">
            <select class='input' required name='model'>
              <option value="" disabled selected hidden>Modell Auswählem ...</option>

              <?php $result = mysqli_query($connect, "SELECT name,id FROM model order by name");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value=$row[id]>$row[name]</option>";
                }
              ?>
            </select>
            <a href='#addmodel'><div class='add'>&#43;</div></a>
          </div>
          <input class='input' type="text" name="serialnumber" placeholder="Seriennummer ...">
          <div class="wrapperinandadd">
            <select class='input' required name='status'>
              <option value="" disabled selected hidden>Status setzen</option>

              <?php $result = mysqli_query($connect, "SELECT name,id FROM status order by name");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value=$row[id]>$row[name]</option>";
                }
              ?>
            </select>
            <a href='#addstatus'><div class='add'>&#43;</div></a>
          </div>
          <input class='input' type="text" name="notes" placeholder="Notizen ...">
          <button class="subButton" type="submit" name="sub">Erstellen</button>
        </form>
      </div>

    </div>
  </div>


<?php
  // Modal zum Bearbeiten vorhandener Tabelleneinträges
  if (isset($_POST["edit"])) {
    if (isset($_POST["auswahl"])) {
      $hardwareid = $_POST["auswahl"];
      // Abrufen aller erforderlichen Daten zum Bearbeiten eines bestehenden Eintrags
      $query = "SELECT h.*
      , m.name AS manufacturer
      , m.id AS manufacturer_id
      , mo.name AS model
      , mo.id AS model_id
      , s.name AS status_name

      FROM hardware AS h
      INNER JOIN device AS d ON d.id = h.device
      INNER JOIN manufacturer AS m ON m.id = d.manufacturer
      INNER JOIN model AS mo ON mo.id = d.model
      INNER JOIN status AS s ON s.id = h.status
      WHERE h.id = ? ";

      $stmt = mysqli_stmt_init($connect);

      // Wenn Anweisung fehlschlägt, wurde die zu bearbeitende Hardware bereits gelöscht
      if(!mysqli_stmt_prepare($stmt, $query)){
        header('location: ' . $_SERVER['PHP_SELF'] . '#error');
        exit();
      }

      mysqli_stmt_bind_param($stmt, "i", $hardwareid);
      if (mysqli_stmt_execute($stmt)){
        #echo($hardwareid);
        $resultData = mysqli_stmt_get_result($stmt);
        $editdsatz = mysqli_fetch_assoc($resultData);

        mysqli_stmt_close($stmt);
        // Gibt das Modal, mit den bereits vorhandenen Daten für das entsprechende Modal aus
        echo("
          <div id='edithardware' class='eoverlay'>
            <div class='popup'>
              <div class='center'><br>
                <a class='close' href=$_SERVER[PHP_SELF]>&#935;</a> <!-- Workaround to get the modal closed again withou the disabillity to reopen it again. -->
                <img src='../img/logo.png' alt='Logo' style='width:30%' class='img-container'>
              </div>

              <h2>Hardware editieren</h2>
              <div class='content'>
                <form action='../includes/hardware.inc.php' method='post'>
                  <input type='hidden' name='hardwareid' value=$editdsatz[id]>
                  <!-- MANUFACTURER FIELD -->
                  <label>Hersteller:</label>
                  <select class='input' required name='manufacturer'>
                    <option value=$editdsatz[manufacturer_id] selected> $editdsatz[manufacturer] </option>");

                    $result = mysqli_query($connect, 'SELECT name,id FROM manufacturer order by name');
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value=$row[id]>$row[name]</option>";
                      }

                  echo("</select>
                  <!-- MODEL FIELD -->
                  <label>Modell:</label>
                  <select class='input' required name='model'>
                    <option value=$editdsatz[model_id] selected> $editdsatz[model] </option>");

                    $result = mysqli_query($connect, 'SELECT name,id FROM model order by name');
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value=$row[id]>$row[name]</option>";
                      }

                  echo("</select>
                  <!-- SERIALNUMBER FIELD -->
                  <label>Seriennummer:</label>
                  <input class='input' type='text' name='serialnumber' value=$editdsatz[serialnumber]>
                  <!-- ASSIGNED USER FIELD -->
                      <!-- Not necessary -->
                  <!-- STATUS FIELD -->
                  <label>Status:</label>
                  <select class='input' required name='status'>
                    <option value=$editdsatz[status] selected>$editdsatz[status_name] </option>");

                    $result = mysqli_query($connect, 'SELECT name,id FROM status order by name');
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value=$row[id]>$row[name]</option>";
                      }

                  echo("</select>
                  <!-- NOTES FIELD -->
                  <label>Notizen:</label>
                  <input class='input' type='text' name='notes' value=$editdsatz[notes] >
                  <!-- SUBMIT BUTTON -->
                  <button class='subButton' type='submit' name='subedit'>Aktuallisieren</button>
                </form>
              </div>

            </div>
          </div>

        ");

      }
      else{
        header('location: ' . $_SERVER['PHP_SELF'] . '?error=doesntexist#error');
      }

    }

  }

?>
  </div> <!-- Closing Wrapper -->

<!-- Modal um einen neune Hersteller hinzuzufügen -->
  <div id="addmanufacturer" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="../img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Hersteller hinzufügen</h2>
      <div class="content">
        <form class="inputdropdown" action="../includes/hardware.inc.php" method="post">
          <input class='input' type="text" name="manufacturer" placeholder="Hersteller ...">
          <button class="subButton" type="submit" name="sub-manufacturer">Erstellen</button>
        </form>
      </div>

    </div>
  </div>

<!-- Modal um ein neues Modell hinzuzufügen -->
  <div id="addmodel" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="../img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Modell hinzufügen</h2>
      <div class="content">
        <form class="inputdropdown" action="../includes/hardware.inc.php" method="post">
          <input class='input' type="text" name="model" placeholder="Modell ...">
          <button class="subButton" type="submit" name="sub-model">Erstellen</button>
        </form>
      </div>
    </div>
  </div>


<!-- Modal um einen neuen Status hinzuzufügen -->
  <div id="addstatus" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="../img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Status hinzufügen</h2>
      <div class="content">
        <form class="inputdropdown" action="../includes/hardware.inc.php" method="post">
          <input class='input' type="text" name="status" placeholder="Status ...">
          <button class="subButton" type="submit" name="sub-status">Erstellen</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
