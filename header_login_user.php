<!-- Beschreibt den Header, welcher den Benutzern auf den User-Webseiten (/X.php) angezeigt wird -->
<?php
session_start ();

// Prüft, ob eine Sitzung vorhanden ist, falls nicht, bekommt der Benutzer die Möglichkeit zur Anmeldung
if (!isset ($_SESSION["id"]  ))  {
  header ("Location: login.php");
}
?>


<html>
<meta charset="utf-8" />
<head>
  <link rel="stylesheet" href="stylesheets/header.css" />
</head>
<body>
  <div class="containerHeader">
    <div class="row">
    <a href="home.php"><img src="img/logo.png" alt="Walter-Rhon-Schule"></a>
    <div class="center">
      <?php
      /* Zeigt unterschiedliche Verknüpfungen im Header an, je nachdem ob der Nutzer angemeldet ist oder nicht */
      /* Wenn er als Adminsitaror angemeldet ist, sieht er folgende Punkte */
        if($_SESSION["role"] == 1){
           echo"<a class='active nav-link' href='admin/hardwaremanagement.php'>Hardware</a>";
           echo"<a class='nav-link' href='admin/usermanagement.php'>User anlegen</a>";
           echo"<a class='nav-link' href='admin/dashboard.php'>Dashboard</a>";
           echo"<a class='nav-link' href='profile.php'>Profile</a>";
           echo"<a class='nav-link' href='includes/logout.inc.php'>Logout</a>";
      /* Wenn er als Benutzer  angemeldet ist, dann sieht er diese folgenden Punkte */
        }else if ($_SESSION["role"] == 0) {
            echo"<a class='active nav-link' href='request.php'>Hardware Ausleihen</a>";
            echo"<a class='active nav-link' href='profile.php'>Profil</a>";
            echo"<a class='nav-link' href='includes/logout.inc.php'>Logout</a>";
        }
      ?>
    </div>
  </div>
</div>
</body>
</html>
