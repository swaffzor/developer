<?php
	
	//collect variables
	
	date_default_timezone_set ("America/New_York");
	$fromDate = date("Y-m-d");
	$toDate = $_POST['Year2'] . "-" . $_POST['Month2'] . "-" . $_POST['Day2'];
	$employee = $_POST['employee'];
	$job = $_POST['job'];
	
	//database
	$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$phours = 1 + getWeeklyHours("Jeremy Swafford", $fromDate);
	$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours, Submitter, WeeklyHours) VALUES (' ', '$fromDate', 'Jeremy Swafford', '99', '1', 'Jeremy SwaFForD', '$phours')";
	mysqli_query($con, $esql);
	
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
	
	//need to make an array of employee names (easy heh)
	$thoseEmps = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM employees WHERE Name != '---Select Employee---'"));

		$message .= "<h1>Missing Person Report for ".strftime("%A",strtotime($fromDate))." ".$fromDate."</h1>";
			$message .= "<table cellspacing='10'><th>Name</th>";
			$employees = mysqli_query($con,"SELECT * FROM employees WHERE Status = 'Active' and id > 1 and Company != 'Sub' ORDER BY Name");
			$empEntered = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$fromDate'");
			while($row = mysqli_fetch_array($employees)) {
				$empNames[] = $row['Name'];
			}
			while($row = mysqli_fetch_array($empEntered)) {
				$empNamesEntered[] = $row['Name'];
				if($row['Hours'] == 0){
					$message .= "<tr><td>".$row['Name']."</td></tr>";
				}
			}

			$fresh = true;
			for ($i=0; $i<count($empNames); $i++){
				for ($j=0; $j<count($empNamesEntered); $j++){
					if(strtolower($empNames[$i]) == strtolower($empNamesEntered[$j])){
						$fresh = false;
					}
				}
				if($fresh){
					$message .= "<tr><td>".$empNames[$i]."</td></tr>";
				}
				$fresh = true;
				$emps[] = $empNames[$i];
				
			}
			$message .= "</table>";

			echo $message;
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			$headers .= "From: Automated Recap System" . "\r\n" . "Reply-To: recap@tsidisaster.com" . "\r\n" . "Bcc: jeremy@tsidisaster.com";
			$message = wordwrap($message, 70, "\r\n");
			
			if(mail("marc@tsidisaster.com", "Missing Person Report", $message, $headers)){
				echo "email sent";
			}
			
	function getWeeklyHours($fname, $fdate){
		include("database.php");
		$now = date("F j, Y @ g:i a");
		$ddate = $fdate;
		$running = true;
		do {
			$qresult = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$ddate' AND Name = '$fname' ORDER BY WeeklyHours ASC");
			while($row = mysqli_fetch_array($qresult)) {
				$fwkh = $row['WeeklyHours'];
				$running = false;
			}
			$ddate = date("Y-m-d", strtotime("-1 days", strtotime($ddate)));
			if (strftime("%A",strtotime($ddate)) == "Sunday"){
				$running = false;
			}
		} while($running);
		if ($fwkh){
			return $fwkh;
		}
		else{
			return 0;
		}
	}
		?>