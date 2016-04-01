<?php
	//collect variables
	$fromDate = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
	$toDate = $_POST['Year2'] . "-" . $_POST['Month2'] . "-" . $_POST['Day2'];
	$employee = $_POST['employee'];
	
	//database
	include("database.php");
	$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$empString = "";
	
	if($employee != ""){
		$empString = "AND Name = '" . $employee . "'";
	}
	else{
		$empString = "";
	}
	
	if($fromDate == "--"){
		$fromDate = $toDate;
	}
	
	$date = $fromDate;
	
	do{
		$result = mysqli_query($con,"INSERT INTO exceptions (Date, Name) VALUES ('$date', '$name')";
		$date = date("Y-m-d", strtotime("1 days", strtotime($date)));
	}while($date != $toDate);
	
	
	include("nav.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Personnel Report</title>
	</head>
	<body>

		
		</table>
	</body>
</html>