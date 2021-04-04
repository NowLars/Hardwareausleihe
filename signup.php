<!-- Deffiniert die Webseite, welche zur Registrierung geöffnet wird, sollte diese über den Fiiter stattfinden -->
<?php
include_once 'header.php';

?>
<html>
  <head>
    <link rel="stylesheet" href="stylesheets/login.css?v=<?php echo time(); ?>">
  </head>
  <body>
    <div class="container">
      <div>
        <h2>Registrierung:</h2>
  <!-- Erstellt einen Button, über welchen sich das Modal öffnen lääst -->
        <button onclick="document.getElementById('id03').style.display='block'">Registrieren</button>
      </div>
  <!-- Erstellt das Modal, um sich auf der Webseite neu zu registieren -->
      <div id="id03" class="modal">
        <div class="modal-content animate-zoom" style="max-width:600px">
          <div><br>
            <span onclick="document.getElementById('id03').style.display='none'" class="exit" title="Abbrechen">&times;</span>
            <img src="./img/logo.png" alt="Avatar" style="width:30%" class="centerImg">
          </div>
  <!-- Deffiniert den Inhalt des sich öffnenenden Modals -->
    <form  class="containerModal" action="includes/signup.inc.php" method="post">
        <input class='input' type="text" name="firstname" placeholder="first name">
        <input class='input' type="text" name="lastname" placeholder="last name....">
        <input class='input' type="text" name="email" placeholder="Email....">
        <input class='input' type="password" name="pwd" placeholder="Password....">
        <input class='input' type="password" name="pwdrepeat" placeholder="Repeat password....">
        <button class="submitButton" type="submit" name="submit">Sign Up</button>
    </form>
      <!-- Error Meassages, falls eine Eingabe etc. falsch ist-->
      <?php

          if(isset($_GET["error"])){
              if($_GET["error"] == "emptyinput"){
                 echo "<p>Fill in all fields!</p>";
              }else if($_GET["error"] == "invalidEmail"){
                  echo "<p>Fill in a correct Mail!</p>";
              }else if($_GET["error"] == "pwddontmatch"){
                  echo "<p>The given passwords doesnt match!</p>";
              }else if($_GET["error"] == "userexists"){
                  echo "<p>User already exists!</p>";
              }else if($_GET["error"] == "stmtfailed1"){
                  echo "<p>Something went wrong, try again!</p>";
              }
          }
        ?>
      </div>
    </div>
  </div>
</body>

<?php
    include_once 'footer.php'
?>
