<?php

//Funktion die checkt ob der User eine leere Eingabe hat
   function emptyInputSignup($firstname, $lastname, $email, $pwd) {
    $result;
    if(empty($firstname) || empty($lastname) || empty($email) || empty($pwd)){
        $result = true;
    }
    else{
       $result = false;
    }
    return $result;
   }

//Funktion zur Überprüfung ob der User eine richtige Email eingegeben hat
   function invalidEmail($email) {
    $result;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = true;
    }
    else{
       $result = false;
    }
    return $result;
   }

//Funktion die überprüft ob das Passwort im Feld "pwd" und pwdrepeat übereinstimmen
   function pwdMatch($pwd, $pwdrepeat) {
    $result;
    if($pwd !== $pwdrepeat){
        $result = true;
    }
    else{
       $result = false;
    }
    return $result;
   }

//Funktion zu Überprüfung ob der User der sich grad registrieren will schon existiert
   function userExist($connect, $firstname, $lastname, $email) {
    $sql = "SELECT * FROM user WHERE first_name = ? AND last_name = ? OR email = ?;";
    $stmt = mysqli_stmt_init($connect);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../signup.php?error=stmtfailed1");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sss", $firstname, $lastname, $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }
    else{
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);

   }

//Funtion um den User zu erstellen und ihn in der Datenbank zu hinterlegen anhand der Input Felder aus der signup.php
   function createUser($connect, $firstname, $lastname, $email, $pwd){
    $sql = "INSERT INTO user (`first_name`, `last_name`, `email`, `password`, `role`) Values (?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
       header("location: ../signup.php?error=stmtfailed2");
       exit();
    }

    $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);
    $defaultRole = 0;


    mysqli_stmt_bind_param($stmt, "ssssi", $firstname, $lastname, $email, $hashedpwd, $defaultRole);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location: ../login.php?error=none");
    exit();
   }

//Funktion zum überprüfen ob ein leeres Feld in der login.php ist was der User hätte eintagen müssen
   function emptyInputLogin($email, $pwd) {
    $result;
    if(empty($email) || empty($pwd)){
        $result = true;
    }
    else{
       $result = false;
    }
    return $result;
   }


//Funktion die den User einloggt nachdem alle Felder von der Funktion zuvor auf inhalt gecheckt wurden
   function loginUser($connect, $email, $pwd ){
    $emailExists = userExist($connect, $firstname, $lastname, $email);

    if(!$emailExists){
        header("location: ../login.php?error=wronglogin");
        exit();
    }

    $pwdHashed = $emailExists["password"];
    $checkPwd = password_verify($pwd, $pwdHashed);

    if($checkPwd === false){
       header("location: ../login.php?error=wronglogin");
       exit();
    }
    else if ($checkPwd === true){
        session_start();
        $_SESSION["id"] = $emailExists["id"];
        $_SESSION["first_name"] = $emailExists["first_name"];
        $_SESSION["last_name"] = $emailExists["last_name"];
        $_SESSION["role"] = $emailExists["role"];
      if($_SESSION["role"] == 1){
        header("location: ../admin/dashboard.php");
        exit();
      }else if($_SESSION["role"] == 0){
        header("location: ../request.php");
      }
    }
   }

// Funktion welche prüft, ob alle Pflichtfelder zum hinzufügen von Hardwre gefüllt wurden
  function emptyInputHardware($manufacturer, $model, $lastEditedBy, $status) {
   $result;
   if(empty($manufacturer) || empty($model) || empty($lastEditedBy) || empty($status)){
     $result = true;
    }
    else{
       $result = false;
    }
    return $result;
  }


  function name() {
    // code
  }

// Funktion zum Hinzufügen eines neuen Benutzers für die Software
function addUser($connect, $firstname, $lastname, $email, $role, $pwd){
    $sql = "INSERT INTO user (`first_name`, `last_name`, `email`, `role`, `password`) Values (?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../admin/usermanagement.php?error=stmtfailed2");
        exit();
    }

    $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);


    mysqli_stmt_bind_param($stmt, "sssis", $firstname, $lastname, $email, $role, $hashedpwd);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../admin/usermanagement.php?error=none#success");

  }

// Funktion zum Löschen eines bestehenden Benutzers
  function delUser($connect, $id) {
    $sql = "DELETE FROM user WHERE id= ? ";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      mysqli_stmt_close($stmt);
      return false;
    }

    foreach ($id as $value) {
      mysqli_stmt_bind_param($stmt, "i", $value);
      mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);

    return true;
  }

// Funktion zum Beabreiten eines bestehenden Benutzers
  function editUser($connect, $firstname, $lastname, $email, $role, $id) {
    $sql = "UPDATE user SET first_name=?, last_name=?, email=?, role=? WHERE id=?";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../admin/usermanagement.php?error=stmtfailed1");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sssii", $firstname, $lastname, $email, $role, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../admin/usermanagement.php?error=enone#success");

  }

