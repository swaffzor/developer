<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Vis</title>
		<link rel="apple-touch-icon-precomposed" href="http://tsidisaster.net/report-touch-icon-114.png">
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;
				padding: 5px 50px 10px 50px;}}
				
			h1, h2{
				Color: black;}
					
			h3{
				Font-style: italic;}
		</style>

<?

	date_default_timezone_set ("America/New_York");
	$now = date("F j, Y @ g:i a");
	$date = date("Y-m-d");
	$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	//test whether the two dates are in the same week
	//date2 is the later date
	function weekTest($date, $date2){
		//determine which day was Sunday
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($date)) == "Sunday") {
				$sunday = $date;	//$sunday is a date yyyy-mm-dd
			}
			$date = date("Y-m-d", strtotime("-1 days", strtotime($date))); //subtract a day from the date and repeat
		}
		$count = 0;
		$stop = false;
		//determine if date2 was during the same week as date
		while($stop == false){
			if($date2 == $sunday && $count <= 6){
				$stop = true;
				return true;	//returns true since less than 7 days between dates
			}
			elseif($count > 6){
				$stop = true;
			}
			$count++;
			$date2 = date("Y-m-d", strtotime("-1 days", strtotime($date2)));
		}
		return false;
	}
	
	//finds and returns the date of sunday of that week
	function getSundayDate($date){
		//determine which day was Sunday
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($date)) == "Sunday") {
				return $date;	//$sunday is a date yyyy-mm-dd
			}
			$date = date("Y-m-d", strtotime("-1 days", strtotime($date))); //subtract a day from the date and repeat
		}
	}
	
	$result2 = mysqli_query($con,"SELECT * FROM Jobs");
	while($row2 = mysqli_fetch_array($result2)) {
		$theJob[$row2['Number']] = $row2['Name'];
	}				
	//get last 4 weeks of data for biggest 5 jobs
			
	$sdate = date("Y-m-d", strtotime("-0 week", strtotime(getSundayDate($date))));	//get start date (last sunday) for calculating biggest 5 jobs
	$fdate = date("Y-m-d", strtotime("6 days", strtotime($sdate)));	//get saturday date to stop calc
	$result = mysqli_query($con, "SELECT Job, SUM(Hours) FROM Hours Group BY Job");
	while($row = mysqli_fetch_array($result)){
		$testJ[$row['Job']] = $row['SUM(Hours)'];	//dump the results into array
	}
	arsort($testJ);	//sort the results by largest hours by job
	foreach($testJ as $key=>$value){
		$jobarr[] = $key;	//get rid of large index numbers replace with low
	}
	foreach($theJob as $num=>$name){
		foreach($jobarr as $key=>$jnum){
			if($num == $jnum){
				$jobC[$key] = $name;	//match job number to index and assign job names
				echo $jobC[$key]." ".$name;
			}
		}
	}
	for ($i=0; $i<5; $i++){
		if($jobC[$i] == ""){
			$jobC[$i] = "NA";
		}
	}	
	//!line graph php for hours			
	for ($i=0; $i<4; $i++){
		
		$sdate = date("Y-m-d", strtotime("-$i week", strtotime(getSundayDate($date))));	//get starting sunday date
		$fdate = date("Y-m-d", strtotime("6 days", strtotime($sdate)));	//get stopping date
		$result = mysqli_query($con, "SELECT Job, SUM(Hours) FROM Hours WHERE Date between '".$sdate."' and '".$fdate."' Group BY Job");
		
		//assign values to associated arrays
		while($row = mysqli_fetch_array($result)){
			if($row['Job'] == $jobarr[0]){
				$j0[$i] = round($row['SUM(Hours)'],1);
			}
			if($row['Job'] == $jobarr[1]){
				$j1[$i] = round($row['SUM(Hours)'],1);
			}
			if($row['Job'] == $jobarr[2]){
				$j2[$i] = round($row['SUM(Hours)'],1);
			}
			if($row['Job'] == $jobarr[3]){
				$j3[$i] = round($row['SUM(Hours)'],1);
			}
			if($row['Job'] == $jobarr[4]){
				$j4[$i] = round($row['SUM(Hours)'],1);
			}
		}
	}
	
	
	for ($i=0; $i<4; $i++){
		if ($j0[$i] == ""){
			$j0[$i] = 0;
		}
		if ($j1[$i] == ""){
			$j1[$i] = 0;
		}
		if ($j2[$i] == ""){
			$j2[$i] = 0;
		}
		if ($j3[$i] == ""){
			$j3[$i] = 0;
		}
		if ($j4[$i] == ""){
			$j4[$i] = 0;
		}
	}
	//for expenses
	for ($i=0; $i<4; $i++){
		$sdate = date("Y-m-d", strtotime("-$i week", strtotime(getSundayDate($date))));
		$fdate = date("Y-m-d", strtotime("6 days", strtotime($sdate)));
		$result = mysqli_query($con, "SELECT Job, SUM(Cost) FROM Expenses WHERE Date between '".$sdate."' and '".$fdate."' Group BY Job");
		while($row = mysqli_fetch_array($result)){
			if($row['Job'] == $jobarr[0]){
				$je0[$i] = round($row['SUM(Cost)'],1);
			}
			if($row['Job'] == $jobarr[1]){
				$je1[$i] = round($row['SUM(Cost)'],1);
			}
			if($row['Job'] == $jobarr[2]){
				$je2[$i] = round($row['SUM(Cost)'],1);
			}
			if($row['Job'] == $jobarr[3]){
				$je3[$i] = round($row['SUM(Cost)'],1);
			}
			if($row['Job'] == $jobarr[4]){
				$je4[$i] = round($row['SUM(Cost)'],1);
			}
		}
	}
	
	for ($i=0; $i<4; $i++){
		if ($je0[$i] == ""){
			$je0[$i] = 0;
		}
		if ($je1[$i] == ""){
			$je1[$i] = 0;
		}
		if ($je2[$i] == ""){
			$je2[$i] = 0;
		}
		if ($je3[$i] == ""){
			$je3[$i] = 0;
		}
		if ($je4[$i] == ""){
			$je4[$i] = 0;
		}
	}
	
	include("nav.php");
	?>
	
	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
	google.load('visualization', '1', {packages:['corechart']});
	google.setOnLoadCallback(drawChart);
	//!charts javascript
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Week', '<? echo $jobC[0]."','".$jobC[1]."','".$jobC[2]."','".$jobC[3]."','".$jobC[4]; ?>'],
			['3 weeks ago',	<? echo $j0[3].",".$j1[3].",".$j2[3].",".$j3[3].",".$j4[3]; ?>],
			['2 weeks ago',	<? echo $j0[2].",".$j1[2].",".$j2[2].",".$j3[2].",".$j4[2]; ?>],
			['Last week',  	<? echo $j0[1].",".$j1[1].",".$j2[1].",".$j3[1].",".$j4[1]; ?>],
			['This week',	<? echo $j0[0].",".$j1[0].",".$j2[0].",".$j3[0].",".$j4[0]; ?>]
        ]);
		
		var options = {
			title: 'Job Hours Over Time (Total Weekly Hours)',

		};

		var chart = new google.visualization.LineChart(document.getElementById('chart_hoursmonth'));
		chart.draw(data, options);
		
		
        var data = google.visualization.arrayToDataTable([
			['Week', '<? echo $jobC[0]."','".$jobC[1]."','".$jobC[2]."','".$jobC[3]."','".$jobC[4]; ?>'],
			['3 weeks ago',	<? echo $je0[3].",".$je1[3].",".$je2[3].",".$je3[3].",".$je4[3]; ?>],
			['2 weeks ago',	<? echo $je0[2].",".$je1[2].",".$je2[2].",".$je3[2].",".$je4[2]; ?>],
			['Last week',  	<? echo $je0[1].",".$je1[1].",".$je2[1].",".$je3[1].",".$je4[1]; ?>],
			['This week',	<? echo $je0[0].",".$je1[0].",".$je2[0].",".$je3[0].",".$je4[0]; ?>]
        ]);
		
		var options = {
			title: 'Job Expenses Over Time (Expenses total per week)'
		};

		var chart = new google.visualization.LineChart(document.getElementById('chart_expmonth'));
		chart.draw(data, options);
  
	}
	</script>
	</head>
	<body>
	<div id='chart_hoursmonth' style='width: 1000px; height: 400px; display: block; border-style:solid;'></div>
	<div id='chart_expmonth' style='width: 1000px; height: 400px; display: block; border-style:solid;'></div>
	</body>
</html>