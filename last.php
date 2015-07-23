<?
	session_start();
	include("functions.php");
	date_default_timezone_set ("America/New_York");
	
	include("database.php");
	
	$url = $_SERVER['REQUEST_URI'];
	if(strstr($url, "last")){
		//if page contains last	
		$result = mysqli_query($con,"SELECT * FROM Data ORDER BY Date DESC LIMIT 1");
		while($row = mysqli_fetch_array($result)) {
			$date = $row['Date'];
		}			
		$day = strftime("%A",strtotime($date));
	}
	elseif(strstr($url, "report")){
		//if page contains report	
		$date = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
		$day = strftime("%A",strtotime($date));
	}
	else{
		echo "Something went wrong, can't load the page. Let Jeremy know.";
	}
	
	
	$now = date("F j, Y @ g:i a");
	
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	//! recent submission session
	$recent_results = mysqli_query($con,"SELECT * FROM Data WHERE Date != '$date' ORDER BY Date");
	while($row = mysqli_fetch_array($recent_results)) {
		//generate link block
		if(SubmittedRecently($row['Submitted'])){
			$linkBlock .= StrToHex("<a href='viewsummary.php?Name=".$row['Name']."&Date=".$row['Date']."'>". $row['Date'] ." ". $row['Name'] ."</a><BR>");
		}
	}
	
	$_SESSION['linkblock'] = $linkBlock;
