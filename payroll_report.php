<?php
	//include 'functions.php';
	//database
	$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	date_default_timezone_set ("America/New_York");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$fromDate = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
	$toDate = $_POST['Year2'] . "-" . $_POST['Month2'] . "-" . $_POST['Day2'];

	$result = mysqli_query($con,"SELECT * FROM Hours 
		WHERE Date BETWEEN '$fromDate' AND '$toDate' 
		ORDER BY Name, Date, Job");
		
	$count = 1;
	
	
	include("nav.html");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Payroll Report</title>
		
	</head>
	<body onload="putInfo()">
	
	<h1>Payroll Report</h1>
	<div id="info" name="info">INFO div</div>
		<table cellspacing="10">
		<th>Name</th><th>Date</th><th>Job</th><th>Hours</th><th>Submitted by</th>
		<?php
			//transfer the data from database into arrays
			while($row = mysqli_fetch_array($result)) {	
				$name[] = $row['Name'];
				$date[] = $row['Date'];
				$job[] = $row['Job'];
				$hours[] = $row['Hours'];
				$submitter[] = $row['Submitter'];
			}
			$personTotal = 0;	//initialize the person's total hour count
			
			//the main loop that prints the info
			for($i=0;$i<=count($name);$i++){	
				if($d1 != $date[$i] && $count > 1){
					echo "<tr><td colspan='5'><hr></td></tr>"; //insert line after new date
				}
				//seperate people
				if($i == 0){}
				elseif($name[$i] != $prevName){
					echo "<tr><td colspan='3' align='right'>Job</td><td>Hours</td></tr>";	//headers for job breakdown
					ksort($theJob);	//sort the array by index (key)
					foreach($theJob as $j=>$val){
						echo "<tr><td colspan='3' align='right'>".$j."</td><td>".$val."</td></tr>";	//print the weekly job totals
					}
					unset($theJob);	//clear the array for the next week
					echo "<tr><td colspan='5' align='center'>".$prevName."'s 2nd Week Hours: ".$weekTotal;	//print person's 2nd week hours
					echo "<tr><td colspan='5' align='center'><h3>".$prevName."'s Total Hours: ".$personTotal."</h3>";	//person name Total Hours: #
					echo "<tr><td colspan='5'>Signature/Date (Firma y Fecha):</td></tr>
						<tr><td colspan='5'><hr style='color: #0000FF;
						background-color: #66ccff;
						height: 5px;'></td></tr>
						<th>Name</th><th>Date</th><th>Job</th><th>Hours</th><th>Submitted by</th>";	//print blue horizontal line & headers
					$personTotal = 0;	//reset for next person
					$weekTotal = 0;	//reset for next person
					$startDate = $date[$i];	//re-initialize for next person
					$pplCount++;	//running total of people
				}
				//ignore 1st entry to make blank line disappear
				if ($prevName == ""){
					$startDate = $date[$i];
					$weekTotal = 0;
					
				}
				//seperate weeks
				if(weekTest($startDate, $date[$i]) == false){
					echo "<tr><td colspan='3' align='right'>Job</td><td>Hours</td></tr>";	//headers for job breakdown
					ksort($theJob);	//sort the array by index (key)
					foreach($theJob as $j=>$val){
						echo "<tr><td colspan='3' align='right'>".$j."</td><td>".$val."</td></tr>";	//print the weekly job totals
					}
					unset($theJob);	//clear the array for the next week
					echo "<tr><td colspan='5' align='center'>".$prevName."'s 1st Week Hours: ".$weekTotal;	//show 1st weeks hours
					echo "<tr><td colspan='5'><hr style='color: #0000FF;
						background-color: black;
						height: 2px;'></td></tr>";	//black horizontal line splitting the two weeks
					$startDate = $date[$i];	//re-initialize for next week
					$weekTotal = 0;	//reset for next week
				}
				
				$personTotal += $hours[$i]; //summation of hours for person's total hours
				$weekTotal += $hours[$i];	//summation of hours for week's total hours
				$d1 = $date[$i];	//set the date for next previous date
				$day = strftime("%A",strtotime($date[$i]));	//day of week
				$prevName = $name[$i];	//set the name for the next previous name
				echo "<tr><td>" . $name[$i] . "</td><td align='center'>" . strftime("%A",strtotime($date[$i])) ." ".$date[$i] . "</td><td align='center'>" . $job[$i] . "</td><td align='center'>" . $hours[$i] . "</td><td align ='center'>".$submitter[$i]."</td></tr>\n"; //print the actual line with all the info
				$totalHours += $hours[$i];	//summation of all hours
				$theJob[$job[$i]] += $hours[$i];	//running total for each job for each person
				$count++;	//running count of each row
			}	
			$count--;	//adjust for the last increase and prevent off by one error
			echo "<tr><td colspan='5'><hr></td></tr><tr><td colspan='3'></td></tr>";	//print total hours
						//*/
		?>
		</table>
		<script type="text/javascript">
			
			//put the information at the top of the page inside the info div
			function putInfo(){
				var infoStr = '<? echo "<table><tr><td>from date:</td><td>$fromDate</td></tr><tr><td>to date:</td><td>$toDate</td></tr><tr><td>Total Hours:</td><td>$totalHours</td></tr><tr><td># of entries:</td><td>$count</td></tr><tr><td># of Employees:</td><td>$pplCount</td></tr><tr><td>Average Employee <br>Pay Period Hours:</td><td>".round($totalHours/$pplCount, 2)."</td></tr></table>"; ?>';
				document.getElementById("info").innerHTML = infoStr;
			}
		</script>
	</body>
</html>
<?
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
?>