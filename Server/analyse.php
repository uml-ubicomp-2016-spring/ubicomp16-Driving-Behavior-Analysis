<?php
	//read all files
	$dir="./upload/"; //relative path
	
	//PHP traversal files
	$handle=opendir($dir.".");
	//the array is used to store the files' names
	$array_file = array();
	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != "..") 
		{
			$array_file[] = $file; //add file name to the array
		}
	}
	closedir($handle);
	rsort($array_file);
	//print_r($array_file);
	
	//open the file
	$file = fopen('./upload/'.$array_file[0], 'r');
	
	//Counters
	$overSpeedCounter = 0;
	$overAccelCounter = 0;
	$hardBreakCounter = 0;
	$fullStopCounter = 0;
	$totalCounter = 0;
	$max_speed = 0;
	
	//Index
	$TIMESTAMP = 0;
	$LATITUDE = 1;
	$LONGITUDE = 2;
	$RPM = 10;
	$SPEED = 19;
	$ENGINE_RUNTIME = 22;
	$FUEL_ECONOMY = 26;
	
	while($data = fgetcsv($file,1000,';'))
	{
		if(2 <= $totalCounter)
		{
			//echo $data[3];
			//echo implode(";", $data)."<br>";
			$goods_list[] = $data;
			
			$speed = substr($data[19],0, strlen($data[19])-3);
			if($speed>$max_speed)
				$max_speed = $speed;
		}
		$totalCounter = $totalCounter + 1;	
	}
	fclose($file);
	
	$totalCounter = $totalCounter - 3; //don't count the title info
	
	//Go through the dataf
	for($i = 0; $i < $totalCounter; $i++)
	{
		//hardBreak detection
		/*
			
		*/
		
		//full stop detection
		/*
			assume when the speed is 0 and previous data's speed is bigger than 0.
			ignore the traffice congestion
		*/
		if($i>0)
		{
			if($goods_list[$i][$SPEED] == 0 && $goods_list[$i-1][$SPEED])
			{
				$fullStopCounter++;
				//todo
			}
		}
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Driving Behavior Analysis</title>
    <style>
      html, body, #map-canvas {
        height: 800px;
		width: 800px
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
    function initialize() {
    var mapOptions = {
    zoom: 12,
    center: new google.maps.LatLng(<?php echo $goods_list[$totalCounter/2][$LATITUDE]; ?>,<?php echo $goods_list[$totalCounter/2][$LONGITUDE];?>),
    mapTypeId: google.maps.MapTypeId.TERRAIN
    };


    var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

    var flightPlanCoordinates = [
    <?php
	foreach ($goods_list as $arr)
	{
		$lat = $arr[$LATITUDE];
        $lon = $arr[$LONGITUDE];
		echo 'new google.maps.LatLng('.$lat.', '.$lon.'),';
	}
    ?>
    ];

    var flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: '#0033FF',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });
    flightPath.setMap(map);
    }
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    </head>
    <body>
        <div id="map-canvas"></div>
		<div>
		<?php
			echo "Driving duration: ". $goods_list[$totalCounter-1][$ENGINE_RUNTIME]."<br>";
			echo "Max Speed: ".$max_speed."mph<br>";
			echo "full stop count: ".$fullStopCounter."<br>";
		?>
		</div>
    </body>
</html>



