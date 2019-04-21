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
            //a dna vodnej nadrze
            $dno_nmv = 0;

            //zistenie aktualnej nadmorskej vysky arduina (ulozena v tabulke nastavenia)
            //aktualne query s radenim hodnot zalozena na nazve nastaveni, trosku naivne a nebezpecne, takze ak, tak
            //v buducnosti upravit
            $sql = "SELECT hodnota nmv FROM nastavenie WHERE nazov = 'arduino_nmv' OR nazov = 'dno_nmv' ORDER BY nazov ASC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                //vysledok by mal mat vzdy 2 riadky, takze nejdeme riesit, co ak nema :)
                $row = $result->fetch_assoc();
                $ard_nmv = $row["nmv"];
                $row = $result->fetch_assoc();
                $dno_nmv = $row["nmv"];
            } else {
                //zatial nic
                //echo "N/A";
            }        

            //hodnota z arduina = vzdialenost od hladiny
            $vzdialenost = $_GET['h'];

            //do databazy ukladame "vysku vody v nadrzi", t.j.
            //nadmorska vyska hladiny (nadmorska vyska arduina - vzdialenost k hladine) - nadmorska vyska dna (najnizsi bod)
            $hlad_nmv = $ard_nmv - ($vzdialenost / 100) - $dno_nmv;

            $sql = "INSERT INTO meranie (hodnota) VALUES ({$hlad_nmv})";
            //$sql = "INSERT INTO meranie (hodnota) VALUES ({$vzdialenost})";

            if ($conn->query($sql) === TRUE) {
                echo "OK (vv=$hlad_nmv)";
                //, a: $ard_nmv, d: $dno_nmv";
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