?>
	<html>

	<head>
		<title>Last Recap Report</title>
		<link rel="apple-touch-icon-precomposed" href="http://tsidisaster.net/report-touch-icon-114.png">
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;
				padding: 5px 50px 10px 50px;
			}
				
			h1, h2{
				Color: black;
			}
					
			h3{
				Font-style: italic;
			}
				
			#pics{
			    width: 50%;
			    height: auto;
			}
		</style>
		
		<?php			
			
			//get hours from db
			$result = mysqli_query($con,"SELECT * FROM Hours WHERE Date ='$date'");
			while($row = mysqli_fetch_array($result)) {
				$jobList[] = $row['Job'];
				$hoursList[] = $row['Hours'];
			}
			//get job list
			$result2 = mysqli_query($con,"SELECT * FROM Jobs");
			while($row2 = mysqli_fetch_array($result2)) {
				$job[] = $row2['Number'];
				$jobName[] = $row2['Name'];
				$theJob[$row2['Number']] = $row2['Name'];
			}
			//get expenses list
			$resultExp = mysqli_query($con,"SELECT * FROM Expenses WHERE Date ='$date'");
			while($rowExp = mysqli_fetch_array($resultExp)) {
				$expName[] = $rowExp['Name'];
				$expCost[] = $rowExp['Cost'];
				$expJob[] = $rowExp['Job'];
			}
			
			//get photo list
			$resultPhoto = mysqli_query($con,"SELECT * FROM photos WHERE Date ='$date'");
			while($rowPhoto = mysqli_fetch_array($resultPhoto)) {
				$photos[] = $rowPhoto['link'];
				$photosName[] = $rowPhoto['Name'];
			}
			//accumulate the hours for all jobs 
			$c2 = count($job);
			$c1 = count($jobList);
			$countExp = count($expJob);
			for ($i=1; $i<=$c2; $i++){
				for ($j=0; $j<=$c1; $j++) {
					if ($job[$i] == $jobList[$j]) {
						$hours[$i] += $hoursList[$j];
					}
					else{
						$hours[$i] += 0;
					}
				}
			}
			//calculate total hours
			for($i=1; $i<$c2; $i++){
				$totalHours += $hours[$i];
			}
			
			//accumulate the expenses for all jobs 
			for ($i=1; $i<=count($job); $i++){
				for ($j=0; $j<=count($expJob); $j++) {
					if ($job[$i] == $expJob[$j]) {
						$theExp[$i] += $expCost[$j];
					}
					else{
						$theExp[$i] += 0;
					}
				}
			}
			//calculate total expenses
			for($i=0; $i<$c2; $i++){
				$totalExp += $expCost[$i];
			}
			
			//gauge code
			$resultG = mysqli_query($con,"SELECT * FROM Data WHERE Date ='$date'");
			while($rowG = mysqli_fetch_array($resultG)) {
				$nameG[] = $rowG['Name'];
			}
			
			//Calculate missing list
			$res = mysqli_query($con, "SELECT * FROM employees WHERE recap = 'yes' ");
			$totalCount = 0;
		
			while($row = mysqli_fetch_array($res)) {
				$empNames[] = strtolower($row['Name']);
				$firstNames[] = $row['Name'];
				$totalCount++;
			}
			$EMP_COUNT = count($empNames); //the number of people who are supposed to turn in recaps
		
			$sql = "SELECT * FROM Data WHERE Date = '$date'";
			$result = mysqli_query($con, $sql);
			$ii = 0;
			while($row = mysqli_fetch_array($result)) {
				$data[$ii] = strtolower($row['Name']);
				$ii++;
			}
			
			
			//query database for criteria
			$exception = mysqli_query($con, "SELECT * FROM exception WHERE Date = '$date'");
			//check database date against today
			while($row = mysqli_fetch_array($exception)){
				//if ==, remove from email list, decrement the count
				$data[] = strtolower($row['Name']);
				$EMP_COUNT--;
				$exceptionNames[] = $row['Name'];
			}
				
			for ($i=0; $i<=$totalCount; $i++){
				for ($j=0; $j<=$totalCount; $j++){
					if ($data[$j] == $empNames[$i]){
						unset($empNames[$i]);
						
					}
				}
			}
			
			//find out what date sunday is
			$testdate = $date;
			for($i=0;$i<7;$i++){
				if (strftime("%A",strtotime($testdate)) == "Sunday") {
					$sunday = $testdate;
				}
				$testdate = date("Y-m-d", strtotime("-1 days", strtotime($testdate)));
			}
			
			$percent = round(count($nameG)/$EMP_COUNT*100, 0);
			if($percent > 100) $percent = 100;

			// Overtime people
			$result = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$date' AND WeeklyHours > 40 ORDER BY Name");
			$exempts = mysqli_query($con, "SELECT * FROM employees WHERE exempt = 'exempt' ORDER BY Name");
			while($erow = mysqli_fetch_array($exempts)){
				$exemptNames[] = $erow['Name'];
			}
			$skip = false;
			$OTcount = 0;
			while($row = mysqli_fetch_array($result)){
				for($i=0;$i<count($exemptNames);$i++){
					if($row['Name'] == $exemptNames[$i]){//take out the salaried employees
						$skip = true;
					}
				}
				if ($skip == false){
					//portion for the OT by employee chart
					$colName[] = $row['Name'];
					$colHours[] = $row['WeeklyHours'];
					$OThours += $row['WeeklyHours'];
					$OTcount++;
					
					//portion for OT by job chart
					$otcJob[] = $row['Job'];
					if ($row['WeeklyHours'] - $row['Hours'] > 40){
						$otcHours[] = $row['Hours'];
					}
					else{
						$otcHours[] = $row['WeeklyHours'] - 40;
					}
					
				}
				$skip = false;
			}
			$OThours = $OThours - 40 * $OTcount;
			
			//ot by job
			for ($i=1; $i<=count($job); $i++){
				for ($j=0; $j<=count($otcHours); $j++) {
					if ($job[$i] == $otcJob[$j]) {
						$otjHours[$i] += $otcHours[$j];
					}
					else{
						$otjHours[$i] += 0;
					}
				}
			}
			
			for ($i=0; $i<count($job); $i++){
				$otjsum += $otjHours[$i];
			}
			
			
			//get last 4 weeks of data for biggest 5 jobs
			
			$sdate = date("Y-m-d", strtotime("-0 week", strtotime(getSundayDate($date))));	//get start date (last sunday) for calculating biggest 5 jobs
			$fdate = date("Y-m-d", strtotime("6 days", strtotime($sdate)));	//get saturday date to stop calc
			$result = mysqli_query($con, "SELECT Job, SUM(Hours) FROM Hours WHERE Date between '".$sdate."' and '".$fdate."' Group BY Job");
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
			
		?><!--------------------------------------------------------------------------------------------------------------------<-->
		
		<script type='text/javascript' src='https://www.google.com/jsapi'></script>
	    <script type='text/javascript'>
		google.load('visualization', '1', {packages:['corechart']});
		google.setOnLoadCallback(drawChart);
		//!charts javascript
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Job', 'Hours per Day'],
				<?php 
					for($i=1; $i<$c2; $i++){
						if ($hours[$i] != ""){
							echo "['" . $jobName[$i] . "', " . $hours[$i] . "],\n";
						}
					}
				?>
			]);
			
			var options = {
				title: 'Total Hours by Job <?php echo $date; ?>',
				backgroundColor: 'white',
				pieSliceText: 'label',
				is3D:'false',
				chartArea:{left:0,top:20,width:'100%',height:'100%'}
				
				//legend.textStyle: {color: <'white'>}
			};
			
			var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
			
			chart.draw(data, options);
			
			//-----------EXPENSES-------------------------------------------------------------------------------------
			var dataExp = google.visualization.arrayToDataTable([
				['Job', 'Expenses per Day'],
				<?php 
					for($i=1; $i<$c2; $i++){
						if ($theExp[$i] != 0){
							echo "['" . $jobName[$i] . "', " . $theExp[$i] . "],";
						}
					}
				?>
			]);
			
			var optionsExp = {
				title: 'Total Expenses by Job <?php echo $date; ?>',
				backgroundColor: 'white',
				pieSliceText: 'label',
				is3D:'false',
				chartArea:{left:0,top:20,width:'100%',height:'100%'}
			};
			
	        var chartExp = new google.visualization.PieChart(document.getElementById('chart_divExp'));
	        
	        chartExp.draw(dataExp, optionsExp);
	        
	        //--------------------------Hours/Expenses--------------------------------------------------------------------------
	        
	        var dataC = google.visualization.arrayToDataTable([
	        	['Job', 'Hours', 'Expenses'],
				<?php 
					for($i=1; $i<count($job); $i++){
						if($hours[$i] > 0 || $theExp[$i] > 0){
							echo "['" . $jobName[$i] . "', " . $hours[$i] . ", " .$theExp[$i] . "],\n";
						}
					}
				?>
			]);
			
			var optionsC = {
				title: 'Hours and Expenses Today',
				hAxis: {title: 'Job', titleTextStyle: {color: 'red'}, slantedTextAngle:90},
				colors:['green', 'orange']
	        };
	        
	        var chartC = new google.visualization.ColumnChart(document.getElementById('chart_divC'));
	        chartC.draw(dataC, optionsC);
	        
	        //--------------------------OverTime--------------------------------------------------------------------------
	         //by employee
	        var dataOT = google.visualization.arrayToDataTable([
	        	['Name', 'Hours'],
				<?php 
					for($i=0; $i<count($colName); $i++){
						echo "['" . $colName[$i] . "', " . $colHours[$i] . "],\n";
					}
				?>
			]);
			
			var optionsOT = {
				title: 'Non-exempt Employee Hours with Overtime',
				hAxis: {title: 'Employee', titleTextStyle: {color: 'red'}, slantedTextAngle:90}
	        };
	        
	        var chartOT = new google.visualization.ColumnChart(document.getElementById('chart_divOT'));
	        chartOT.draw(dataOT, optionsOT);
	        
	        //by Job
	        var dataOTj = google.visualization.arrayToDataTable([
	        	['Job', 'Hours'],
				<?php 
					for($i=1; $i<count($job); $i++){
						if($otjHours[$i] != 0){
							echo "['" . $jobName[$i] . "', " . $otjHours[$i] . "],\n";
						}
					}
				?>
			]);
			
			var optionsOTj = {
				title: 'Overtime Hours by Job',
				hAxis: {title: 'Job', titleTextStyle: {color: 'red'}, slantedTextAngle:90},
				colors:['red']
	        };
	        
	        var chartOTj = new google.visualization.ColumnChart(document.getElementById('chart_divOTjob'));
	        chartOTj.draw(dataOTj, optionsOTj);
	        
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
		//-----------------------------Gauge---------------------------------------------------------------------------------
		google.load('visualization', '1', {packages:['gauge']});
		google.setOnLoadCallback(drawChartG);	
		
		function drawChartG() {
			var data = google.visualization.arrayToDataTable([
				['Label', 'Value'],
				['%Reports In', 0],
			]);
		
			var options = {
				animation:{
				duration: 2000,
				easing: 'inAndOut',},
				width: 400, height: 250,
				redFrom: 0, redTo: 50,
				greenFrom: 90, greenTo: 100,
				yellowFrom: 50, yellowTo: 90,
				minorTicks: 5
			};
	
	        var chart = new google.visualization.Gauge(document.getElementById('chart_gauge'));
	        
			chart.draw(data, options);
			
			data = google.visualization.arrayToDataTable([
				['Label', 'Value'],
				['%Reports In', <?php echo $percent; ?>],
	        ]);
	        
			chart.draw(data, options);
		}
	
	    </script>
	</head>
	<body>


		<?php
			include("nav.html");
			// Check connection
			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			
			$result = mysqli_query($con,"SELECT * FROM Data WHERE Date ='$date'");
			
			
			
			echo "Report Generated on " . $now . "<br><h1>Recap for " .$day ." " . $date . "</h1>";

			//gauge 
			echo "<table cellspacing='15px'><tr><td><div id='chart_gauge' style='width: 250px; height: 250px;'></div></td>";
			
			
			echo "<td><b>Missing:</b><BR>";
			for ($i=0; $i<$totalCount; $i++){
				if ($empNames[$i] != ""){
					echo $firstNames[$i]."<BR>";
				}
			}
			//show exceptions
			if(count($exceptionNames)>0){
				echo "</td><td width='50'><!placeholder></td><td><b>Exceptions:</b><br>";
				for($i=0;$i<count($exceptionNames);$i++){
					echo $exceptionNames[$i]."<BR>";
				}
			}

			//! recent submissions			
			echo "</td><td><b>Recent Submissions not on this page</b><br>";
			echo HexToStr($_SESSION['linkblock']);
			
			echo "</td></tr></table><br>";
			echo "<a href='#chart_div'><button style='background-color:#66ccff; width:100px; height:50px;'>Charts</button></a>";
			
			//! recaps
			while($row = mysqli_fetch_array($result)) {
			  echo "<h3>" . $row['Name'] . "</h3> <i>Submitted: " . $row['Submitted'] . "</i><br>" . $row['Summary'];
			  
				// photo right here
				if (is_array($photos)){
					foreach($photos as $key => $value){
						if($photosName[$key] == $row['Name']){
							echo "<a href='http://tsidisaster.net/developer/uploads/" . $value . "'><img src='http://tsidisaster.net/developer/uploads/" . $value . "' id='pics'></a><br>";
						}
					}
				}			  
				echo "<hr style='color: #0000FF;
					background-color: #66ccff;
					height: 5px;'>";
			}
			echo "Total hours today: " . $totalHours;
		?>
		
		<div id='chart_div' style='width: 800px; height: 400px; display: block; border-style:solid;'></div>
		<? echo 'Total Expenses today: $' . $totalExp; ?>
		<div id='chart_divExp' style='width: 800px; height: 400px; display: block; border-style:solid;'></div>
		
		<div id='chart_divC' style='width: 800px; height: 400px; display: block; border-style:solid;'></div>
		<div id='chart_hoursmonth' style='width: 800px; height: 400px; display: block; border-style:solid;'></div>
		<div id='chart_expmonth' style='width: 800px; height: 400px; display: block; border-style:solid;'></div>
		<? echo 'Total Overtime Hours this week: ' . $OThours; ?>
		<div id='chart_divOT' style='width: 1000px; height: 800px; display: 
		<?
		 if($OThours > 0)
		 	echo "border";
		 else
		 	echo "none";		 
		 ?>; border-style:solid;'></div>
		<? echo 'Total Overtime Hours today: ' . $otjsum; ?>
		<div id='chart_divOTjob' style='width: 1000px; height: 800px; display:
		<?
		 if($otjsum > 0)
		 	echo "border";
		 else
		 	echo "none";		 
		 ?>; border-style:solid;'></div>
		 
		
	</body>
</html>
		<? //*/ ?>
