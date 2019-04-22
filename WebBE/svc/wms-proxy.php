<?php
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
    
    //If $contents is not a boolean FALSE value.
    //if($contents !== false){
        //Print out the contents.
        header('Content-type: image/png');
        echo $contents;
        //echo var_dump($contents);// imagecreatefromstring(file_get_contents($url));
    //}

    /*

    cURL

    */
    /*
    $ch = curl_init();
 
    //Set the URL that you want to GET by using the CURLOPT_URL option.
    curl_setopt($ch, CURLOPT_URL, $url);
    
    //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);       // enabled response headers
    
    //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    //Execute the request.
    $data = curl_exec($ch);
    
    //Close the cURL handle.
    curl_close($ch);

    // split response to header and content
    list($response_headers, $response_content) = preg_split('/(\r\n){2}/', $response, 2);
    
    // (re-)send the headers
    $response_headers = preg_split('/(\r\n){1}/', $response_headers);
    foreach ($response_headers as $key => $response_header) {
      // Rewrite the `Location` header, so clients will also use the proxy for redirects.
      if (preg_match('/^Location:/', $response_header)) {
        list($header, $value) = preg_split('/: /', $response_header, 2);
        $response_header = 'Location: ' . $_SERVER['REQUEST_URI'] . '?csurl=' . $value;
      }
      if (!preg_match('/^(Transfer-Encoding):/', $response_header)) {
        header($response_header, false);
      }
    }

    // finally, output the content
    print($response_content);

    //Print the data out onto the page.
    //echo var_dump($data);
 // }
 */
?>