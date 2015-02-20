<?php
	
	$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	date_default_timezone_set ("America/New_York");
	$date = date("Y-m-d");
	$now = date("F j, Y @ g:i a");
	$day = strftime("%A",strtotime($date));
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= "From: recap@TSIdisaster.com" . "\r\n" . "Reply-To: recap@tsidisaster.com";
	
	
	//Calculate missing list
			$res = mysqli_query($con, "SELECT * FROM employees WHERE recap = 'yes' ");
			$totalCount = 0;
		
			while($row = mysqli_fetch_array($res)) {
				$empNames[] = strtolower($row['Name']);
				$firstNames[] = $row['Firstname'];
				$empEmails[] = $row['email'];
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
			
			if ($day == "Friday"){
				$data[] = "larry clough";
				$EMP_COUNT--;
			}
			//query database for criteria
			$exception = mysqli_query($con, "SELECT * FROM exception WHERE Date = '$date'");
			//check database date against today
			while($row = mysqli_fetch_array($exception)){
				//if ==, remove from email list, decrement the count
				$data[] = strtolower($row['Name']);
				$EMP_COUNT--;
			}
		
			for ($i=0; $i<=$totalCount; $i++){
				for ($j=0; $j<=$totalCount; $j++){
					if ($data[$j] == $empNames[$i]){
						unset($empNames[$i]);
						
					}
				}
			}	
	
	/*echo "emp count ".$EMP_COUNT."<BR>";
	var_dump($empNames);
	echo "<table><th>i</th><th>empNames</th><th>empEmails</th><th>newData</th><th>data</th>";
	for ($i=0; $i<$totalCount; $i++){
		echo "<tr><td>$i</td><td>$empNames[$i]</td><td>$empEmails[$i]</td><td>$newData[$i]</td><td>$data[$i]</td></tr>";
	}
	echo "</table>";*/
	
	for ($i=0; $i<$totalCount; $i++){
		if ($empNames[$i] != ""){
			$to = $empEmails[$i];
			$message = "Good Evening ".$firstNames[$i].", <br>Unfortunately, your recap was not received by midnight. Timely recaps are essential to establishing good communication within TSI and ensuring projects run smoothly. <br><br>

This has automatically been logged in your employee file and reported to the Compliance Committee. Disciplinary action will be taken for not turning in a recap.<br><br>

If you feel there has been an error, or need assistance and/or training to complete your reports, please email <a href='mailto:recap@tsidisaster.com'>recap@tsidisaster.com</a>. We are here to help. We encourage you to turn in your recap as soon as possible as a late recap is still better than a missing recap. <br><br>

Thank you!";
			
			$message = wordwrap($message, 70, "\r\n");
			mail($to, "Daily recap/hours needed", $message, $headers);
			mysqli_query($con, "INSERT INTO email (name, submitted, status) VALUES ('$empEmails[$i]', '$now', 'discipline email sent')");
			echo $to . "\n";
		}
	}

	
	
?>