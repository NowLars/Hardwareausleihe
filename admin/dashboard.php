<!-- Diese Webseite dient zur Übersicht für die Adminstratoren der Webseite,
hier können der Aktuelle Bestand, die Überfalligen Geräte und weitere Informationen eingesehen werden -->
<?php require("../database.php");
      require('../header_login_admin.php');
      require('../includes/function.inc.php');



?>

<html>
  <meta charset="utf-8" />
    <head>
      <link rel="stylesheet" href="../stylesheets/modal.css" />
      <link rel="stylesheet" href="../stylesheets/dashboard.css?v=<?php echo time(); ?>" />
    </head>
    <body>
      <div class="container">
        <div class="status-box-wrapper">

        <?php
            #########################################
            #                                       #
            # Erstellen von Zählern, zur Übersicht  #
            #                                       #
            #########################################
            // Anzahl an:
            // - Anzahl verfügabrer Geräte - green
            // - Anzahl an offenen Anfragen - blue
            // - Anzahl an ausgeliehenen Geräten - yellow
            // - Anzahl an überschrittenem Rückgabedatum - red

            $get_stored_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT count(*) as total from hardware where assigned_user IS NULL"));
            $get_open_request_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT count(*) as total from requests where request_status IS NULL"));
            $get_lend_out_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT count(*) as total from hardware where return_date > now()"));
            $get_overdue_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT count(*) as total from hardware where return_date < now()"));


        ?>


        <?
            #########################################
            #                                       #
            # Erstellen der Boxen, mit den Anzahlen #
            #                                       #
            #########################################
        ?>


        <div class="status-box green-box" >
            <p class="status-box-description" >Gelagert:</p>
            <p class="status-box-value" > <?php echo($get_stored_count["total"]);?></p>
        </div>

        <div class="status-box blue-box">
            <p class="status-box-description" >Anfragen:</p>
            <p class="status-box-value" ><?php echo($get_open_request_count["total"]);?></p>
        </div>

        <div class="status-box yellow-box">
            <p class="status-box-description" >Verliehene:</p>
            <p class="status-box-value" ><?php echo($get_lend_out_count["total"]);?></p>
        </div>

        <div class="status-box red-box">
            <p class="status-box-description" >Überfällige:</p>
            <p class="status-box-value" ><?php echo($get_overdue_count["total"]);?></p>
        </div>

       </div>



    <?php

        #########################################
        #   Erhalten der zutreffenden Geräte    #
        #  Für Verspätete Abgabe und anstehende #
        #              Ausleihen                #
        #########################################

        $get_lend_outs_next_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM hardware WHERE lend_out_date BETWEEN now() AND adddate(now(),+14)"));
        $get_returns_next_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM hardware WHERE return_date BETWEEN now() AND adddate(now(),+14)"));




        #################################################
        #   Erstellen der detailierten Continaern zu    #
        #          zur Übersicht über Ausleihen etc.    #
        #################################################
    ?>

    <!-- Container zur Anzweige der Geräte, welche in den nächsten 14 Tagen ausgegeben werden -->
    <div class="center2">
    <div class="overview-tables-wrapper">
        <div class="overview-table lend-outs">
            <div class="overview-table-headline">
                <p>Ausgabe nächste 14 Tage: <?php echo(" " . $get_lend_outs_next_count['total']);?></p>
            </div>
            <div class="overview-table-table-wrapper">
                <table class="table">
                    <tr>
                        <th>Hersteller</th>
                        <th>Modell</th>
                        <th>Zugewiesen</th>
                        <th>Bearbeiter</th>
                        <th>Status</th>
                        <th>Leihdatum</th>
                        <th>Rückgabedatum</th>
                    </tr>

                    <?php
                    $lend_outs_next_query = mysqli_query($connect, "SELECT h.id, h.lend_out_date, h.return_date, h.notes ,f.first_name AS assigned_user_first_name , l.last_name AS assigned_user_last_name , x.first_name AS last_edited_by_first_name , y.last_name AS last_edited_by_last_name , m.name AS manufacturer , mo.name AS model , s.name AS status FROM hardware AS h INNER JOIN user AS f ON f.id = h.assigned_user INNER JOIN user AS l ON l.id = h.assigned_user INNER JOIN user AS x ON x.id = h.last_edited_by INNER JOIN user AS y ON y.id = h.last_edited_by INNER JOIN device AS d ON d.id = h.device INNER JOIN manufacturer AS m ON m.id = d.manufacturer INNER JOIN model AS mo ON mo.id = d.model INNER JOIN status AS s ON s.id = h.status WHERE h.lend_out_date BETWEEN now() AND adddate(now(),+14) ORDER BY h.lend_out_date LIMIT 10");
                        while($dsatz = mysqli_fetch_assoc($lend_outs_next_query)){
                            echo "<tr>" .
                                "<td>$dsatz[manufacturer]</td>
                                <td>$dsatz[model]</td>
                                <td>$dsatz[assigned_user_first_name]" . " " . "$dsatz[assigned_user_last_name]</td>
                                <td>$dsatz[last_edited_by_first_name]" . " " . "$dsatz[last_edited_by_last_name]</td>
                                <td>$dsatz[status]</td>
                                <td>$dsatz[lend_out_date]</td>
                                <td>$dsatz[return_date]</td>" .
                                "</tr>" ;
                        }
                        mysqli_free_result($lend_outs_next_query);
                echo"</table>";
                    if ($get_lend_outs_next_count["total"] == 0){
                        echo"<br>Keine Einträge";
                    }


                    ?>

            </div>
        </div>
        <!--Container zur Anzweige der Geräte, welche in den nächsten 14 Tagen Zurückgeegebn werden-->
        <div class="overview-table returns">
            <div class="overview-table-headline">
                <p>Rückgaben nächste 14 Tage: <?php echo(" " . $get_returns_next_count['total']);?></p>
            </div>
            <div class="overview-table-table-wrapper">
                <table class="table">
                    <tr>
                        <th>Hersteller</th>
                        <th>Modell</th>
                        <th>Zugewiesen</th>
                        <th>Bearbeiter</th>
                        <th>Status</th>
                        <th>Leihdatum</th>
                        <th>Rückgabedatum</th>
                    </tr>

                    <?php
                    $returns_next_query = mysqli_query($connect, "SELECT h.id, h.status, h.lend_out_date, h.return_date, h.notes , f.first_name AS assigned_user_first_name , l.last_name AS assigned_user_last_name , x.first_name AS last_edited_by_first_name , y.last_name AS last_edited_by_last_name , m.name AS manufacturer , mo.name AS model FROM hardware AS h INNER JOIN user AS f ON f.id = h.assigned_user INNER JOIN user AS l ON l.id = h.assigned_user INNER JOIN user AS x ON x.id = h.last_edited_by INNER JOIN user AS y ON y.id = h.last_edited_by INNER JOIN device AS d ON d.id = h.device INNER JOIN manufacturer AS m ON m.id = d.manufacturer INNER JOIN model AS mo ON mo.id = d.model WHERE h.return_date BETWEEN now() AND adddate(now(),+14) ORDER BY h.return_date LIMIT 10");
                        while($dsatz = mysqli_fetch_assoc($returns_next_query)){
                            echo "<tr>" .
                                "<td>$dsatz[manufacturer]</td>
                                <td>$dsatz[model]</td>
                                <td>$dsatz[assigned_user_first_name]" . " " . "$dsatz[assigned_user_last_name]</td>
                                <td>$dsatz[last_edited_by_first_name]" . " " . "$dsatz[last_edited_by_last_name]</td>
                                <td>$dsatz[status]</td>
                                <td>$dsatz[lend_out_date]</td>
                                <td>$dsatz[return_date]</td>" .
                            "</tr>" ;
                        }
                        mysqli_free_result($returns_next_query);
                echo"</table>";
                    if ($get_returns_next_count["total"] == 0){
                        echo"<br>Keine Einträge";
                    }


                    ?>
            </div>
        </div>
        <!--Container zur Anzweige der Geräte, welche ein Überfälliges Rückgabedatum haben-->
        <div class="overview-table overdue">
            <div class="overview-table-headline">
                <p>Überfällige Geräte Liste: <?php echo(" " . $get_overdue_count["total"]);?></p>
            </div>
            <div class="overview-table-table-wrapper">
                <table class="table">
                    <tr>
                        <th>Hersteller</th>
                        <th>Modell</th>
                        <th>Zugewiesen</th>
                        <th>Bearbeiter</th>
                        <th>Status</th>
                        <th>Leihdatum</th>
                        <th>Rückgabedatum</th>
                    </tr>

                    <?php
                    $overdue_list_query = mysqli_query($connect, "SELECT h.id, h.lend_out_date, h.return_date, h.notes , f.first_name AS assigned_user_first_name , l.last_name AS assigned_user_last_name , x.first_name AS last_edited_by_first_name , y.last_name AS last_edited_by_last_name , m.name AS manufacturer , mo.name AS model , s.name AS status FROM hardware AS h LEFT JOIN user AS f ON f.id = h.assigned_user LEFT JOIN user AS l ON l.id = h.assigned_user LEFT JOIN user AS x ON x.id = h.last_edited_by LEFT JOIN user AS y ON y.id = h.last_edited_by INNER JOIN device AS d ON d.id = h.device INNER JOIN manufacturer AS m ON m.id = d.manufacturer INNER JOIN model AS mo ON mo.id = d.model INNER JOIN status AS s ON s.id = h.status WHERE h.return_date < now() ORDER BY h.return_date LIMIT 10");
                        while($dsatz = mysqli_fetch_assoc($overdue_list_query)){
                            echo "<tr>" .
                                "<td>$dsatz[manufacturer]</td>
                                <td>$dsatz[model]</td>
                                <td>$dsatz[assigned_user_first_name]" . " " . "$dsatz[assigned_user_last_name]</td>
                                <td>$dsatz[last_edited_by_first_name]" . " " . "$dsatz[last_edited_by_last_name]</td>
                                <td>$dsatz[status]</td>
                                <td>$dsatz[lend_out_date]</td>
                                <td>$dsatz[return_date]</td>" .
                            "</tr>" ;
                        }
                        mysqli_free_result($overdue_list_query);
                echo"</table>";
                    if ($get_overdue_count["total"] == 0){
                        echo"<br>Keine Einträge";
                    }


                    ?>
            </div>
        </div>
        <!--Container zur Anzweige der Anfragen, welche offen sind und noch nicht bearbeitet wurden-->
        <div class="overview-table open-requests">
            <div class="overview-table-headline">
                <p>Offene Anfragen: <?php echo(" " . $get_open_request_count["total"]);?></p>
            </div>
            <div class="overview-table-table-wrapper">
                <table class="table">
                    <tr>
                        <th>Anfragesteller</th>
                        <th>Leihdatum</th>
                        <th>Rückgabedatum</th>
                        <th>Zeitpunkt</th>
                    </tr>

                    <?php
                    $open_request_list_query = mysqli_query($connect, "SELECT r.timestamp, r.lend_out_date, r.return_date , f.first_name AS requester_first_name , l.last_name AS requester_last_name , m.name AS manufacturer , mo.name AS model FROM requests AS r LEFT JOIN user AS f ON f.id = r.requester LEFT JOIN user AS l ON l.id = r.requester LEFT JOIN hardware AS h ON h.id = r.hardware_id LEFT JOIN device AS d ON d.id = h.device LEFT JOIN manufacturer AS m ON m.id = d.manufacturer LEFT JOIN model AS mo ON mo.id = d.model WHERE request_status IS NULL ORDER BY r.timestamp");
                        while($dsatz = mysqli_fetch_assoc($open_request_list_query)){
                            $sinceWhen = date_diff(getNow(),date_create($dsatz['timestamp']));
                            $sinceWhen = ($sinceWhen->format('vor %h Stunden'));
                            echo "<tr>" .
                                    "<td>$dsatz[requester_first_name]" . " " . "$dsatz[requester_last_name]</td>
                                    <td>$dsatz[lend_out_date]</td>
                                    <td>$dsatz[return_date]</td>
                                    <td>$sinceWhen</td>" .
                                "</tr>";
                        }
                        mysqli_free_result($open_request_list_query);
                echo"</table>";
                    if ($get_open_request_count['total'] == 0){
                        echo"<br>Keine Einträge";
                    }


                    ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
