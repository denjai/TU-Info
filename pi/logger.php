<?php
error_reporting(0);
$context = stream_context_create(array('http' => array('header' => 'Connection: close\r\n')));
$file = 'log.txt';
$names_url = "http://localhost/api/names";
$humi_url = "http://localhost/api/types/humi";
$temp_url = "http://localhost/api/types/temp";

$names = json_decode(file_get_contents($names_url), true);
if (!isset($names)) {
    file_put_contents($file,date("d-m-Y H:i")."(server-time)- Can not open url:" . $names_url. ";\n\r", FILE_APPEND | LOCK_EX);
}
// All magic goes here
$output = ob_get_clean();
ignore_user_abort(true);
set_time_limit(0);
header("Connection: close");
header("Content-Length: " . strlen($output));
header("Content-Encoding: none");
echo $output . str_repeat(' ', 10) . "\n\n\n";
flush();

while (true) {
    $humi = json_decode(file_get_contents($humi_url, false, $context), true);
    $temp = json_decode(file_get_contents($temp_url, false, $context), true);
    if (!isset($humi)) {
        file_put_contents($file,date("d-m-Y H:i"). "(server-time)- Can not open url:" . $humi_url . ";\n\r", FILE_APPEND | LOCK_EX);
    }
    if (!isset($temp)) {
        file_put_contents($file,date("d-m-Y H:i"). "(server-time)- Can not open url:" . $temp_url . "!\n\r", FILE_APPEND | LOCK_EX);
    } else {
        if (isset($humi)) {

            $date = $humi['humidity']['date'];
            $datetime = str_pad($date['day'], 2, '0', STR_PAD_LEFT) . "-" . str_pad($date['month'], 2, '0', STR_PAD_LEFT) . "-" . $date['year'] . " " . str_pad($date['hour'], 2, '0', STR_PAD_LEFT) . ":" . str_pad($date['minute'], 2, '0', STR_PAD_LEFT);
            foreach ($names['names'] as $value) {
                file_put_contents($file, $datetime . " - [" . $value . "]   " . "Humidity:" . str_pad($humi['humidity'][$value] . ",", 15) . " " . "Temperature:" . $temp['temperature'][$value] . ";\n\r", FILE_APPEND | LOCK_EX);
            }
        }
    }
    sleep(61);
}
?>

