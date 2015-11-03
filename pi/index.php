<?php
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
error_reporting(0);
$names_url = "http://localhost/api/names";
$names = json_decode(file_get_contents($names_url,false,$context), true);
$data_array;
if($names){
foreach ($names["names"] as $key => $value) {
    $temp_url = $names_url . "/" . $value;
    $data = json_decode(file_get_contents($temp_url,false,$context), true);
    $data_array[] = $data;
}
}
// TO DO error if cant open file/url
?>  
<!DOCTYPE html>
<html>
    <head>
        <title>Map info</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="datetimepicker/jquery.datetimepicker.css"/>
        <link rel="stylesheet" type="text/css" href="css/default.css">
 
        <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="js/application.js"></script>

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBj3rdY41GzJJcNL4eNni5v8-etZIuTS30"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
        <script type="text/javascript" src="datetimepicker/jquery.js"></script>
        <script type="text/javascript" src="datetimepicker/jquery.datetimepicker.js"></script>
        <script type="text/javascript">
   
        function initialize(){
<?php
//echo "var myLatlng = new google.maps.LatLng(" . $location['garden']['latitude'] . "," . $location['garden']['longitude'] . ");";

//echo "var temperature1=" . $data_array[0]['temp'] . ";";
//echo "var humidity1=" . $data_array[0]['humi'] . ";";
?>

                var mapOptions = {
                    center: {lat: 42.139175, lng: 24.772743},
                    zoom: 15
                };
                var map = new google.maps.Map(document.getElementById('map-canvas'),
                        mapOptions);
<?php
$n = 1;
foreach ($data_array as $key => $value) {
    echo "var myLatlng" . $n . "= new google.maps.LatLng(" . $value['latitude'] . "," . $value['longitude'] . ");";

    echo "var marker" . $n . " = new google.maps.Marker({
                         position: myLatlng" . $n . ",
                         map: map,
                         title:" . "'" . $value['name'] . "'" .
    "});";
    
    echo "google.maps.event.addListener(marker".$n.", 'click', function () {
                   update_marker('".$value['name']."'); 
                });";
    $n++;
}
?> 
 }
        google.maps.event.addDomListener(window, 'load', initialize);
      </script>

    </head>
    <body>
        <header></header>
        <nav  id='header'>
            <a id="show-map" class="nav"  href="page-navigation.php?id=0">Map</a> |
            <a id='show-chart' class="nav"  href="page-navigation.php?id=1">Chart</a> |
            <a id="show-stats" class="nav"  href=#>Statistics</a>
        </nav>
        <div id='wrapper'>
        <div id="map-canvas"></div>
        <div id="marker_info">
            <p>
             <?php
                if(!$names){
                    $error_messages = "Can't connect to the Web service(http://localhost/api/names) or data server(http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp) is offline!";
                    echo '<p class="error">'.$error_messages.'</p>';
                }
                if(!$data){
                    $error_messages = "Can't connect to the Web service(http://localhost/api/names/xxx) or data server(http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp) is offline!";
                    echo '<p class="error">'.$error_messages.'</p>';
                }
                ?>
            </p>
            <p id='click'>Click on a marker for more information!</p>
            <table class="grid">
                <tr>
                    <th>NAME</th>
                    <th>HUMIDITY</th>
                    <th>TEMPERATURE</th>
                </tr>
                <tr>
                    <td id='marker_name'>--</td>
                    <td id="marker_humi">--</td>
                    <td id="marker_temp">--</td>
                </tr>
              
            </table>
        </div>
       </div>
       
        <footer>Information about temperature and humidity from: <a href="http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp">http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp</a>
            using restful API!
        </footer>
         <div class="modal"><!-- loading animation --></div>
    </body>
</html>