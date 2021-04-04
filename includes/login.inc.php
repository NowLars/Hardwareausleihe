<?php
// Funktion welche beim Login ausgeführt wird, um die Anmeldung des Benutzers zu prüfen, und diesen je nach Ausgang weiterzuleiten 
    if(isset($_POST["submit"])){

        $email = $_POST["email"];
        $pwd = $_POST["pwd"];

        require_once 'dbh.inc.php';
        require_once 'function.inc.php';

        if(emptyInputLogin($email, $pwd)){
            header("location: ../login.php?error=emptyinput");
            exit();
        }

        loginUser($connect, $email, $pwd );
    }
    else{
        header("location: ../login.php");
        exit();
    }
