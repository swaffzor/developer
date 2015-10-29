<?php
	
	/*
		This script returns a list of people and the assosiated days that they have not been reported.
		It is run every night at 11:59 PM EST
		This report is sent to Marc Junker and Jeremy Swafford is CC'd
		
		CAUTION:
			Loading this page will 
			-INCREMENT DATABASE FIELD the days missing of everyone that has not been reported for the day
			-Send an email to 2 people
	*/
	
	//collect variables
	require_once 'functions.php';
	date_default_timezone_set ("America/New_York");
	$fromDate = date("Y-m-d");
	$toDate = $_POST['Year2'] . "-" . $_POST['Month2'] . "-" . $_POST['Day2'];
	$employee = $_POST['employee'];
	$job = $_POST['job'];
	
	//database
	require_once 'database.php';
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$phours = 1 + getWeeklyHours("Jeremy Swafford", $fromDate);
	$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours, Submitter, WeeklyHours) VALUES (' ', '$fromDate', 'Jeremy Swafford', '99', '1', 'Jeremy Swafford', '$phours')";
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
		$message .= "<table cellspacing='10'><th>Name</th><th>Days Missing</th><th>Last Worked</th><th>Last Supervisor</th>";
		$employees = mysqli_query($con,"SELECT * FROM employees WHERE Status = 'Active' and id > 1 and Company != 'Sub' ORDER BY Name");
		$empEntered = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$fromDate'");
		while($row = mysqli_fetch_array($employees)) {
			$empNames[] = $row['Name'];
		}
		while($row = mysqli_fetch_array($empEntered)) {
			$empNamesEntered[] = $row['Name'];
			if($row['Hours'] == 0){
				$daysMissing = $row['daysMissing'] + 1;
				$message .= "<tr><td>".$row['Name']."</td><td>$daysMissing</td></tr>";
			}
			else{
				$daysMissing = 0;
			}
			mysqli_query($con, "UPDATE employees SET daysMissing='$daysMissing' WHERE Name='".$row['Name']."'");
		}

		$fresh = true;
		for ($i=0; $i<count($empNames); $i++){
			for ($j=0; $j<count($empNamesEntered); $j++){
				if(strtolower($empNames[$i]) == strtolower($empNamesEntered[$j])){
					$fresh = false;
				}
			}
			if($fresh){
				$temp = mysqli_query($con, "SELECT * FROM employees WHERE Name = '". $empNames[$i] ."'");
				while($row = mysqli_fetch_array($temp)) {
					$daysMissing = $row['daysMissing'] + 1;
				}
				mysqli_query($con, "UPDATE employees SET daysMissing='$daysMissing' WHERE Name='".$empNames[$i]."'");
				
				//get the last date of when they worked
				$tempLastDayWorked = mysqli_query($con, "SELECT * FROM Hours WHERE Name = '". $empNames[$i] ."' ORDER BY Date DESC LIMIT 1");
				while($row = mysqli_fetch_array($tempLastDayWorked)) {
					$lastDateWorked = $row['Date'];
					$lastSupervisor = $row['Submitter'];
				}
				
				if($daysMissing > 30){
					$message .= "<tr><td>".$empNames[$i]."</td><td>$daysMissing (HAS JUST BEEN REMOVED FROM DROP DOWN)</td>";
					mysqli_query($con, "UPDATE employees SET Status='expired' WHERE Name='".$empNames[$i]."'");
				}
				else if($daysMissing > 28){
					$message .= "<tr><td>".$empNames[$i]."</td><td>$daysMissing (will be automatically removed at 30)</td>";
				}
				else{
					$message .= "<tr><td>".$empNames[$i]."</td><td>$daysMissing</td>";
				}
				
				//show last date worked and last supervisor
				$message .= "<td>$lastDateWorked</td><td>$lastSupervisor</td></tr>";
			}
			//if an employee worked this day, reset their missing day count
			else{
				$daysMissing = 0;
				mysqli_query($con, "UPDATE employees SET daysMissing='$daysMissing' WHERE Name='".$row['Name']."'");
			}
			$fresh = true;
			$emps[] = $empNames[$i];
			
		}
		$message .= "</table>";

		echo $message;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$headers .= "From: robot@tsidisaster.net" . "\r\n" . "Bcc: jeremy@tsidisaster.com";
		$message = wordwrap($message, 70, "\r\n");
		
		if(mail("marc@tsidisaster.com", "Missing Person Report", $message, $headers)){
			echo "email sent";
		}
			
		?>