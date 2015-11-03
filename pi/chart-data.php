<?php

header('Content-Type: application/json');
error_reporting(0);
$context = stream_context_create(array('http' => array('header' => 'Connection: close\r\n')));

$file = "log.txt";

if (!(file_exists($file))) {
    $error_messages[] = "Can't open the log file '" . $file . "'! File doesn't exist";
    $response = array("errors" => $error_messages);
    deliver_response(200, "OK", $response);
    exit;
}
//read log.txt
$data = file_get_contents($file, false, $context);
//expressions match name/type 
$datetime = "([0-9]*\-[0-9]*\-[0-9]*\s[0-9]*\:[0-9]*)";
$name = ".*\[(.*)\]";
$humidity = ".*Humidity\:(.*),";
$temperature = ".*Temperature\:(.*)\;";
$start_index;


if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $name = ".*\[" . $_POST['location'] . "\]";
    if ($_POST['type'] == "temperature") {
        $humidity = ".*Humidity\:.*\,";
    } else {
        $temperature = ".*Temperature\:.*\;";
    }
    if (preg_match_all("/" . $datetime . $name . $humidity . $temperature . "/", $data, $matches)) {
        //echo $matches[1][1]."<br>";
        //echo $matches[2][1]."<br>";
    } else {
        $error_messages[] = array('message' => "Can't find data in  the log file '" . $file . "'!");
        $response = array("errors" => $error_messages);
        deliver_response(200, "OK", $response);
        exit;
    }
    //---------------------------------
    $start_date = new DateTime($start_date);
    $end_date = new DateTime($end_date);
    
    if ($start_date > $end_date) {
        $error_messages[] = "Selected start date is after the end date! Select another dates.";
        $response = array("errors" => $error_messages);
        deliver_response(200, "OK", $response);
        exit;
    }
     if ($start_date == $end_date) {
        $error_messages[] = "Selected start date is the same as the end date! Select another dates.";
        $response = array("errors" => $error_messages);
        deliver_response(200, "OK", $response);
        exit;
    }
    //obhojdane na masiva i sravnqvane na chas i vreme
    $array_count = count($matches[1]);
    for ($i = 0; $i < $array_count; $i++) {
        $temp_date = new DateTime($matches[1][$i]);
        if ($temp_date >= $start_date) {
            if (!(isset($start_index))) {
                $start_index = $i;
            }
        }
        if ($temp_date <= $end_date) {
            $end_index = $i;
        } else {
            break;
        }
    }
    //if isset start and notset end..
    if (isset($start_index) && isset($end_index)) {
        for ($i = $start_index; $i <= $end_index; $i++) {
            if (0 != floatval($matches[2][$i])) {
                $values[] = array("datetime" => $matches[1][$i], "data" => $matches[2][$i]);
            } else {
                $warning[] = array('message' => "There is invalid data values in the selected time period in  the log file '" . $file . "'!");
            }
        }
        $response = array("values" => $values,);
    } else {
        if (!isset($start_index)) {
            $error_messages[] = "Can't find the selected period in the log file '" . $file . "'! Please select erlier period.";
        }
        if (!isset($end_index)) {
            $error_messages[] = "Can't find the selected period in the log file '" . $file . "'!Please select later period.";
        }
        $response = ["errors" => $error_messages];
    }
    //vrushtane na masiv v JSON
    deliver_response(200, "OK", $response);
}

function deliver_response($status, $status_message, $data) {
    header("HTTP/1.1 $status $status_message");
    $json_response = json_encode($data);
    echo $json_response;
}

?>