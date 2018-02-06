






<?php
$mongo = new MongoClient();
$db = $mongo->emergency;
$collection = $db ->service;
$tempo =  $db->tempo;

$cur=$collection->findOne(array("description"=>'log_details'));
foreach ($cur['servicelog'] as $entry) {
  $lol = $tempo -> insert($entry);
}

// construct map and reduce functions
$map = new MongoCode("function() {".
  "var created = this._id.getTimestamp();".
  "var date = created.toISOString().slice(0, 10);".
  "return emit(date, 1);".
"}");
$reduce = new MongoCode("function(key, vals) {".
  "return Array.sum(vals);".
"}");
$result = $db ->command(array("mapreduce" => "tempo","map" => $map,"reduce" => $reduce,"out" => array("inline" => 1)));

$tempo -> drop();

$analysis = $result['results'];



?>



<html>
<head>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var date = new Array();
      '<?php foreach ($analysis as $day) { ?>'
      date.push('<?php echo $day[_id];?>');
      '<?php } ?>'

      var count = new Array();
      '<?php foreach ($analysis as $day) { ?>'
      count.push('<?php echo $day['value'];?>');
      '<?php } ?>'


      var data = new google.visualization.DataTable();
      data.addColumn('number', 'X');
      data.addColumn('number', 'Vehicles');

      var rows = new Array();
      var Header= ['Date', 'Total People Serviced'];
      rows.push(Header);
      for(var i=0;i<date.length;i++)
                        {
                            var data1 = [];

                                data1.push(date[i]);
                                data1.push(parseInt(count[i]));

                                rows.push(data1);

                        }
      var data = google.visualization.arrayToDataTable(rows);

      var options = {
        title: 'Deployment Stats',
        legend: { position: 'bottom' }
      };

      var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

      chart.draw(data, options);

    }
  </script>
</head>
<body>
  <div id="curve_chart" style="width: 900px; height: 500px"></div>
</body>
</html>
