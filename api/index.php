<?php
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=UTF-8');
error_reporting(0);
$context = stream_context_create(array('http' => array('header' => 'Connection: close\r\n', 'timeout' => 1)));

if (isset($_GET['errors'])) {
    $error = strtolower($_GET['errors']);
    if ($error == 1) {
        $response = array("status" => array('message' => "Access denied", 'status_code' => '401'));
        deliver_response(401, "Unauthorized", $response);
        exit;
    }
}
//-------------------------- get data ------------------------------------------
$url = "http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp";
$data = file_get_contents($url, false, $context);
if (!$data) {
    $response = array("status" => array('message' => "Service Unavailable! Can not connect to" . $url, 'status_code' => '503'));
    deliver_response(503, "Service Unavailable", $response);
    exit;
}

$garden = array("name" => "garden", "temp" => "0", "humi" => "0", "status" => "ok", "latitude" => "0.0", "longitude" => "0.0", "date" => 0);
$server_room = array("name" => "server_room", "temp" => "0", "humi" => "0", "status" => "ok", "latitude" => "0.0", "longitude" => "0.0", "date" => 0);
//---------------------------- parse data --------------------------------------
$error = 0;
//matching temperature
if (preg_match_all("/\<b\>(.*)\s\&deg;\<\/b\>/", $data, $temp)) {
    $server_room['temp'] = $temp[1][0];
    $garden['temp'] = $temp[1][1];
} else
    $error = 1;
//match humidity
if (preg_match_all("/\<b\>(.*)\s\%\<\/b\>/", $data, $hum)) {
    $server_room['humi'] = $hum[1][0];
    $garden['humi'] = $hum[1][1];
} else
    $error = 2;

//match status
if (preg_match_all("/Stat.*\<b\>(.*)\<\/b\>/", $data, $stat)) {
    $server_room['status'] = $stat[1][0];
    $garden['status'] = $stat[1][1];
} else
    $error = 3;

//matching latitude and longitude (floating point)
if (preg_match_all("/\w=([0-9]*\.[0-9]+)/", $data, $coords)) {
    $server_room['latitude'] = $coords[1][0];
    $server_room['longitude'] = $coords[1][1];
    $garden['latitude'] = $coords[1][2];
    $garden['longitude'] = $coords[1][3];
} else
    $error = 4;
//match date-time
if (preg_match_all("/([a-zA-z]+\s[0-9]+\s[0-9]+\:[0-9]+\:[0-9]+\s[a-zA-z]+\s[0-9]+)/", $data, $matches)) {
    $date = date_parse($matches[1][0]);
    if($date[error_count]>0){
       $error = 5; 
    }
    $garden['date'] = $date;
    $server_room['date'] = $date;
} else
    $error = 5;

if ($error > 0) {
    $response = array("status" => array('message' => "Service Unavailable! Parsing error.", 'status_code' => '503'));
    deliver_response(503, "Service Unavailable", $response);
    exit;
}
//------------------------------- echo data ------------------------------------
if (isset($_GET['name'])) {
    $name = strtolower($_GET['name']);
    if ($name == "garden") {
        deliver_response(200, "Data found!", $garden);
        exit;
    }
    if ($name == "serverroom" || $name == "server_room" || $name == "server-room") {
        deliver_response(200, "Data found!", $server_room);
        exit;
    }
    if ($name == '0') {
    deliver_response(200, "Data found!", array("names" =>array($server_room['name'],$garden['name'])));
        exit;
    } else {
        $response = array("status" => array('message' => "Name - '$name' Not Found", 'status_code' => '404'));
        deliver_response(404, "Not Found!", $response);
        exit;
    }
}

