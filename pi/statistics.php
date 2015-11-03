<?php
header('Content-Type: application/json');
error_reporting(0);
//read log.txt
$file="log.txt";
$data =file_get_contents($file);
//expressions match name/type and return values for the period to do
$datetime="[0-9]*\-[0-9]*\-[0-9]*\s[0-9]*\:[0-9]*";
$name=".*\[.*\]";
$humidity=".*Humidity\:([0-9]*\.[0-9]*),";
$temperature=".*Temperature\:([0-9]*\.[0-9]*)\;";

$name1="garden";
$name2="server_room";
if($data){
    $name=".*\[".$name1."\]";
    if (preg_match_all("/".$datetime.$name.$humidity.$temperature."/", $data, $matches)) {
     $min_humi=min($matches[1]);
     $max_humi=max($matches[1]);
     $average_humi=  number_format(array_sum($matches[1])/count($matches[1]),2);
     $humi_stats=array($min_humi,$average_humi,$max_humi);
    
     $min_temp=min($matches[2]);
     $max_temp=max($matches[2]);
     $average_temp=  number_format(array_sum($matches[2])/count($matches[2]),2);
     $temp_stats=array($min_temp,$average_temp,$max_temp);
     $response1=array("humidity"=>$humi_stats,"temperature"=>$temp_stats);
}
else{
    $error_messages[] = "Can't find data for '".$name1."' in the log file:".$file."!";
}
$name=".*\[".$name2."\]";
    if (preg_match_all("/".$datetime.$name.$humidity.$temperature."/", $data, $matches)) {
     $min_humi=min($matches[1]);
     $max_humi=max($matches[1]);
     $average_humi=  number_format(array_sum($matches[1])/count($matches[1]),2);
     $humi_stats=array($min_humi,$average_humi,$max_humi);
    
     $min_temp=min($matches[2]);
     $max_temp=max($matches[2]);
     $average_temp=  number_format(array_sum($matches[2])/count($matches[2]),2);
     $temp_stats=array($min_temp,$average_temp,$max_temp);
     $response2=array("humidity"=>$humi_stats,"temperature"=>$temp_stats);
}else{
    $error_messages[] = "Can't find data for '".$name2."' in the log file:".$file."!";
}
$response=array($name1=>$response1,$name2=>$response2);
//echo $repsponse;
deliver_response(200, "OK", $response);

//else error cannot parse log file/cannot open file
}
else{
    $error_messages[] = "Can't open the log file:".$file."! Check if the file exists.";
    $response = array("errors"=>$error_messages);
    deliver_response(200, "OK", $response);
}

function deliver_response($status, $status_message, $data) {
    header("HTTP/1.1 $status $status_message");
    $json_response = json_encode($data);
    echo $json_response;
}
