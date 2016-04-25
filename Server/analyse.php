<?php

	//Counters
	$overSpeedCounter = 0;
	$overAccelCounter = 0;
	$hardBreakCounter = 0;
	$fullStopCounter = 0;
	$totalCounter = 0;
	$max_speed = 0;
	$maxDrivingDuration = "00:00:00";
	
	//Index
	$TIMESTAMP = 0;
	$LATITUDE = 1;
	$LONGITUDE = 2;
	$RPM = 10;
	$SPEED = 19;
	$ENGINE_RUNTIME = 22;
	//$FUEL_ECONOMY = 26;
	
	//0：last time status is increase;1: last time status is decrease; 3: even ; 4:over increase; 5:over decrese
	$INCREASE = 0;
	$DECREASE = 1;
	$EVEN = 3;
	$OVER_INCREASE = 4;
	$OVER_DECREASE = 5;
	
	//Boundary, this value should be get from the experiment
	$AcceMax = 5;
	$BreakMax = 5;
	
	//Fule comsumption
	$maxFuleComsmption = 0;
	$minFuleComsmption = 0;
	$avgFuleComsmption = 0;
	
	//coordinate list
	$fullStopCoList = array();
	$hardBreakCoList = array();
	$overAccelCoList = array();
	
	//read all files
	$dir="./upload/"; 
	
	$handle=opendir($dir."."); //PHP traversal files
	$array_file = array(); //the array is used to store the files' names
	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != "..") 
		{
			$array_file[] = $file;
		}
	}
	closedir($handle);

	//debug
	//print_r($array_file);
	
	rsort($array_file);
	//open the newest file $array_file[0]
	$file = fopen('./upload/'.$array_file[0], 'r');
	
	$counter = 0;
	while($data = fgetcsv($file,1000,';'))
	{
		if(2 <= $counter)
		{
			//clean data
			if($data[$TIMESTAMP]!=null && $data[$TIMESTAMP]!="NODATA" && $data[$TIMESTAMP]!="null" && $data[$TIMESTAMP]!=""
			&& $data[$LATITUDE]!=null && $data[$LATITUDE]!="NODATA" && $data[$LATITUDE]!="null" && $data[$LATITUDE]!=""
			&& $data[$LONGITUDE]!=null && $data[$LONGITUDE]!="NODATA" && $data[$LONGITUDE]!="null" && $data[$LONGITUDE]!=""
			&& $data[$RPM]!=null && $data[$RPM]!="NODATA" && $data[$RPM]!="null" && $data[$RPM]!=""
			&& $data[$SPEED]!=null && $data[$SPEED]!="NODATA" && $data[$SPEED]!="null" && $data[$SPEED]!=""
			&& $data[$ENGINE_RUNTIME]!=null && $data[$ENGINE_RUNTIME]!="NODATA" && $data[$ENGINE_RUNTIME]!="null" && $data[$ENGINE_RUNTIME]!="")
			{
				//debug
				//echo implode(";", $data)."<br>";

				$goods_list[] = $data;

				$speed = substr($data[$SPEED],0, strlen($data[$SPEED])-3);
				if($speed>$max_speed)
					$max_speed = $speed;
				//test
				//echo $data[$SPEED]."<br>";
				
				if($data[$ENGINE_RUNTIME]!=null && $data[$ENGINE_RUNTIME]!="NODATA" && $data[$ENGINE_RUNTIME]!="null")
				{
					$drivingDur = $data[$ENGINE_RUNTIME];
					if($drivingDur>$maxDrivingDuration)
						$maxDrivingDuration = $drivingDur;
				}
				
				$totalCounter++;
			}
		}
		$counter++;
	}
	fclose($file);
	
	//full stop detection
	$buff0 = $goods_list[0];
	$buff1 = $goods_list[0];
	for($i = 0; $i < $totalCounter; $i++)
	{	
		/*
			assume when the speed is 0 and previous data's speed is bigger than 0.
		*/
		if($i>0)
		{
			if(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3)) == 0.00 
			&& substr($goods_list[$i-1][$SPEED],0,count($goods_list[$i][$SPEED]-3)) > 0 )
			{
				
				$fullStopCounter = $fullStopCounter + 1;;
				//add Co to the list 
				if($goods_list[$i][$LATITUDE]<>$goods_list[$i-1][$LATITUDE] || $goods_list[$i][$LONGITUDE]<>$goods_list[$i-1][$LONGITUDE])
				{
					$buff0 = $goods_list[$i-1];
					$buff1 = $goods_list[$i];
				}
				if($goods_list[$i][$LATITUDE]==$goods_list[$i-1][$LATITUDE] && $goods_list[$i][$LONGITUDE]==$goods_list[$i-1][$LONGITUDE])
				{
					for($j=$i; $j>0; $j--)
					{
						if($goods_list[$i][$LATITUDE]<>$goods_list[$j][$LATITUDE] || $goods_list[$i][$LONGITUDE]<>$goods_list[$j][$LONGITUDE])
						{
							$buff0 = $goods_list[$j];
							$buff1 = $goods_list[$i];
							break;
						}
					}
				}
				
				$fullStopCoList[] = array($buff0, $buff1);
			}
		}	
	}
	
	$flag=$EVEN;//0：last time status is increase;1: last time status is decrease; 3: even ; 4:over increase; 5:over decrese
	$overAccelCo = array();
	$hardBreakCo = array();
	for($i = 0; $i < $totalCounter-2; $i++)
	{
		//hardBreak and over acceleration detection
		/*
			state machine
		*/
		//if(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))==0)
		//	continue;
		
		//increase
		if(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
			<substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3))
			&&(substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3))-
				substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3)))<$AcceMax)
		{
			//add $overAccelCo to the $overAccelCoList then clean $overAccelCo;
			if($flag == $OVER_DECREASE)
			{
				$hardBreakCoList[] = $hardBreakCo;
				unset($hardBreakCo); 
			}
			//add $hardBreakCo to the $hardBreakCoList then clean $hardBreakCo;
			if($flag == $OVER_INCREASE)
			{
				$overAccelCoList[] = $overAccelCo;
				unset($overAccelCo);
			}
			
			$flag = $INCREASE;
		}
		
		//over increase
		if(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
			<substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3)))
		{
			if((substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3))-
			substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3)))>$AcceMax && flag!=$OVER_INCREASE)
			{
				$overAccelCounter++;
				
				//add $hardBreakCo to the $hardBreakCoList then clean $hardBreakCo; 
				if($flag == $OVER_DECREASE)
				{
					$hardBreakCoList[] = $hardBreakCo;
					unset($hardBreakCo); 
				}

				//add Co to the $overAccelCo
				if($goods_list[$i][$LATITUDE]==$goods_list[$i+1][$LATITUDE] && $goods_list[$i][$LONGITUDE]==$goods_list[$i+1][$LONGITUDE])
				{
					for($j=$i; $j<count($goods_list)-i; $j++)
					{
						if($goods_list[$i][$LATITUDE]<>$goods_list[$j][$LATITUDE] || $goods_list[$i][$LONGITUDE]<>$goods_list[$j][$LONGITUDE])
						{
							$overAccelCo[] = $goods_list[$i];
							$overAccelCo[] = $goods_list[$j];
							break;
						}
					}
				}
				else
				{
					$overAccelCo[] = $goods_list[$i];
					$overAccelCo[] = $goods_list[$i+1];
				}
				
				$flag = $OVER_INCREASE;
			}
			
			if((substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3))-
			substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3)))>$AcceMax && flag==$OVER_INCREASE)
			{
				//add Co to the $overAccelCo
				$overAccelCo[] = $goods_list[$i+1];
			}
		}
		
		//decrease
		if(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
			>substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3))
			&&(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
				-substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3)))<$BreakMax)
		{
		
			//add $hardBreakCo to the $hardBreakCoList then clean $hardBreakCo;
			if($flag == $OVER_DECREASE)
			{
				$hardBreakCoList[] = $hardBreakCo;
				unset($hardBreakCo); 
			}
			
			//add $overAccelCo to the $overAccelCoList then clean $overAccelCo;
			if($flag == $OVER_INCREASE)
			{
				$overAccelCoList[] = $overAccelCo;
				unset($overAccelCo);
			}
			
			$flag = $DECREASE;
		}
		
		//over decrease
		if(substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
			>substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3)))
		{
			if((substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
				-substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3)))>$BreakMax && flag!=$OVER_DECREASE)
			{
				
				$hardBreakCounter++;;
				//add $overAccelCo to the $overAccelCoList then clean $overAccelCo; 
				if($flag == $OVER_INCREASE)
				{
					$overAccelCoList[] = $overAccelCo;
					unset($overAccelCo);
				}
				
				//add Co to the $hardBreakCo
				if($goods_list[$i][$LATITUDE]==$goods_list[$i+1][$LATITUDE] && $goods_list[$i][$LONGITUDE]==$goods_list[$i+1][$LONGITUDE])
				{
					for($j=$i; $j<count($goods_list)-i; $j++)
					{
						if($goods_list[$i][$LATITUDE]<>$goods_list[$j][$LATITUDE] || $goods_list[$i][$LONGITUDE]<>$goods_list[$j][$LONGITUDE])
						{
							$hardBreakCo[] = $goods_list[$i];
							$hardBreakCo[] = $goods_list[$j];
							break;
						}
					}
				}
				else
				{
					$hardBreakCo[] = $goods_list[$i];
					$hardBreakCo[] = $goods_list[$i+1];
				}
				
				$flag = $OVER_DECREASE;
			}
			if((substr($goods_list[$i][$SPEED],0,count($goods_list[$i][$SPEED]-3))
				-substr($goods_list[$i+1][$SPEED],0,count($goods_list[$i+1][$SPEED]-3)))>$BreakMax && flag==$OVER_DECREASE)
			{
				//add Co to the $hardBreakCo
				$hardBreakCo[] = $goods_list[$i+1];
			}
			
		}
		
		//even
		if($goods_list[$i][$SPEED]==$goods_list[$i+1][$SPEED])
		{
			
			//add $overAccelCo to the $overAccelCoList then clean $overAccelCo;
			if($flag == $OVER_DECREASE)
			{
				$hardBreakCoList[] = $hardBreakCo;
				unset($hardBreakCo); 
			}
			//add $hardBreakCo to the $hardBreakCoList then clean $hardBreakCo;
			if($flag == $OVER_INCREASE)
			{
				$overAccelCoList[] = $overAccelCo;
				unset($overAccelCo);
			}
			
			$flag = $EVEN;
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
		width: 1400px
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
	
	//mark full stop
	<?php
		
		for($i=0; $i<count($fullStopCoList); $i++)
		{
			echo "var flightPlanCoordinatesFullStop".$i." = [
				new google.maps.LatLng(".$fullStopCoList[$i][0][$LATITUDE].", ".$fullStopCoList[$i][0][$LONGITUDE]."),
				new google.maps.LatLng(".$fullStopCoList[$i][1][$LATITUDE].", ".$fullStopCoList[$i][1][$LONGITUDE]."),
				];";
				
			echo "var flightPathFullStop".$i." = new google.maps.Polyline({
				path: flightPlanCoordinatesFullStop".$i.",
				geodesic: true,
				strokeColor: '#FF0000',
				strokeOpacity: 1.0,
				strokeWeight: 5
				});
				flightPathFullStop".$i.".setMap(map);";
		}
	?>
	
	//mark hard break 
	<?php
	for($i=0; $i<count($hardBreakCoList); $i++)
		{
			$str = "";
			for($j=0; $j<count($hardBreakCoList[$i]); $j++)
			{
				$str = $str."new google.maps.LatLng(".$hardBreakCoList[$i][$j][$LATITUDE].",".$hardBreakCoList[$i][$j][$LONGITUDE]."),";
			}
			echo "var flightPlanCoordinatesHardBreak".$i." = [".$str."];";
				
			echo "var flightPathHardBreak".$i." = new google.maps.Polyline({
				path: flightPlanCoordinatesHardBreak".$i.",
				geodesic: true,
				strokeColor: '#ffff00',
				strokeOpacity: 1.0,
				strokeWeight: 4
				});
				flightPathHardBreak".$i.".setMap(map);";
		}
	?>
	
	//Mark over acceleration
	<?php
		for($i=0; $i<count($overAccelCoList); $i++)
		{
			$str = "";
			for($j=0; $j<count($overAccelCoList[$i]); $j++)
			{
				$str = $str."new google.maps.LatLng(".$overAccelCoList[$i][$j][$LATITUDE].",".$overAccelCoList[$i][$j][$LONGITUDE]."),";
			}
			echo "var flightPlanCoordinatesOverAc".$i." = [".$str."];";
				
			echo "var flightPathOverAc".$i." = new google.maps.Polyline({
				path: flightPlanCoordinatesOverAc".$i.",
				geodesic: true,
				strokeColor: '#00cc66',
				strokeOpacity: 1.0,
				strokeWeight: 3
				});
				flightPathOverAc".$i.".setMap(map);";
		}
	?>
	
	
	}
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    </head>
    <body>
        <div id="map-canvas"></div>
		<div>
		<?php
			echo "Driving duration: ".$maxDrivingDuration."<br>";
			echo "Max Speed: ".$max_speed."mph<br>";
			echo "full stop count: ".$fullStopCounter."<br>";
			echo "Hard break count: ".$hardBreakCounter."<br>";
			echo "Over acceleration count: ".$overAccelCounter."<br>";
			
			//for debug
			//for($i=0; $i<count($fullStopCoList); $i++)
			//{
			//	echo $fullStopCoList[$i][0][$LATITUDE].", ".$fullStopCoList[$i][0][$LONGITUDE].";"
			//	.$fullStopCoList[$i][1][$LATITUDE].", ".$fullStopCoList[$i][1][$LONGITUDE]."<br>";
			//}
			
			//for($i=0; $i<count($hardBreakCoList); $i++)
			//{
			//	for($j=0; $j<count($hardBreakCoList[$i]); $j++)
			//	{
			//		echo $hardBreakCoList[$i][$j][$LATITUDE].",".$hardBreakCoList[$i][$j][$LONGITUDE].";";
			//	}
			//	echo "<br>";
			//}
			
			//for($i=0; $i<count($overAccelCoList); $i++)
			//{
			//	for($j=0; $j<count($overAccelCoList[$i]); $j++)
			//	{
			//		echo $overAccelCoList[$i][$j][$LATITUDE].",".$overAccelCoList[$i][$j][$LONGITUDE].";";
			//	}
			//	echo "<br>";
			//}
			
		?>
		</div>
    </body>
</html>