if (isset($_GET['latitude']) && isset($_GET['longitude']) && !isset($_GET['type'])) {
    $latitude = strtolower($_GET['latitude']);
    $longitude = strtolower($_GET['longitude']);
    if ($latitude == '0' && $longitude == '0') {
        $temp = array("server_room" => array("latitude" => $server_room['latitude'], "longitude" => $server_room['longitude']), "garden" => array("latitude" => $garden['latitude'], "longitude" => $garden['longitude']));
        deliver_response(200, "Found", $temp);
        exit;
    }
    if ($latitude == $server_room['latitude'] && $longitude == $server_room['longitude']) {
        unset($server_room['latitude']);
        unset($server_room['longitude']);
        $temp = array("location" => $server_room);
        deliver_response(200, "Found", $temp);
        exit;
    }
    if ($latitude == $garden['latitude'] && $longitude == $garden['longitude']) {
        unset($garden['latitude']);
        unset($garden['longitude']);
        $temp = array("location" => $garden);
        deliver_response(200, "Found", $temp);
        exit;
    } else {
        $response = array("status" => array('message' => "Location Not Found", 'status_code' => '404'));
        deliver_response(404, "Not Found!", $response);
        exit;
    }
}
//location - type
if (isset($_GET['latitude']) && isset($_GET['longitude']) && isset($_GET['type'])) {
    $latitude = strtolower($_GET['latitude']);
    $longitude = strtolower($_GET['longitude']);
    $type = strtolower($_GET['type']);

    if ($latitude == $server_room['latitude'] && $longitude == $server_room['longitude']) {
        if ($type == "temp" || $type == "temperature") {
            $temp_type = array("temperature" => $server_room['temp'], "date" => $date);
            $temp = array("server_room" => $temp_type);
            deliver_response(200, "Data found!", $temp);
            exit;
        }
        if ($type == "humi" || $type == "humidity") {
            $temp_type = array("humidity" => $server_room['humi'], "date" => $date);
            $temp = array("server_room" => $temp_type);
            deliver_response(200, "Data found!", $temp);
            exit;
        } else {
            $response = array("status" => array('message' => "Type - '$type' Not Found", 'status_code' => '404'));
            deliver_response(404, "Not Found!", $response);
            exit;
        }
    }
    if ($latitude == $garden['latitude'] && $longitude == $garden['longitude']) {
        if ($type == "temp" || $type == "temperature") {
            $temp_type = array("temperature" => $garden['temp'], "date" => $date);
            $temp = array("garden" => $temp_type);
            deliver_response(200, "Data found!", $temp);
            exit;
        }
        if ($type == "humi" || $type == "humidity") {
            $temp_type = array("humidity" => $garden['humi'], "date" => $date);
            $temp = array("garden" => $temp_type);
            deliver_response(200, "Data found!", $temp);
            exit;
        } else {
            $response = array("status" => array('message' => "Type - '$type' Not Found", 'status_code' => '404'));
            deliver_response(404, "Not Found!", $response);
            exit;
        }
    } else {
        $response = array("status" => array('message' => "Location Not Found", 'status_code' => '404'));
        deliver_response(404, "Not Found!", $response);
        exit;
    }
}
// /api/types
if (isset($_GET['type'])) {
    //proverki / lowercase
    $type = strtolower($_GET['type']);
    if ($type == "temp" || $type == "temperature") {
        $temp = array("server_room" => $server_room['temp'], "garden" => $garden['temp'], "date" => $date);
        $temp = array("temperature" => $temp);
        deliver_response(200, "Data found!", $temp);
        exit;
    }
    if ($type == "humi" || $type == "humidity") {
        $temp = array("server_room" => $server_room['humi'], "garden" => $garden['humi'], "date" => $date);
        $temp = array("humidity" => $temp);
        deliver_response(200, "Data found!", $temp);
        exit;
    }
    if ($type == '0') {
        deliver_response(200, "Data found!", array("types" => array("temperature", "humidity")));
        exit;
    } else {
        $response = array("status" => array('message' => "Type - '$type' Not Found", 'status_code' => '404'));
        deliver_response(404, "Not Found!", $response);
        exit;
    }
}

function deliver_response($status, $status_message, $data) {
    header("HTTP/1.1 $status $status_message");
    $json_response = json_encode($data);
    echo $json_response;
}

?>
