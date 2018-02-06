<?php





/*

  function vehicle_count ($servicetype,$available)
  {
    $count = 0;
    $mongo = new MongoClient();
    $db = $mongo->emergency;
    $service_db = $db->service->findOne(array("servicetype"=>$servicetype));
    foreach ($service_db['vehicles'] as $vehicle)
    {
      if($vehicle['available']==$available && $vehicle['verified']==TRUE)
        $count=$count + 1;
    }
    return $count;
  }

	$count_a_na=vehicle_count("ambulance",FALSE);
  echo $count_a_na;

  $count_a=vehicle_count("ambulance",TRUE);
  echo $count_a;

  $mongo = new MongoClient();
  $db = $mongo->emergency;
  $service_db = $db->service->findOne(array("servicetype"=>"ambulance"));
  foreach ($service_db['vehicles'] as $vehicle)
  {
    if($vehicle['available']==FALSE)
      $lol = $vehicle;
  }
  $collection->update(array('vehicles.username'=>$lol['username']),array('$set'=>array('vehicles.$.available' => TRUE)));
*/



//$mongo = new MongoClient();
//$db = $mongo->emergency;
/*$ops = array(
    array(
        '$project' => array(
            "description" => 1,
            "vehicles"   => 1,
        )
    ),
    array('$unwind' => '$vehicles'),
    array(
        '$group' => array(
            "_id" => array("vehicles" => '$vehicles'),
            'count' => array('$sum' => 1)
        )

    ),
    array('$match'=> array("count"=>("vehicles.available"=>FALSE)) )

);*/

      /*$ops=array(array('$unwind'=>'$vehicles'),array('$match'=>array('vehicles.available'=>FALSE,'vehicles.verified'=>TRUE)),
                 array('$group' =>array('_id' =>NULL,'count'=>array('$sum'=>1) )));
*/

/*
function vehicle_count ($servicetype,$available)
{
  $mongo = new MongoClient();
  $db = $mongo->emergency;
  $ops=array(array('$unwind'=>'$vehicles'),array('$match'=>array('description'=>'ambulance','vehicles.available'=>$available)),
             array('$group' =>array('_id' =>NULL,'count'=>array('$sum'=>1) )));
  $cur=$db->service->aggregate($ops);
  $curs=$cur['result'];
  foreach ($curs as $doc)
  {
    $count = $doc['count'];
  }
  return $count;
}

$count_a_na=vehicle_count("ambulance",FALSE);
$count_a=vehicle_count("ambulance",TRUE);

echo $count_a_na;
echo $count_a;*/?>
<?php
$mongo = new MongoClient();
$db = $mongo->emergency;
$service_db = $db->service->findOne(array("description"=>"log_details"));
  $pin_a=0;
  $pin_p=0;
  $pin_f=0;
  $pinc_a=array();
  $pinc_p=array();
  $pinc_f=array();
  $pin1=array();
  $i=0;
  foreach ($service_db['servicelog'] as $servicelog)
  {
        $pin1[]=$servicelog['pincode'];
        $pin1 = array_unique($pin1);
  }
  $pin1=array_values($pin1);

  $le = count($pin1);
  while($i<$le)
  {
    foreach($service_db['servicelog'] as $servicelog)
    {
        if($servicelog['pincode']==$pin1[$i])
        {
          if($servicelog['servicetype']=='ambulance')
          {
            $pin_a=$pin_a+1;
          }
          if($servicelog['servicetype']=='police')
          {
            $pin_p=$pin_p+1;
          }
          if($servicelog['servicetype']=='firebrigade')
          {
            $pin_f=$pin_f+1;
          }
        }
    }
    $pinc_a[]=$pin_a;
    $pinc_p[]=$pin_p;
    $pinc_f[]=$pin_f;
    $pin_a=0;
    $pin_p=0;
    $pin_f=0;
    $i=$i+1;
  }
?>


    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {

        var pin1 = new Array();
        '<?php foreach($pin1 as $key => $val){ ?>'
        pin1.push('<?php echo $val; ?>');
        '<?php } ?>'
        var pinc_a = new Array();
        '<?php foreach($pinc_a as $key => $val){ ?>'
        pinc_a.push('<?php echo $val; ?>');
        '<?php } ?>'
        var pinc_p = new Array();
        '<?php foreach($pinc_p as $key => $val){ ?>'
        pinc_p.push('<?php echo $val; ?>');
        '<?php } ?>'
        var pinc_f = new Array();
        '<?php foreach($pinc_f as $key => $val){ ?>'
        pinc_f.push('<?php echo $val; ?>');
        '<?php } ?>'


        var rows = new Array();
        var Header= ['Pincode', 'Ambulance', 'Police','Fire-Brigade'];
        rows.push(Header);
        for(var i=0;i<pin1.length;i++)
                          {
                              var data1 = [];

                                  data1.push(pin1[i]);
                                  data1.push(parseInt(pinc_a[i]));
                                  data1.push(parseInt(pinc_p[i]));
                                  data1.push(parseInt(pinc_f[i]));

                                  rows.push(data1);

                          }
        var data = new google.visualization.arrayToDataTable(rows);

        var options = {
          'title': 'Deployment Stats',
          colors: ['#55ff33', '#3346ff', '#ff0000'],
          width:500,
          animation:{
            startup:true,
        duration: 2000,
        easing: 'inAndOut'
      }
        };

      var chart = new google.charts.Bar(document.getElementById('dual_y_div'));
      chart.draw(data, options);
    };
    </script>

    <div id="dual_y_div" style="width: 900px; height: 50px;"></div>
