<?php
    //udaje pre prihlasenie do DB
    $servername = "localhost";
    $username = "id9038554_userrha";
    $password = "Heslo.123";
    $dbname = "id9038554_diplomovka";
  //if (isset($_SERVER[REQUEST_URI])) {

    //The URL with parameters / query string.
    //$url = 'http://webmap.sk:8080/geoserver2112/nitra_students/wms?&service=WMS&request=GetFeatureInfo&version=1.1.1&layers=nitra_students%3Ajelenec_335&styles=&format=image%2Fpng&transparent=true&tiled=true&width=1653&height=600&srs=EPSG%3A3857&bbox=2025144.1253016654%2C6172067.88891453%2C2029092.5755440213%2C6173501.083194875&query_layers=nitra_students%3Ajelenec_335&X=778&Y=288';

    $req_uri = $_SERVER[REQUEST_URI];

    //"PROXY", vymiename "lokalnu" URL za tu, ktoru chceme realne volat (v tomto pripade WMS)
    $url = str_replace("/svc/wms-proxy.php?","http://webmap.sk:8080/geoserver2112/nitra_students/wms?",$req_uri);

    //echo $req_uri."<br />".$url;

    //GET_???
    //echo $url;

    //$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    //Once again, we use file_get_contents to GET the URL in question.
    //$headers = get_headers($url);
    
    //echo var_dump($headers);
    $contents = file_get_contents($url);
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $headers = get_headers($url);
    //$sql = "INSERT INTO wms_proxy_log (request_orig, request_prox, response) VALUES ('$req_uri','$url','$contents')";
    $sql = "INSERT INTO wms_proxy_log (request_orig, request_prox) VALUES ('$req_uri','$url')";
    //$sql = "INSERT INTO meranie (hodnota) VALUES ({$vzdialenost})";

    //$headers_txt = implode("","", $headers)
    //$sql = "INSERT INTO wms_proxy_log (request_orig, request_prox, res_headers) VALUES ('$req_uri','$url','$headers_txt')";

    if ($conn->query($sql) === TRUE) {
        //echo "OK (vv=$hlad_nmv)";
        //, a: $ard_nmv, d: $dno_nmv";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();

    //If $contents is not a boolean FALSE value.
    //if($contents !== false){
        //Print out the contents.
        header('Content-type: image/png');
        echo $contents;
        //echo var_dump($contents);// imagecreatefromstring(file_get_contents($url));
    //}

  //https://www.dougv.com/2012/03/converting-latitude-and-longitude-coordinates-between-decimal-and-degrees-minutes-seconds/

?>