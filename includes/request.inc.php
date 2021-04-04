<?php
// Funktion welche beim Beantragen von Hardware im Backend durchgeführt wird, um unter anderem zu prüfen, ob genügend Hardwre vorhanden ist, sowie alle weiteren Abhängigkeiten 
if (isset($_POST["sub"])) {
    session_start();

    require_once '../database.php';
    require_once '../includes/function.inc.php';

    $lendOutDate = $_POST['lend_out_date'];
    $returnDate = $_POST['return_date'];
    $reason = $_POST['reason'];
    $requester = $_SESSION['id'];
    $now = getNow();


    if(emptyInputRequest($lendOutDate, $returnDate, $reason)){
        header("location: ../request.php?error=emptyinput#error");
        exit();
    }

    if (($now > date_create($lendOutDate)) OR (date_create($returnDate) < date_create($lendOutDate))){
        header("location: ../request.php?error=wrongdate#error");
        exit();
    }

    if(checkToManyOpenRequests($connect, $requester)){
        header("location: ../request.php?error=tomanyrequests#error");
        exit();
    }

    createRequest($connect, $requester, $lendOutDate, $returnDate, $reason);


}else{
    header("location: ../request.php");
}
