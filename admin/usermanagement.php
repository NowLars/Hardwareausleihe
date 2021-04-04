<!-- Diese Webseite dient zur zentralen Verwaltung aller Benutezr, welche die Webseite benutzen,.
Hier können neue Benutzer angelegt weren und bestehende bearbeitet sowie gelöscht werden -->
<?php

  require('../includes/function.inc.php');
  require('../header_login_admin.php');
  require('../database.php');


  //Fehler abfangen und Fehlermeldung zur Anzeige im Modal erzeugen
  if(isset($_GET["error"])){
    if($_GET["error"] == "emptyinput"){
      $event = "Bitte alle Felder ausfüllen";
    }else if($_GET["error"] == "invalidEmail"){
      $event = "Die Emailadresse ist falsch";
    }else if($_GET["error"] == "pwddontmatch"){
      $event = "Die Passwörter stimmen nicht überein";
    }else if($_GET["error"] == "userexists"){
      $event = "Der Benutzer existiert bereits";
    }else if($_GET["error"] == "doesntexist"){
      $event = "Der Benutzer existiert nicht mehr";
    }else if($_GET["error"] == "stmtfailed1"){
      $event = "INTERNAL SERVER ERROR";
    }else if($_GET["error"] == "enone"){
      $event = "Benutzer erfolgreich aktuallisiert";
    }else if($_GET["error"] == "none"){
      $event = "Benutzer erfolgreich hinzugefügt";
    }
  }


  // Delete Anweisung mit ID um Datenbankeintrag eindeutig zu identifizieren
  // Wird ausgeführt, sobald der Delete-Button gedrückt wird
  if (isset($_POST["del"])) {
    // Ausgewählte Einträge holen (einzeln oder mehrfach)
    if (isset($_POST["auswahl"])) {
      // Löschfunktion aufrufen
      if (delUser($connect, $_POST["auswahl"])) {
        //Modal öffnen, um einen Erfolgreichen Abschluss zu signalisieren
        header('location: ' . $_SERVER['PHP_SELF'] . '#success');
        die();
      }//Wenn der Löschvorgang fehlschlägt, Umleitung auf Fehlermeldung
      else{
        header('location: ' . $_SERVER['PHP_SELF'] . '#error');
        die();
      }
    }else{
    #header('location: ' . $_SERVER['PHP_SELF'] . '#');
    }
  }


?>


  <html>
  <head>
      <title>Usermanagement</title>
    <link rel="stylesheet" href="../stylesheets/modal.css" />
        <link rel="stylesheet" href="../stylesheets/usermanagement.css" />
  </head>
  <body>
    <div class="container">
      <h2>Usermanagement:</h2>


  <!-- Feld mit Buttons über der Tabelle -->
      <div class='buttons'>
      <!-- hinzufügen und Löschen von Benutzer -->
        <a href='#adduser'><button class='add'>&#43;</button></a>
        <a href='#confirm'><button class='delete'>Markierte Einträge löschen</button></a>
      </div>
      <!-- Suchfunktion, um nach bestimmten Benutzern zu suchen -->
      <div class="search">
        <form class='searchcontainer' action='' method='post'>
          <input type='text' name='searchstring' placeholder='Suche....'>
          <button type='submit' name='search'>Suchen</button>
        </form>
      </div>

<?php
// Benutzer-Tabelle mit dem gesamten Benutzern aus der Datenbank erstellen
  echo "<table class='table'>
    <tr>
      <th>Markierung</th>
      <th>Vorname</th>
      <th>Nachname</th>
      <th>E-Mail</th>
      <th>Rolle</th>
      <th></th>
      <th></th>
    </tr>";


    $query = "SELECT `first_name`, `last_name`, `email`, `id`, `role` FROM user";
    // Abfrage ändern, wenn Suchfunktion verwendet wird
    if (isset($_POST["search"])){
        $searchstring = mysqli_real_escape_string($connect, $_POST['searchstring']);
        $query = ($query . " WHERE CONCAT (`first_name`, `last_name`, `email`, `role`) LIKE '%$searchstring%' ");
    }

    $userList = mysqli_query($connect, $query);

    while($dsatz = mysqli_fetch_assoc($userList)){
      $id = $dsatz["id"];
      if ($dsatz["role"] == 0){
        $role = "Schüler";
      }elseif ($dsatz["role"] == 1){
        $role = "Lehrer";
      }

    // je Eintrag eine neue Tabellenzeile ausgeben
    echo "<tr>
            <td><input type='checkbox' form='users' name='auswahl[]' value=$id></td>
            <td>$dsatz[first_name]</td>
            <td>$dsatz[last_name]</td>
            <td>$dsatz[email]</td>
            <td>$role</td>
            <td style='width:90px; margin-left:5px;'><form id='edit-entry' method='post'>
                    <input type='hidden' name='auswahl' value=$id>
                    <input type='submit' value='Editieren' name='edit' class='edit-button'>
                  </form>
            </td>" .

          //Löschtaste für jeden Eintrag hinzufügen
            "<td style='width:90px;'><form id='delete-entry' method='post'>
                    <input type='hidden' name='auswahl[]' value=$id>
                    <input type='submit' value='Löschen' name='del' class='delete-button'>
                  </form>
            </td>" .
        "</tr>";
    }
  // Tabelle abschließe
  echo"</table>";
  #echo "</form>";


  echo"<form id='users' method='post'>";
