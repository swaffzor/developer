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