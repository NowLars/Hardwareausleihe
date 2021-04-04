<?php
#################################
#                               #
#   Hardware-Formular-Backend   #
#            hinzufügen         #
#                               #
#################################

if (isset($_POST["sub"])) {
    session_start();
    $manufacturer = $_POST['manufacturer'];
    $model = $_POST['model'];
    $serialnumber = $_POST['serialnumber'];
    $lastEditedBy = $_SESSION['id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];


    require_once '../database.php';
    require_once '../includes/function.inc.php';

    if(emptyInputHardware($manufacturer, $model, $lastEditedBy, $status) !== false){
        header("location: ../admin/hardwaremanagement.php?error=emptyinput#error");
        exit();
    }

    addItem($connect, $manufacturer, $serialnumber, $model, $lastEditedBy, $status, $notes);


}



elseif (isset($_POST["sub-manufacturer"])) {
    $manufacturer = $_POST['manufacturer'];
    $type = 1;

    require_once '../database.php';
    require_once '../includes/function.inc.php';

    addBasicData($connect, $manufacturer, $type);

}

elseif (isset($_POST["sub-model"])) {
    $model = $_POST['model'];
    $type = 2;

    require_once '../database.php';
    require_once '../includes/function.inc.php';

    addBasicData($connect, $model, $type);

}

elseif (isset($_POST["sub-status"])) {
    $status = $_POST['status'];
    $type = 3;

    require_once '../database.php';
    require_once '../includes/function.inc.php';

    addBasicData($connect, $status, $type);

}



#################################
#                               #
#  Bearbeiten Hardware-Form     #
#            Backend             #
#                               #
#################################

elseif (isset($_POST["subedit"])) {
    session_start();
    $hardwareid = $_POST['hardwareid'];
    $manufacturer = $_POST['manufacturer'];
    $model = $_POST['model'];
    $serialnumber = $_POST['serialnumber'];
    $lastEditedBy = $_SESSION['id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];


    require_once '../database.php';
    require_once '../includes/function.inc.php';


    if(emptyInputHardware($manufacturer, $model, $lastEditedBy, $status) !== false){
        header("location: ../admin/hardwaremanagement.php?error=emptyinput#error");
        exit();
    }

    editItem($connect, $hardwareid, $manufacturer, $serialnumber, $model, $lastEditedBy, $status, $notes);


} else{
    header("location: ../admin/hardwaremanagement.php");
}
