<?php
    /*"sluzba" pre zapis dat z merani, posiela sa ako url parameter ?h=[hodnota]*/
    $servername = "localhost";
    $username = "id9038554_userrha";
    $password = "Heslo.123";
    $dbname = "id9038554_diplomovka";
 
    try {
    
        if (isset($_GET['h'])) {
        //ak je parameter uvedeny, vratime OK

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            
            //nadmorska vyska arduina
            $ard_nmv = '0';

            //zistenie aktualnej nadmorskej vysky arduina (ulozena v tabulke nastavenia)
            $sql = "SELECT hodnota FROM nastavenie WHERE nazov = 'arduino_nmv'";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                $row = $result->fetch_assoc();
                $ard_nmv = $row["hodnota"];
                //echo $ard_nmv;
            } else {
                //zatial nic
                //echo "N/A";
            }        

            //hodnota z arduina = vzdialenost od hladiny
            $vzdialenost = $_GET['h'];

            $hlad_nmv = $ard_nmv - $vzdialenost;

            $sql = "INSERT INTO meranie (hodnota) VALUES ({$hlad_nmv})";
            //$sql = "INSERT INTO meranie (hodnota) VALUES ({$vzdialenost})";

            if ($conn->query($sql) === TRUE) {
                echo "OK ".$hlad_nmv;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();

        } else {
            //ak nie je, vratime NOK
            echo "NOK";
        }
    }
    //catch exception
    catch(\Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }

?>