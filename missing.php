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
		
	$count = 1;
	$dateCount = 1;
	
	//need to make an array of employee names (easy heh)
	$thoseEmps = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM employees WHERE Name != '---Select Employee---'"));


	include("nav.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title></title>
	</head>
	<body>
		<h1>Missing Person Report for <? echo strftime("%A",strtotime($fromDate))." ".$fromDate; ?></h1>
		<table cellspacing="10">
		<th>Name</th>
		<?php
		
			
			$employees = mysqli_query($con,"SELECT * FROM employees WHERE Status = 'Active' and id > 1 and Company != 'Sub' ORDER BY Name");
			$empEntered = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$fromDate'");
			while($row = mysqli_fetch_array($employees)) {
				$empNames[] = $row['Name'];
			}
			while($row = mysqli_fetch_array($empEntered)) {
				$empNamesEntered[] = $row['Name'];
				if($row['Hours'] == 0){
					echo "<tr><td>".$row['Name']."</td></tr>";
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
					echo "<tr><td>".$empNames[$i]."</td></tr>";
				}
				$fresh = true;
				$emps[] = $empNames[$i];
				
			}
		
		?>
		</table>
	</body>
</html>