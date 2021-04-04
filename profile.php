<!-- Deffiniert die Webseite, über welche der Benutzer seine Daten einshene kann,
des Weiteren hat er hier die Möglichkeit sein Passwort zu ändern und seine Aktitvitäten einzusehen -->
<?php

    require('header_login_user.php');
    require('database.php');
    require('includes/function.inc.php');

?>

<html>
  <head>
    <title>Usermanagement</title>
    <link rel="stylesheet" href="stylesheets/modal.css" />
  </head>
  <body>
  <!-- Erstellt ein Element, in dem die Daten des aktuellen Benuters angezeigt werden -->
    <div class="container">
    <?php

    if(isset($_SESSION["role"]) AND $_SESSION["role"] == 1){
        echo"<img style='width: 15%' src='img/profile.svg' alt='Admin'>";
    }else
        echo"<img style='width: 15%' src='img/user_1.svg' alt='User'>";

    $name = $_SESSION["first_name"];
    $lastname = $_SESSION["last_name"];
    if($_SESSION['role'] == 1){
        $role = "Admin";
    }else {
        $role = "User";
    }


$id = $_SESSION["id"];
echo"</br></br>";
echo"<b>Nachname, Vorname:</b>";
echo"</br>";
echo"$lastname, $name ($role)";
echo"</br></br>";
echo"";

  /* Modal, über welches das Passwort des Benutzers bearbeitet werden kann */
  echo"<form action='profile.php' method='post'>";
      echo"<input type='password' name='pwd' placeholder='Password'> ";
      echo"<input type='password' name='pwdrepeat' placeholder='Password repeat'>";
      echo"<button type='submit' name='submit'>Ändern</button>";

      if(isset($_POST["submit"])){
          $pwd = $_POST["pwd"];
          $pwdrepeat = $_POST["pwdrepeat"];

          require_once 'database.php';

          if(emptyInputProfile($pwd, $pwdrepeat) !== false){
              header("location: profile.php?error=emptyinput");
              exit();
          }
          if(pwdMatch($pwd, $pwdrepeat) !== false){
              header("location: profile.php?error=pwddontmatch");
              exit();
           }
           changePassword($connect, $pwd, $pwdrepeat, $id);
      }

      if(isset($_GET["error"])){
          if($_GET["error"] == "none"){
          echo "<p>Password change successfull!</p>";
          }
      }
  echo"</br></br>";

// Aktitvitäten-Tabelle, in welcher alle dem Benutzer zugeordneten Aktitvitäten anzeiegt werdsen
#if($_SESSION["role"] == 0){
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
    </tr>";

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

//Looping durch jedes Ergebnis
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
      "</td>" .
      "</tr>";

  }
#}
?>
</div>
</body>
<?php
    require('footer.php')
?>