// Funktion zum Hinzufüghen eines Hardwaregegenstandes zum Inventar
  function addItem($connect, $manufacturer, $serialnumber, $model, $lastEditedBy, $status, $notes) {

    $deviceID = getDeviceID($connect, $manufacturer, $model);

    $sql = "INSERT INTO hardware (`device`, `serialnumber`, `last_edited_by`,`status`,`notes`) VALUES ( ?, ?, ?, ?, ? )";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/hardwaremanagement.php?error=stmtfailedx");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "isiis", $deviceID, $serialnumber, $lastEditedBy, $status, $notes);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../admin/hardwaremanagement.php?error=none#success");

    }


// Funktion um die GeräteID eines Gegenstandes zu extrahieren
  function getDeviceID($connect, $manufacturer, $model){
    $sql = "SELECT id FROM device WHERE manufacturer = ? AND model = ? ";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/hardwaremanagement.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "ii", $manufacturer, $model);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $obj = mysqli_fetch_object($resultData);

    mysqli_stmt_close($stmt);

    if (!is_null($obj)){
      $deviceid = $obj->id;
    }
    else{
      $sql = "INSERT INTO device (`manufacturer`, `model`) VALUES ( ? , ? )";
      $stmt = mysqli_stmt_init($connect);

      if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../admin/hardwaremanagement.php?error=stmtfailed");
        exit();
      }

      mysqli_stmt_bind_param($stmt, "ii", $manufacturer, $model);
      mysqli_stmt_execute($stmt);

      $deviceid = mysqli_stmt_insert_id($stmt);

      mysqli_stmt_close($stmt);

    }

    return $deviceid;

  }

// Funktion zum Löschen eines Hardwaregegenstandes
  function delItem($connect, $id) {

    $sql = "DELETE FROM hardware WHERE id= ? ";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      mysqli_stmt_close($stmt);
      return false;
    }

    foreach ($id as $value) {
      mysqli_stmt_bind_param($stmt, "s", $value);
      if (!mysqli_stmt_execute($stmt)){
        return false;
      }
    }

    mysqli_stmt_close($stmt);

    return true;

  }

// Funktion zum editieren eines bestehenden Hardwaregegenstandes
  function editItem($connect, $hardwareid, $manufacturer, $serialnumber, $model, $lastEditedBy, $status, $notes) {
    $deviceID = getDeviceID($connect, $manufacturer, $model);

    $sql = "UPDATE hardware SET device=?, serialnumber=?, last_edited_by=?, status=?, notes=?  WHERE id=?";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/hardwaremanagement.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "isiisi", $deviceID, $serialnumber, $lastEditedBy, $status, $notes, $hardwareid);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../admin/hardwaremanagement.php?error=enone#success");


  }

// Funktion zum Prüfen, ob Daten wie Model, Marke und Status bereits vorhanden sind
  function checkIfBasicDataExists($connect, $data, $type){
    if ($type == 1){
      $sql = "SELECT name FROM manufacturer WHERE name=?";
    }
    elseif ($type == 2){
      $sql = "SELECT name FROM model WHERE name=?";
    }
    elseif ($type == 3){
      $sql = "SELECT name FROM status WHERE name=?";
    }

    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/hardwaremanagement.php?error=stmtfailedx");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $data);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $obj = mysqli_fetch_object($resultData);

    mysqli_stmt_close($stmt);

    if (is_null($obj)){
      return false;
    }else{
      return true;
    }
  }



// Funktion welche die Daten hinzugefügt, sollten Modell, Marke opder Status nicht bereits vorhanden sein
  function addBasicData($connect, $data, $type){
    if (checkIfBasicDataExists($connect, $data, $type)){
      header("location: ../admin/hardwaremanagement.php?error=alreadyexist#error");
      exit();
    }

    if ($type == 1){
      $query = "INSERT INTO manufacturer (`name`) VALUES (?)";
    }
    elseif ($type == 2){
      $query = "INSERT INTO model (`name`) VALUES (?)";
    }
    elseif ($type == 3){
      $query = "INSERT INTO status (`name`) VALUES (?)";
    }


    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $query)){
      header("location: ../admin/hardwaremanagement.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $data);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../admin/hardwaremanagement.php?error=none#success");

  }



//Funktion die checkt ob der User eine leere Eingabe hat
  function emptyInputRequest($lendOutDate, $returnDate, $reason) {
    if(empty($lendOutDate) || empty($returnDate) || empty($reason) ){
      return true;
    }
    else{
      return false;
    }
   }

//Funktion die überprüft, ob aktuell zu viele Anfragen offen sind
  function checkToManyOpenRequests($connect, $requester){
    $sql = "SELECT COUNT(*) as total FROM requests WHERE requester=? AND request_status IS NULL";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../request.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $requester);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($resultData);

    mysqli_stmt_close($stmt);

    if($count['total'] >= 2){
      return true;
    }else{
      return false;
    }
  }


