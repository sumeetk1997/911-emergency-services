<?php
  session_start();

  if ($_SESSION['user']!='admin')
  {
    header('Location: index.php');
  }

  include 'header_admin.html';
  include 'config.php';

  function gender_count ($gender)
  {
    $mongo = new MongoClient();
    $db = $mongo->emergency;
    $ops=array(array('$unwind'=>'$servicelog'),
               array('$match'=>array('description'=>'log_details','servicelog.gender'=>$gender)),
               array('$group' =>array('_id' =>NULL,'count'=>array('$sum'=>1) )));
    $cur=$db->service->aggregate($ops);
    $curs=$cur['result'];

    foreach ($curs as $doc)
    {
      $count = $doc['count'];
    }
    return $count;
  }


  $count = 0;
  $add=0;
  $i=1;
  $mongo = new MongoClient();
  $db = $mongo->emergency;
  $service_db = $db->service->findOne(array("description"=>"log_details"));
  foreach ($service_db['servicelog'] as $servicelog)
  {
    if(($servicelog['Release_Timestamp'])!= NULL)
    {
      if($servicelog['servicetype']=='ambulance')
      {
        $time1=strtotime($servicelog['Deploy_Timestamp']['date']);
        $time2=strtotime($servicelog['Release_Timestamp']['date']);
        $interval = abs($time2-$time1)/60;
        $add_a=$add_a+$interval;
        $count_a=$count_a+1;
      }
      if($servicelog['servicetype']=='police')
      {
        $time1=strtotime($servicelog['Deploy_Timestamp']['date']);
        $time2=strtotime($servicelog['Release_Timestamp']['date']);
        $interval = abs($time2-$time1)/60;
        $add_p=$add_p+$interval;
        $count_p=$count_p+1;
      }
      if($servicelog['servicetype']=='firebrigade')
      {
        $time1=strtotime($servicelog['Deploy_Timestamp']['date']);
        $time2=strtotime($servicelog['Release_Timestamp']['date']);
        $interval = abs($time2-$time1)/60;
        $add_f=$add_f+$interval;
        $count_f=$count_f+1;
      }
    }
  }
  foreach ($service_db['servicelog'] as $servicelog)
  {
    if(($servicelog['Reached_Timestamp'])!= NULL)
    {
      $time1=strtotime($servicelog['Deploy_Timestamp']['date']);
      $time2=strtotime($servicelog['Reached_Timestamp']['date']);
      $time3=$servicelog['estimated_duration'];
      $interval = abs($time2-$time1)/60;

      if($servicelog['servicetype']=='ambulance')
      {

        $add1_a=$add1_a+$interval;
        $est_a=$est_a+$time3;
        $count1_a=$count1_a+1;
      }
      if($servicelog['servicetype']=='police')
      {

        $add1_p=$add1_p+$interval;
        $est_p=$est_p+$time3;
        $count1_p=$count1_p+1;
      }
      if($servicelog['servicetype']=='firebrigade')
      {

        $add1_f=$add1_f+$interval;
        $est_f=$est_f+$time3;
        $count1_f=$count1_f+1;
      }
    }
  }
  $avg_a=$add_a/$count_a;
  $avg_p=$add_p/$count_p;
  $avg_f=$add_f/$count_f;
  $avg1_a=$add1_a/$count1_a;
  $avg1_p=$add1_p/$count1_p;
  $avg1_f=$add1_f/$count1_f;
  $avg2_a=$est_a/$count1_a;
  $avg2_p=$est_p/$count1_p;
  $avg2_f=$est_f/$count1_f;

  $count_m=gender_count("Male");
  $count_f=gender_count("Female");


  $collection = $db ->service;
  $tempo =  $db->tempo;

  $cur=$collection->findOne(array("description"=>'log_details'));
  foreach ($cur['servicelog'] as $entry)
  {
    $temp = $tempo -> insert($entry);
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

<div class="col l12">
	<div class="row">
		<div class="card" style="width: 60%; height:40%; float:top; margin:auto; margin-top:10px" >
			<div class="card-content black-text">
				<div class="row">
          <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
          <div class="reload" id="chart_div" style="height:33%;"></div>
          <div id="chart_div1"></div>
          <div id="chart_div2"></div>
          <div id="curve_chart" style="width: 1050px; height: 500px"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawBasic1);
  google.charts.setOnLoadCallback(drawBasic2);
  google.charts.setOnLoadCallback(drawMultSeries);
  function drawBasic1()
  {
    var count_m = parseFloat("<?php echo $count_m; ?>");
    var count_f = parseFloat("<?php echo $count_f; ?>");
    var data = google.visualization.arrayToDataTable([
          ['gender', 'males','females'],
          ['Gender', count_m,count_f],
        ]);
    var options = {
          'title': 'number of male and females serviced',
          colors: ['#3346ff', '#ff00d1'],
          animation:{
                      startup:true,
                      duration: 2000,
                      easing: 'inAndOut'
                    },
          'chartArea': {'width': '50%'},
          'hAxis': {
                    'title': 'Total number',
                    'minValue': 0
                   },
          'vAxis': {
                    title: 'gender type'
                   }
    };
    var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }

  function drawBasic2()
  {
    var avg_a = parseFloat("<?php echo $avg_a; ?>");
    var avg_p = parseFloat("<?php echo $avg_p; ?>");
    var avg_f = parseFloat("<?php echo $avg_f; ?>");
    var data = google.visualization.arrayToDataTable([
              ['servicetype', 'ambulance','police','firebrigade'],
              ['service', avg_a,avg_p,avg_f],
            ]);
    var options = {
          'title': 'average time',
          colors: ['#55ff33', '#3346ff', '#ff0000'],
          animation:{
                      startup:true,
                      duration: 2000,
                      easing: 'inAndOut'
                    },
          'chartArea': {'width': '50%'},
          'hAxis': {
                    'title': 'average time(in min.)',
                    'minValue': 0
                   },
          'vAxis': {
                      title: 'service type'
                   }
    };
    var chart = new google.visualization.BarChart(document.getElementById('chart_div1'));
    chart.draw(data, options);
  }

  function drawMultSeries()
  {
    var avg1_a = parseFloat("<?php echo $avg1_a; ?>");
    var avg1_p = parseFloat("<?php echo $avg1_p; ?>");
    var avg1_f = parseFloat("<?php echo $avg1_f; ?>");
    var avg2_a = parseFloat("<?php echo $avg2_a; ?>");
    var avg2_p = parseFloat("<?php echo $avg2_p; ?>");
    var avg2_f = parseFloat("<?php echo $avg2_f; ?>");
    var data = google.visualization.arrayToDataTable([
                          ['Service Type', 'Actual Reach Time', 'Estimated Reach Time'],
                          ['Ambulance', avg1_a, avg2_a],
                          ['Police', avg1_p, avg2_p],
                          ['Fire-Brigade', avg1_f, avg2_f]
                        ]);
    var options = {
                    title: 'Average time taken',
                    chartArea: {width: '70%'},
                    animation:{
                                startup:true,
                                duration: 2000,
                                easing: 'inAndOut'
                              },
                    colors: ['#ff0000', '#55ff33'],
                    hAxis: {
                              title: 'Time(in minutes)',
                              minValue: 0
                            },
                    vAxis: {
                              title: 'Service type'
                            }
                  };
    var chart = new google.visualization.BarChart(document.getElementById('chart_div2'));
    chart.draw(data, options);
  }

  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart()
  {
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
                    animation:{
                                startup:true,
                                duration: 2000,
                                easing: 'inAndOut'
                              },
                    title: 'Deployment Stats',
                    legend: { position: 'bottom' }
                  };

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    chart.draw(data, options);
  }
</script>
