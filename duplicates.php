<?php
	//collect variables
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
		
	while($row = mysqli_fetch_array($result)) {
		$nameData[] = $row['Name'];
		$dateData[] = $row['Date'];
		$jobData[] = $row['Job'];
		$hoursData[] = $row['Hours'];
		$submitterData[] = $row['Submitter'];
	}
	
	for ($i=0;$i<count($nameData);$i++){
		for ($j=1;$j<count($nameData);$j++){
			if ($nameData[$j] == $nameData[$i]
			&& $dateData[$j] == $dateData[$i]
			&& $jobData[$j] == $jobData[$i]
			&& $hoursData[$j] == $hoursData[$i]){
				$nameDup[] = $nameData[$i];
				$dateDup[] = $dateData[$i];
				$jobDup[] = $jobData[$i];
				$hoursDup[] = $hoursData[$i];
				$submitterDup[] = $submitterData[$i];
				$nameDup[] = $nameData[$j];
				$dateDup[] = $dateData[$j];
				$jobDup[] = $jobData[$j];
				$hoursDup[] = $hoursData[$j];
				$submitterDup[] = $submitterData[$j];
			}
		}
	}
	
	for ($i=0;$i<count($nameDup);$i++){
		echo $nameDup[$i]." ".
		$dateDup[$i]." ".
		$jobDup[$i]." ".
		$hoursDup[$i]." ".
		$submitterDup[$i]."<br>";
	}

	$count = 1;
	$dateCount = 1;
	
	include("nav.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Duplicates Report</title>
	</head>
	<body>
		<table cellspacing="10">
		<th>Name</th><th>Date</th><th>Job</th><th>Hours</th><th>Submitted by</th>
		<?php
		
			echo "from date: " . $fromDate . "<br>to date: " . $toDate . "<br>employee: " . $employee . "<br>job: " . $job . "<BR>empString: " . $empString . "<BR>jobString: " . $jobString;
			
			while($row = mysqli_fetch_array($result)) {
				//insert line after new date
				if($d1 != $row['Date'] && $count > 1){
					echo "<tr><td colspan='5'><hr></td></tr>";
					$dateCount++;
				}
				$d1 = $row['Date'];
				echo "<tr><td>" . $row['Name'] . "</td><td align='center'>" . strftime("%A",strtotime($row['Date'])) ." ".$row['Date'] . "</td><td align='center'>" . $row['Job'] . "</td><td align='center'>" . $row['Hours'] . "</td><td align ='center'>".$row['Submitter']."</td></tr>\n";
				$totalHours += $row['Hours'];
				$count++;
			}	
			
			echo "<tr><td colspan='5'><hr></td></tr><tr><td colspan='3'>Total Hours:</td><td>$totalHours</td></tr>";
			echo "<tr><td colspan='3'># of entries:</td><td>$count</td></tr>";
		?>
		</table>
	</body>
</html>