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

	//open the file(largest)
	$file = fopen('./upload/'.$array_file[0], 'r');
	
	//Counters
	$overSpeedCounter = 0;
	$overAccelCounter = 0;
	$hardBreakCounter = 0;
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
		if(3 <= $totalCounter)
		{
			//echo implode(";", $data)."<br>";
			$goods_list[] = $data;
			
			$speed = substr($data[19],0, strlen($data[19])-4);
			if($speed>$max_speed)
				$max_speed = $speed;
		}
		$totalCounter = $totalCounter + 1;	
	}
	fclose($file);
	
	$totalCounter = $totalCounter - 2; //don't count the tital info
	
	echo "Driving duration: ". $goods_list[$totalCounter-1][$ENGINE_RUNTIME]."<br>";
	echo "Max Speed: ".$max_speed."mph<br>";
	
	echo "The Speed Limit service is only available to Google Maps APIs Premium Plan customers!!!<br>";
	
	//Test code
	echo "Total:".$totalCounter."<br>";
	foreach ($goods_list as $arr)
	{
		for($i =0; $i<count($arr); $i++)
		{
			if($i==0)
				echo date("Y/m/d H:m:s", substr($arr[0],0,10));
			else
				echo ";".$arr[$i];
		}
		echo "<br>";
	}
	echo "<br>";
	//timestamp test
	//echo date("Y/m/d H:m:s",1460087747);
	//echo "<br>";
	//echo time();
	//echo "<br>";
	//echo strtotime("2016/4/7 23:55:47");
	
?>