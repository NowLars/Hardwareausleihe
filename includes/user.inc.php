<?php
// Funktion welche beim Erstellen eines neuen Benutezrs über das Usermanagement im Admin-Bereich durchgeführt wird, auch hier werden bestimmte Abhängigkeiten geprüft
if(isset($_POST["submit"])){
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $pwd = $_POST["pwd"];
    $pwdrepeat = $_POST["pwdrepeat"];


    require_once '../database.php';
    require_once '../includes/function.inc.php';

    if(emptyInputSignup($firstname, $lastname, $email, $pwd, $pwdrepeat) !== false){
        header("location: ../admin/usermanagement.php?error=emptyinput#error");
        exit();
    }
    if(invalidEmail($email) !== false){
        header("location: ../admin/usermanagement.php?error=invalidEmail#error");
        exit();
    }
    if(pwdMatch($pwd, $pwdrepeat) !== false){
        header("location: ../admin/usermanagement.php?error=pwddontmatch#error");
        exit();
    }
    if(userExist($connect, $firstname, $lastname, $email) !== false){
        header("location: ../admin/usermanagement.php?error=userexists#error");
        exit();
    }

    addUser($connect, $firstname, $lastname, $email, $role, $pwd);


}


// Funktion welche beim Editieren eines neuen Benutezrs über das Usermanagement im Admin-Bereich durchgeführt wird, auch hier werden bestimmte Abhängigkeiten geprüft
if (isset($_POST["subedit"])) {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $id = $_POST["id"];


    require_once '../database.php';
    require_once '../includes/function.inc.php';


    if(emptyInputSignup($firstname, $lastname, $email, "dummydata", "dummydata") !== false){
        header("location: ../admin/usermanagement.php?error=emptyinput#error");
        exit();
    }

    if(invalidEmail($email) !== false){
        header("location: ../admin/usermanagement.php?error=invalidEmail#error");
        exit();
    }

    editUser($connect, $firstname, $lastname, $email, $role, $id);


} else{
    header("location: ../admin/usermanagement.php");
}
