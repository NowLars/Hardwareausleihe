<!-- Deffiniert die Webseite, welche zur Anmeldung geöffnet wird, sollte der erste Anmeldeversuch gescheitert sein -->
<?php
include_once 'header.php';


?>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="stylesheets\login.css">
</head>
<body>
  <div class="container">
    <div>
      <h2>Melden Sie sich an:</h2>
      <button onclick="document.getElementById('id02').style.display='block'">Login</button>
    </div>
    <!-- Öffnet über "Login" ein neues Modal, über welches eine Anmeldung mit Benutzerdaten möglich ist -->
    <div id="id02" class="modal">
      <div class="modal-content animate-zoom" style="max-width:600px">
        <div><br>
          <span onclick="document.getElementById('id02').style.display='none'" class="exit" title="Abbrechen">&times;</span>
          <img src="./img/logo.png" alt="Avatar" style="width:30%" class="img-container">
        </div>
        <!-- Erstellt ein Form, zur Eingabe und Abgelich der notwenigen Daten -->
        <form class="containerModal" action="includes/login.inc.php" method="post">
          <input class="input" type="text" name="email" placeholder="Firstname/Email....">
          <input class="input" type="password" name="pwd" placeholder="Password....">
          <button class="submitButton" type="submit" name="submit">Login</button>
        </form>
        </br>

      </div>
    </div>
  </div>
<?php

    if(isset($_GET["error"])){
        if($_GET["error"] == "emptyinput"){
        echo "<p>Fill in all fields!</p>";
        }else if($_GET["error"] == "wronglogin"){
            echo "<p>Incorrect Login information</p>";
        }
      }
?>
  </div>
 </div>
</body>

<?php
include_once 'footer.php'
?>
