<?php
	//collect variables
	
	date_default_timezone_set ("America/New_York");
	$fromDate = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
	$toDate = $_POST['Year2'] . "-" . $_POST['Month2'] . "-" . $_POST['Day2'];
	$employee = $_POST['employee'];
	$job = $_POST['job'];
	
	//database
	$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$datediff = strtotime($fromDate) - strtotime($toDate);
    $datediff = abs(floor($datediff/(60*60*24)));
	$weekNum = round($datediff/7);
	$jobString = "";
	$empString = "";
	
	if($job > 0){
		$jobString = "AND Job = '" . $job . "'";
	}
	else{
		$jobString = "";
	}
	
	if($employee != ""){
		$empString = "AND Name = '" . $employee . "'";
	}
	else{
		$empString = "";
	}
	
	if($fromDate == "--"){
		$fromDate = $toDate;
	}
	$result = mysqli_query($con,"SELECT * FROM Hours 
		WHERE Date BETWEEN '$fromDate' AND '$toDate' 
		".$empString."
		".$jobString."
		ORDER BY Date, Name");
		
	$count = 1;
	$dateCount = 1;
	
	
	include("nav.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title></title>
	</head>
	<body>
		<table cellspacing="5" >
		<th>Day</th><th>Date</th><th>Job</th><th>Hours</th>
		<?php
			echo "Date Diff: ".$datediff."<BR>";
			echo "weekNum: ".$weekNum."<BR>";
			echo "from date: " . $fromDate . "<br>to date: " . $toDate . "<br>employee: " . $employee . "<br>job: " . $job . "<BR>empString: " . $empString . "<BR>jobString: " . $jobString."<BR>";
			
			$theHours = array();
			$theJob = array();
			$theDate = array();
			$index = array();
			$count = 0;
			while($row = mysqli_fetch_array($result)) {
				$theHours[] = $row['Hours'];
				$theJob[] = $row['Job'];
				$theName = $row['Name'];
				$theDate[] = $row['Date'];
				$index[] = $count;
				$count++;
			}
			echo "Name: " . $theName."<BR>";
			
			
			$count = 0;
			for ($j=0;$j<$weekNum; $j++){
				echo "<tr></tr>";
				
				echo "<tr><td colspan='4'><hr style='color: #0000FF;background-color: #66ccff;height: 5px;'></td></tr>";
				echo "<tr><td>Sunday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Sunday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}

				echo "<tr><td colspan='4'><hr></td></tr>";
				echo "<tr><td>Monday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Monday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}
				
				echo "<tr><td colspan='4'><hr></td></tr>";
				echo "<tr><td>Tuesday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Tuesday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}
				
				echo "<tr><td colspan='4'><hr></td></tr>";
				echo "<tr><td>Wednesday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Wednesday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}
				
				echo "<tr><td colspan='4'><hr></td></tr>";
				echo "<tr><td>Thursday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Thursday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}
				
				echo "<tr><td colspan='4'><hr></td></tr>";
				echo "<tr><td>Friday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Friday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}
				
				echo "<tr><td colspan='4'><hr></td></tr>";
				echo "<tr><td>Saturday</td>";
				for ($i=0; $i<count($theJob); $i++){
					if (date('l', strtotime($theDate[$i])) == "Saturday" && $index[$i] == $count){
						echo "<td>".$theDate[$i]."</td><td>".$theJob[$i]."</td><td>".$theHours[$i]."</td></tr>";
						$totalHours+= $theHours[$i];
						$theDate[$i] = "";
						$theJob[$i] = "";
						$theHours[$i] = "";
						$count++;
						break;
					}
				}
				echo "<tr></tr>";
				echo "<tr><td colspan='4'><hr style='color: #0000FF;background-color: #66ccff;height: 5px;'></td></tr><tr><td colspan='2'>Week Total Hours:</td><td>$totalHours</td></tr>";
				$totalSumHours += $totalHours;
				$totalHours = 0;
			}
				
			
			echo "<tr><td colspan='4'><hr style='color: #0000FF;background-color: #66ccff;height: 5px;'></td></tr><tr><td colspan='2'>Total Hours:</td><td>$totalSumHours</td></tr>";
			echo "<tr><td colspan='2'># of entries:</td><td>$count</td></tr>";
			echo "hello";
		?>
		</table>
	</body>
</html>