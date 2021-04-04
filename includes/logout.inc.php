<?php

// Funktion welche beim Logout ausgeführt wird, um den Benutzer zurück zur Startseite zu leiten 
session_start();
session_unset();
session_destroy();

header("location: ../home.php");
exit();
