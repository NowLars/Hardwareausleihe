<?php
// Funktion welche beim ändern des Passworts über die Profilwebseite ausgeführt wird und je nach Eingabe das Passwort ändert 
if(isset($_POST["submit"])){
        $pwd = $_POST["pwd"];
        $pwdrepeat = $_POST["pwdrepeat"];

        require_once '../database.php';
        require_once 'function.inc.php';

        if(emptyInputProfil($pwd, $pwdrepeat) !== false){
            header("location: ../usermanagement.php?error=emptyinput");
            exit();
        }
        if(pwdMatch($pwd, $pwdrepeat) !== false){
            header("location: ../usermanagement.php?error=pwddontmatch");
            exit();
         }

         changePassword($connect, $pwd, $pwdrepeat);

    } else{
        header("location: ../usermanagement.php");
        exit();
    }
