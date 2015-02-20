<?php
	
	include 'database.php';
	date_default_timezone_set ("America/New_York");
	$today = date("Y-m-d");
	$now = date("F j, Y @ g:i a");
	$EMP_COUNT = 17;
	$empNames = array (
		"amanda swafford",
		"amy hartman",
		"brandon minder",
		"jessica paul",
		"johnny cullen",
		"john soltis",
		"jeremy swafford",
		"larry clough",
		"lauren lee",
		"leigh elliott",
		"marc junker",
		"bob haggard",
		"stephen lee",
		"steve mcbreairty",
		"mckayla elmore",
		"delmar rager");
		
		
	$firstNames = array (
		"Amanda",
		"Amy",
		"Brandon",
		"Jessica",
		"Johnny",
		"John",
		"Jeremy",
		"Larry",
		"Lauren",
		"Leigh",
		"Marc",
		"Bob",
		"Stephen",
		"Steve",
		"McKayla",
		"Delmar");

	$empEmails = array (
		"amanda@tsidisaster.com",
		"amy@tsidisaster.com",
		"brandon@tsidisaster.com",
		"jessica@tsidisaster.com",
		"johnny@tsidisaster.com",
		"johnsoltis@tsidisaster.com",
		"jeremy@tsidisaster.com",
		"larry@tsidisaster.com",
		"lauren@tsidisaster.com",
		"leigh@tsidisaster.com",
		"marc@tsidisaster.com",
		"bob@tsidisaster.com",
		"stephen@tsidisaster.com",
		"steve@tsidisaster.com",
		"mckayla@tsidisaster.com",
		"delmar@tsidisaster.com");
		
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= "From: recap@TSIdisaster.com" . "\r\n" . "Reply-To: recap@tsidisaster.com";
	
	
	$sql = "SELECT * FROM Data WHERE Date = '$today'";
	$result = mysqli_query($con, $sql);
	$ii = 0;
	while($row = mysqli_fetch_array($result)) {
		$data[$ii] = strtolower($row['Name']);
		$ii++;
	}
	
	if ($day == "Friday"){
		$data[] = "larry clough";
		$EMP_COUNT--;
	}
	
	for ($i=0; $i<=$EMP_COUNT; $i++){
		for ($j=0; $j<=count($data); $j++){
			if ($data[$j] == $empNames[$i]){
				$newData[$i] = $data[$j];
			}
		}
	}
	
	/*
	echo "<table><th>empNames</th><th>empEmails</th><th>newData</th><th>data</th>";
	for ($i=0; $i<$EMP_COUNT; $i++){
		echo "<tr><td>$empNames[$i]</td><td>$empEmails[$i]</td><td>$newData[$i]</td><td>$data[$i]</td></tr>";
	}
	echo "</table>";
	*/
	for ($i=0; $i<$EMP_COUNT; $i++){
		if ($newData[$i] == ""){
			$to = $empEmails[$i];
			$message = "Good Evening ".$firstNames[$i].", <br>This is an automated generic reminder for all employees who have not turned in their daily report/recap yet. If you're reading this now, you need to submit your report as soon as possible. <br><br>
            Please visit <a href='http://tsidisaster.net'>tsidisaster.net</a> to enter your recap. This is the only way that upper management receive recaps now.<br><br>
            Thank you and have a good night!";
			
			$message = wordwrap($message, 70, "\r\n");
			mail($to, "Daily recap/hours needed", $message, $headers);
			mysqli_query($con, "INSERT INTO email (name, submitted, status) VALUES ('$empEmails[$i]', '$now', 'reminder email sent')");
			echo $to . "\n";
		}
	}
	
	
	
?>