?>

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
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Erfolgreich Benutzer gelöscht";}?></div>
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
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Fehler beim Löschen von Benutzer(n)";}?> </div>
        <a href="#"><button type='button' name="deny" class="deny-button" >Shit!</button></a>
      </div>
    </div>
  </div>



<!-- Modal um neue Benutzer hinzuzufügen -->
  <div id="adduser" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="../img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Benutzer erstellen</h2>
      <div class="content">
        <form class="container" action="../includes/user.inc.php" method="post">
          <input class='input' type="text" required name="firstname" placeholder="Vorname...">
          <input class='input' type="text" required name="lastname" placeholder="Nachname....">
          <input class='input' type="text" required name="email" placeholder="Email....">
          <input class='input' type="password" required name="pwd" placeholder="Passwort....">
          <input class='input' type="password" required name="pwdrepeat" placeholder="Wiederhole Passwort....">
          <select class='input' type="number" required name="role">
            <option value="" disabled selected hidden>Rolle Auswählem ...</option>
            <option value="0">Schüler</option>
            <option value="1">Lehrer</option>
          </select>
          <button class="subButton" type="submit" name="submit">Erstellen</button>
        </form>
      </div>
    </div>
  </div>

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
        <input type='submit' form="users" value="Ja" name='del' class="accept-button" />
        <a href="#"><button type='button' name="deny" class="deny-button" >Nein</button></a>
      </div>
    </div>
  </div>


<?php
// Modal zum Bearbeiten vorhandener Tabelleneinträges
  if (isset($_POST["edit"])) {
    if (isset($_POST["auswahl"])) {
      $userid = $_POST["auswahl"];
      // Abrufen aller erforderlichen Daten zum Bearbeiten eines bestehenden Eintrags
      $query = "SELECT `first_name`, `last_name`, `email`, `id`, `role` FROM user WHERE id = ? ";

      $stmt = mysqli_stmt_init($connect);

      // Wenn Anweisung fehlschlägt, wurde der zu bearbeitende Benutzer bereits gelöscht wurde
      if(!mysqli_stmt_prepare($stmt, $query)){
        header('location: ' . $_SERVER['PHP_SELF'] . '#error');
        exit();
      }

      mysqli_stmt_bind_param($stmt, "i", $userid);
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
                <a class='close' href=$_SERVER[PHP_SELF]>&times;</a> <!-- Workaround to get the modal closed again withou the disabillity to reopen it again. -->
                <img src='../img/logo.png' alt='Logo' style='width:30%' class='img-container'>
              </div>

              <h2>Hardware editieren</h2>
              <div class='content'>
                <form action='../includes/user.inc.php' method='post'>
                  <input class='input' type='hidden' name='id' value=$editdsatz[id]>
                  <label>Vorname:</label>
                  <input class='input' type='text' required name='firstname' value=$editdsatz[first_name]>
                  <label>Nachname:</label>
                  <input class='input' type='text' required name='lastname' value=$editdsatz[last_name]>
                  <label>E-Mail:</label>
                  <input class='input' type='text' required name='email' value=$editdsatz[email]>
                  <label>Rolle:</label>
                  <select class='input' type='number' value=$editdsatz[role] required name='role'>
                    <option value='0'>Schüler</option>
                    <option value='1'>Lehrer</option>
                  </select>
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
<!-- Closing Wrapper -->
</div>
</body>
</html>
