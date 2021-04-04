<!-- Deffiniert den Aufbau des Footers mit allen seinen Bestandteilen -->
<!DOCTYPE html>
<html>
   <head>
      <link rel="stylesheet" href="stylesheets/footer.css">                            <!-- Definiert das Stylesheet für die PHP Seite -->
   </head>
   <body>
      <div class="containerFooter">                                                     <!-- Erstellt das Haupt DIV, in welchem alle Elemente erstellt werden -->
      <hr>                                                                              <!-- Hinzufügen einer Trennlinie, um den Übergang zum Inhalt zu verdeutlichen -->
        <div class="row">                                                               <!-- Erstellen eines weiteren DIVs, um die Elemente Nebeneinader zu positionieren -->
          <div class="about">                                                           <!-- Erstellen des ersten Elementes, in welchem ein kurzer Text über die Funktion der Webseite angegeben ist -->
            <h6>Über Uns</h6>
              <p><i>Walter-Rhoen-Schule </i>wir helfen Ihnen bei Ihrer Ausleihe. <br> Ausleihen sind online zu jeder Zeit Möglich.<br> Probieren Sie unseren Service jetzt aus. </p>
          </div>
          <div class="categorie">                                                       <!-- Erstellen des zweiten Elementes, rechtsbündig zum vorherigen, mit Links zu Webseitenspezifschen Funktionen -->
            <h6>Webseiten</h6>
              <ul class="footer-links">                                                 <!-- Anlegen einer Liste, welche die Links beinhaltet-->
                <li><a href="home.php">Startseite</a></li>
                <li><a href="https://www.walter-rhoen-schule.de">Schulwebseite</a></li>
              </ul>
          </div>
          <div class="categorie">                                                       <!-- Erstellen des dritten Elementes, rechtsbündig zum vorherigen, mit Links zu Benutzerspezifischen Funktionen -->
            <h6>Verwaltung</h6>
              <ul class="footer-links">                                                 <!-- Anlegen einer Liste, welche die Links beinhaltet-->
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign-Up</a></li>
              </ul>
          </div>
        </div>
      </div>
  </body>
</html>