//Funktion welche beim Absenden der Anfrage durch die Schüler ausgeführt wird
  function createRequest($connect, $requester, $lendOutDate, $returnDate, $reason) {
    $sql = "INSERT INTO requests (`requester`, `lend_out_date`, `return_date`, `reason`) VALUES (?,?,?,?)";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../request.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "isss", $requester, $lendOutDate, $returnDate, $reason);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


    header("location: ../request.php?error=none#success");

  }



  function cancelRequest($connect) {
    // code
  }


  function acceptRequest($connect, $id, $proessor) {
    // code
  }

//Funktion welche die Verfügbarkeit des Angegebene Gegendstandes durch den Schüler, im entsprechenden Zeitfenster prüft
  function checkDeviceAvailabillity($connect, $id, $beginning, $ending, $proessor) {
    $query = "SELECT h.id, h.status, h.serialnumber, h.last_edited, h.notes
    FROM hardware AS h
    , f.first_name AS assigned_user_first_name
    , l.last_name AS assigned_user_last_name
    , x.first_name AS last_edited_by_first_name
    , y.last_name AS last_edited_by_last_name
    , m.name AS manufacturer
    , mo.name AS model
    , s.name AS status_name
    LEFT JOIN user AS f ON f.id = h.assigned_user
    LEFT JOIN user AS l ON l.id = h.assigned_user
    LEFT JOIN user AS x ON x.id = h.last_edited_by
    LEFT JOIN user AS y ON y.id = h.last_edited_by
    INNER JOIN device AS d ON d.id = h.device
    INNER JOIN manufacturer AS m ON m.id = d.manufacturer
    INNER JOIN model AS mo ON mo.id = d.model
    INNER JOIN status AS s ON s.id = h.status
    WHERE h.lend_out_date NOT BETWEEN ? and ?
    AND WHERE h.return_date NOT BETWEEN ? and ?
    AND WHERE NOT ? < h.return_date AND ? > h.lend_out_date";
    // code
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/requestmanagement.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $beginning, $ending, $beginning, $ending, $beginning, $beginning);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($resultData);

    mysqli_stmt_close($stmt);

    return $result;
  }

//Funktion welche eine Anfrage für eine Ausleihe ablehnt
  function denyRequest($connect, $id, $processor) {
    $sql = "UPDATE requests SET request_status=?, processor=? WHERE id=?";
    $status = 0;
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/requestmanagement.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "iis", $status, $processor, $id);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


    $sql = "SELECT requester FROM requests WHERE id=?";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../admin/requestmanagement.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $id);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $requester = mysqli_fetch_assoc($resultData);

    mysqli_stmt_close($stmt);


    createNotification($connect, $requester['requester'], "deny");

    return true;

  }

//Funktion welche dem Schüler eine Info über den aktuellen Status seiner Anfrage gibt
  function createNotification($connect, $user, $type, $additionalInfo) {


    if ($type == xyz){
      $message= s;
    }elseif ($type == xyz){

    }elseif ($type == xyz){

    }elseif ($type == xyz){

    }elseif ($type == xyz){

    }elseif ($type == xyz){

    }elseif ($type == xyz){

    }


    $message = "Dein Antrag wurde abgelehnt";
    $message = "Dein Antrag wurde angenommen";
    $message = "Ein neuer Antrag wurde eingereicht";
    $message = "Ein Gerät ist überfällig!";
    $message = "Deine Geräterückgabe ist überfällig";
    $message = "";
    $message = "";
    $message = "";
    $message = "";

    $sql = "INSERT INTO notifications (`user`, `message`) VALUES (?,?)";
    $stmt = mysqli_stmt_init($connect);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("location: ../request.php?error=stmtfailed");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "isss", $user, $message);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);



  }

//Funktion welche die aktuell Zeit zur weiterverarbeitung in abhängige Funktionen ausgibt
  function getNow(){
   # $now = new DateTime('now', new DateTimeZone('Europe/Berlin'));
    $now = date_create('now', timezone_open('Europe/Berlin'));
    return $now;
  }



//Funktion zum Ändern des Passworts über die persönliche Profilwebseite
  function changePassword($connect, $pwd, $pwdrepeat, $id){
    $stmt=$connect->prepare("UPDATE user SET password=? WHERE id = ?");
    $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);


    mysqli_stmt_bind_param($stmt, "si", $hashedpwd, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location: ../profile.php?error=none");
    exit();

   }

//Funktion welche prüft, ob die Eingabe zum ändern des Passworts vollständig ist
   function emptyInputProfile($pwd, $pwdrepeat) {
    $result;
    if(empty($pwd) || empty($pwdrepeat)){
        $result = true;
    }
    else{
       $result = false;
    }
    return $result;
  }
?>
