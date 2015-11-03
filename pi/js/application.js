
$(document).ready(function () {
    function get_statistics() {
        $.getJSON("statistics.php", function (json_data) {
            var html = '';
            if('errors' in json_data){
            html='';
                $.each(json_data.errors, function (i, value) {
                    html+="<p class='error'>Error:"+value+"</p><br>";
                });
                $('#min-max-data').html(html);

            }
            else{
            html += "<table class='grid' id='table-l'>";
            html += "<caption>" + "SERVER ROOM" + "</caption>";
            html += "<tr><th></th><th>MIN</th><th>AVERAGE</th><th>MAX</th></tr>";
            item = json_data.server_room;
            html +=
                    "<tr>" +
                    "<td>Humidity</td>" +
                    "<td>" + item.humidity[0] + "</td>" +
                    "<td>" + item.humidity[1] + "</td>" +
                    "<td>" + item.humidity[2] + "</td>" +
                    "</tr>";
            html +=
                    "<tr>" +
                    "<td>Temperature</td>" +
                    "<td>" + item.temperature[0] + "</td>" +
                    "<td>" + item.temperature[1] + "</td>" +
                    "<td>" + item.temperature[2] + "</td>" +
                    "</tr>";
            html += "</table>";
            //garden table

            html += "<table class='grid' id='table-r'>";
            html += "<caption>" + "GARDEN" + "</caption>";
            html += "<tr><th></th><th>MIN</th><th>AVERAGE</th><th>MAX</th></tr>";
            item = json_data.garden;
            html +=
                    "<tr>" +
                    "<td>Humidity</td>" +
                    "<td>" + item.humidity[0] + "</td>" +
                    "<td>" + item.humidity[1] + "</td>" +
                    "<td>" + item.humidity[2] + "</td>" +
                    "</tr>";
            html +=
                    "<tr>" +
                    "<td>Temperature</td>" +
                    "<td>" + item.temperature[0] + "</td>" +
                    "<td>" + item.temperature[1] + "</td>" +
                    "<td>" + item.temperature[2] + "</td>" +
                    "</tr>";
            html += "</table>";
            //html element content
            $('#min-max-data').html(html);
        }
        });
        setTimeout(get_statistics, 60000);
    }


    $(document).on('submit', '#chart', function (e) {
        $.post($(this).attr('action'), $(this).serialize(), function (data) {
            if('errors' in data){
                html='';
                $.each(data.errors, function (i, value) {
                    html+="Error: "+value+"<br>";
                });
                $('#error').html(html);
                console.log(data.errors);
            }
            else{
                $('#error').html('');
            var type_index = $("#type_select")[0].selectedIndex;
            var type_options = $("#type_select")[0].options;
            //console.log(type_options[type_index]);
            type_value = type_options[type_index].value;

            var chart_data = [];
            var dates = [];
            $.each(data.values, function (i, value) {
                var temp_array = [];
                //temp_array.push(i);
                temp_array.push(parseDatetime(value.datetime));
                dates.push(value.datetime);
                temp_array.push(parseFloat(value.data));
                //console.log( value.data );
                chart_data.push(temp_array);
            }
            );
            //console.log(chart_data);
            ////////////////////////////////////////
            google.load('visualization', '1', {packages: ['corechart']});
            //google.setOnLoadCallback(drawChart);
            //drawChart(chart_data);

            var data = new google.visualization.DataTable();
            data.addColumn('datetime', 'time');
            data.addColumn('number', type_value);
            data.addRows(chart_data);

            var options = {
                //width: 1000,
                //height: 563,
                hAxis: {
                    title: 'Time',
                    format: 'd-M-yyyy H:m'
                            //ticks: temp2
                },
                vAxis: {
                    title: type_value
                }
            }; 
            var chart = new google.visualization.LineChart(
                    document.getElementById('ex0'));

            chart.draw(data, options);
            }
            ///////////////////////////////////////
        });
        return false;
    });
    $(document).on('click', '#show-chart', function (e) {
        $.get($(this).attr('href'), function (data) {
            $('#wrapper').html(data);
            drawChartExample();
            $('.date_pick').datetimepicker({
                format: 'd-m-Y H:i',
                formatTime: 'H:i',
                formatDate: 'd-m-Y',
                step: 5,
                defaultDate: '30-12-2014',
                defaultTime: '17:30'
            });
        });
        return false;
    });
    $(document).on('click', '#show-map', function (e) {
        $.get($(this).attr('href'), function (data) {
            $('#wrapper').html(data);
            initialize();
        });
        return false;
    });
    $(document).on('click', '#show-stats', function (e) {

        var data = '<div id="min-max-data"></div>';
        $('#wrapper').html(data);
        get_statistics();
        //return false;
    });

    function parseDatetime(date) {
        dtsplit = date.split(/[\/ -.:]/);
        // creates assoc array for date
        dt = new Array();
        for (i = 0; i < 5; i++) {
            dt[i] = parseInt(dtsplit[i]);
        }
        //console.log(dt);
        var parsedDate = new Date(dt[2], dt[1] - 1, dt[0], dt[3], dt[4]);
        //console.log(parsedDate);
        return parsedDate;
    }

$(document).on({
    ajaxStart: function() { $("body").addClass("loading");   },
    ajaxStop: function() { $("body").removeClass("loading"); }    
});

});
    //update/show- marker information
function update_marker(name) {
    href = "update-marker-info.php?name=" + name;
    $.get(href, function (data) {
        $('#marker_name').html(data.name);
        $('#marker_humi').html(data.humi);
        $('#marker_temp').html(data.temp);
    });
    return false;
}
function drawChartExample() {

    var data = new google.visualization.DataTable();
    data.addColumn('number', 'X');
    data.addColumn('number', 'Example');

    data.addRows([
        [0, 0], [1, 10], [2, 23],
        [3, 17], [4, 18], [5, 9],
        [6, 11], [7, 27], [8, 33],
        [9, 40], [10, 32], [11, 35],
        [12, 30], [13, 40], [14, 42],
        [15, 47], [16, 44], [17, 48],
        [18, 52], [19, 54], [20, 42],
        [21, 55], [22, 56], [23, 57],
        [24, 60], [25, 50], [26, 52],
        [27, 51], [28, 49], [29, 53],
        [30, 55], [31, 60], [32, 61],
        [33, 59], [34, 62], [35, 65],
        [36, 62], [37, 58], [38, 55],
        [39, 61], [40, 64], [41, 65],
        [42, 63], [43, 66], [44, 67],
        [45, 69], [46, 69], [47, 70],
        [48, 72], [49, 68], [50, 66],
        [51, 65], [52, 67], [53, 70],
        [54, 71], [55, 72], [56, 73],
        [57, 75], [58, 70], [59, 68],
        [60, 64], [61, 60], [62, 65],
        [63, 67], [64, 68], [65, 69],
        [66, 70], [67, 72], [68, 75],
        [69, 80]
    ]);

    var options = {
        //width: 'auto',
        //height: 600,
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Example'
        }
    };

    var chart = new google.visualization.LineChart(
            document.getElementById('ex0'));

    chart.draw(data, options);

}
//declare function
//function initialize(){};