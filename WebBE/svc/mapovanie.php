<?php
    /* "sluzba" pre ziskanie najnovsej vrstvy, ktora ma byt zobrazena v aktualnej mape */
    $servername = "localhost";
    $username = "id9038554_userrha";
    $password = "Heslo.123";
    $dbname = "id9038554_diplomovka";
    
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 

        //$hodnota = $_GET['h'];

        //$sql = "INSERT INTO meranie (hodnota) VALUES ({$hodnota})";
        $sql = "SELECT map.vrstva vrstva, mer.cas_merania cas FROM meranie mer JOIN mapovanie map ON map.hodnotaOd <= (mer.hodnota) AND (mer.hodnota) <= map.hodnotaDo ORDER BY mer.id DESC LIMIT 1";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $cas = $row['cas'];
                $vrstva = $row['vrstva'];
                //echo join(', ', $row);
            }
        } else {
            echo "N/A";
        }

        $date=date_create($cas);
        date_add($date,date_interval_create_from_date_string("2 hours"));
        echo date_format($date,"d.m.Y").";".date_format($date,"H:i:s").";".$vrstva;

        $conn->close();
?>