<!-- Deffiniert den Aufbau des Headers auf der home.php Webseiten, abhängig ob der Benutezr anmeldet ist, oder nicht  -->
<?php session_start();?>
<html>
<meta charset="utf-8" />
<head>
  <link rel="stylesheet" href="stylesheets/modal.css" />
  <link rel="stylesheet" href="stylesheets/header.css" />
</head>
<body>
  <div class="containerHeader">
    <div class="row">
      <a href="home.php"><img src="./img/logo.png"></a>
    <div class="center">
      <?php
          // Prüfen, ob Benutzer angemeldet ist
          // Falls ja, bekommt er folgende Punkte angezeigt
        if(isset($_SESSION["id"])){
           echo"<a class='active nav-link' href='request.php'>Hardware Ausleihen</a>";
           echo"<a class='inhalt nav-link' href='includes/logout.inc.php'>Logout</a>";
        }else { // Wenn nicht Anmeldung und Registrierung anzeigen
            echo"<a class='active nav-link' href='home.php'>Home</a>";
            #echo"<a class='inhalt' href='signup.php'>Sign Up</a>";
            echo"<a class='inhalt nav-link' href='#login'>Login</a>";
        }
      ?>
    </div>
  </div>

<!-- Modals -->
<!-- Modal zum Einloggen mit bestehenden Daten -->
  <div id="login" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Login</h2>
      <div class="containerModal">
        <form  class="container" action="includes/login.inc.php" method="post">
          <input class='input' required type="text" name="email" placeholder="Email....">
          <input class='input' required type="password" name="pwd" placeholder="Password....">
          <button class="subButton register" type="submit" name="submit">Login</button>
        </form>
        <a href="#register" class='notd '><button class="subButton" type="submit" name="register">Jetzt Registrieren</button></a>
      </div>
    </div>
  </div>


<!-- Modal, um einen neuen Benutzer anzulegen -->
  <div id="register" class="overlay">
    <div class="popup">
      <div class="center"><br>
        <a class="close" href="#">&times;</a>
        <img src="img/logo.png" alt="Logo" style="width:30%" class="img-container">
      </div>
      <h2>Registrieren</h2>
      <div class="containerModal">
        <form class="container" action="includes/signup.inc.php" method="post">
          <input class='input' type="text" required name="firstname" placeholder="Vorname...">
          <input class='input' type="text" required name="lastname" placeholder="Nachname....">
          <input class='input' type="text" required name="email" placeholder="Email....">
          <input class='input' type="password" required name="pwd" placeholder="Password....">
          <input class='input' type="password" required name="pwdrepeat" placeholder="Wiederhole Passwort....">
          <div class="agbLogin">
            <input id='legalmumbojumbo' class='agbcheck' type='checkbox' required>
            <label for="legalmumbojumbo">Ich bestätige, dass ich die <a href="https://agb.walter-rhoen-schule.de/" target="_blank" rel="noopener noreferrer">AGB</a> und <a href="https://nutzungsbedingungen.walter-rhoen-schule.de/" target="_blank" rel="noopener noreferrer">Nutzungsbedingungen</a> gelesen und zur Kenntnis genommen habe</label>
          </div>
          <button class="subButton" type="submit" name="submit">Registrieren</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
