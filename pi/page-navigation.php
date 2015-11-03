<?php
//header('Content-Type: text/html');
error_reporting(0);
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));

$file = 'log.txt';
$names_url = "http://localhost/api/names";
$types_url = "http://localhost/api/types";

$data =file_get_contents($file,false,$context);
$names = json_decode(file_get_contents($names_url,false,$context), true);
$types = json_decode(file_get_contents($types_url,false,$context), true);

if(!$names){
    $error_messages[] = "Can't connect to the Web service(http://localhost/api/names) or data server(http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp) is offline!";
}
if(!$types){
     $error_messages[] = "Can't connect to the Web service(http://localhost/api/types) or data server(http://dsnet.tu-plovdiv.bg/3TierJSP/minimal.jsp) is offline!";
}
/*
if(!$data){
     $error_messages[] = "Can't open the log file:".$file."! Check if the file exists.";
}
 */
if (isset($_GET['id'])) {
if($_GET['id']==1){
$html = '<div id="ex0"></div>
        <div id="chart_errors"><p id="error" class="error">';
       foreach($error_messages as $value){
           $html.=$value."<br>";
       }
     $html .= '</p></div>
        <div id="form">
            <form id="chart" class="basic-grey" action="chart-data.php" enctype="multipart/form-data" method="post">
                <fieldset>
                    <legend>Select time interval and value type:</legend>
                        <label><span>Start date/time:</span>
                        <input id="start-date" class="date_pick" name="start_date" type="text" placeholder="click to select date"> 
                        </label>
                    <label><span>End date/time:</span>
                        <input  id="end-date" class="date_pick" name="end_date" type="text" placeholder="click to select date"></label>
      
                        <label><span>Location:</span>
                        <select name="location" id="select-location" form="chart">
                        <option value="server_room">server room</option>
                        <option value="garden">garden</option>
                        </select>
                    </label>
                    <label><span>Type:</span>
                        <select name="type" form="chart" id="type_select">
                        <option value="temperature">temperature</option>
                        <option value="humidity">humidity</option>
                     </select>
                    </label>
                    <label><span>&nbsp;</span>
                   <input type="submit" class="button" value="Draw Chart"></label>
                </fieldset>
            </form>
        </div>
        ';
      echo $html;
}
if($_GET['id']==0){
    $html = '<div id="map-canvas"></div>
        <div id="marker_info">';
        foreach ($error_messages as $value){
                $html.='<p class="error">'.$value.'</p>';
        }
    $html.='<p id="click">Click on a marker for more information!</p>
            <table class="grid">
                <tr>
                    <th>NAME</th>
                    <th>HUMIDITY</th>
                    <th>TEMPERATURE</th>
                </tr>
                <tr>
                    <td id="marker_name">--</td>
                    <td id="marker_humi">--</td>
                    <td id="marker_temp">--</td>
                </tr>
                
            </table>
        </div>';
    echo $html;
}
}