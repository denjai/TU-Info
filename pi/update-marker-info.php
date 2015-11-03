<?php
header('Content-Type: application/json');
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
$names_url = "http://localhost/api/names";
if (isset($_GET['name'])) {
    $name=$_GET['name'];
    $temp_url = $names_url . "/" . $name;
    $data = json_decode(file_get_contents($temp_url,false,$context), true);
    $response=array("name"=>$name,"humi"=>$data['humi'],"temp"=>$data['temp']);
    deliver_response(200, "OK", $response);
}
else{
    exit;
}

function deliver_response($status, $status_message, $data) {
    header("HTTP/1.1 $status $status_message");
    $json_response = json_encode($data);
    echo $json_response;
}