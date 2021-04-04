<!-- Diese Webseite erstellt die Hauptfunktion, in welcher es für die Schüler möglich ist,
eine Ausleihe von Hardware zu beantragen -->
<?php
    require('header_login_user.php');
    require('database.php');

    //Fehler abfangen und Fehlermeldung zur Anzeige im Modal erzeugen
    if(isset($_GET["error"])){
        if($_GET["error"] == "emptyinput"){
            $event = "Bitte alle Felder ausfüllen";
        }elseif($_GET["error"] == "none"){
            $event = "Antrag erfolgreich eingereicht";
        }elseif($_GET["error"] == "wrongdate"){
            $event = "Das Datum ist nicht korrekt eingetragen worden";
        }else if($_GET["error"] == "stmtfailed"){
            $event = "INTERNAL SERVER ERROR";
        }else if($_GET["error"] == "tomanyrequests"){
            $event = "Du hast bereits mehrere offene Anfragen";
        }
    }


?>


<html>
  <head>
    <title>Anfrage Erstellen</title>
    <link rel="stylesheet" href="./stylesheets/modal.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="stylesheets/request.css" />
  </head>
  <body>
  <!-- Erstellt ein Formular, über welches eine Ausleihe beantragt werden kann -->
    <div class="wrapper-request">
      <h2>Leihantrag</h2>
      <form action='includes/request.inc.php' method='post'>
        <label class="requestform">Vorname</label>
          <input class="requestform" type="text" disabled required <?php echo"value=$_SESSION[first_name]"?>>
        <label class="requestform">Nachname</label>
          <input class="requestform" type="text" disabled required <?php echo"value=$_SESSION[last_name]"?>>
        <label class="requestform">Abholtermin</label>
          <input class="requestform" name="lend_out_date" type="datetime-local" required> <!-- Hier wird das Ausgabedatum der Hardware festgelegt -->
        <label class="requestform">Rückgabetermin</label>
          <input class="requestform" name="return_date" type="datetime-local" required> <!-- Und das Rückgabedatum, an welchem die Hardware wieder in Schul-Hand seien muss -->
        <label class="requestform">Beschreibung</label>
          <textarea class="requestform" name="reason" rows="4" cols="50" maxlength="150" required placeholder="Beschreibe hier kurz warum du ein Gerät ausleihen möchtest"></textarea> <!-- Für die Abgab ist des Weiteren eine kurze Begründung notwendig -->
        <!-- Erstellt zwei checkboxen, zum Akzeptieren der Nutzungsbedingungen und der pünktlichen Rückgabe-->
        <input id="accepttos" type="checkbox" required>
          <label for="accepttos">Ich habe die <a  href="https://www.Nutzungsbedingungen.walter-rhoen-schule.de/" target="_blank" rel="noopener noreferrer">Nutzungsbedingungen</a> zur Kenntnis genommen und werde im Schadensfall diesen unverzüglich melden</label>
        <br>
        <input id="accepttimeline" type="checkbox" required> <!-- -->
          <label for="accepttimeline">Ich werde mich an den vereinbarten Abgabetermin halten</label>
        <br><br>
        <button class="subButton" name="sub" type="submit">Anfrage absenden</button><!-- -->
    </form>
    <!-- Closing Wrapper -->
  </div>

<!-- Modals -->
<!-- Modal zur Anzeige der erfolgreichen Duschführung einer Operation  -->
  <div id="success" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" style="color:green;" href="#">&#935;</a>
      </div>
      <h2 class="status-icon" style="color:green; font-size:80px;">&check;</h2>
      <h2 style="color:green; text-align:center;">Erfolgreich</h2>
      <div class="content">
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Antrag erfolgreich eingereicht";}?></div>
        <a href="#"><button type='subButton' name="accept" class="accept-button">Super!</button></a>
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
        <div class="notice-text"><?php if (isset($event)){echo($event);}else{echo"Fehler beim Einreichen deines Antrags";}?> </div>
        <a href="#"><button type='subButton' name="deny" class="deny-button" >Shit!</button></a>
      </div>
    </div>
  </div>
</body>


<?php
include_once 'footer.php';
?>
