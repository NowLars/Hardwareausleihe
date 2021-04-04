<?php
// Funktion welche beim Erstellen eines neuen Benutezrs durchgeführt wird, auch hier werden bestimmte Abhängigkeiten geprüft 
    if(isset($_POST["submit"])){
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];
        $pwdrepeat = $_POST["pwdrepeat"];

        require_once 'dbh.inc.php';
        require_once 'function.inc.php';

        if(emptyInputSignup($firstname, $lastname, $email, $pwd, $pwdrepeat) !== false){
            header("location: ../signup.php?error=emptyinput");
            exit();
        }
        if(invalidEmail($email) !== false){
            header("location: ../signup.php?error=invalidemail");
            exit();
        }
        if(pwdMatch($pwd, $pwdrepeat) !== false){
           header("location: ../signup.php?error=pwddontmatch");
           exit();
        }
        if(userExist($connect, $firstname, $lastname, $email) !== false){
            header("location: ../signup.php?error=userexists");
            exit();
        }

        createUser($connect, $firstname, $lastname, $email, $pwd);

    }
    else{
        header("location: ../signup.php");
        exit();
    }
