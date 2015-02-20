<?php
	$test = false;
	Class employeeList
	{
		Public $name = array();
		Public $hours = array();
		Public $job = array();
	}
	
	Class expense
	{
		Public $name = array();
		Public $cost = array();
	}
	$E_COUNT = 20;
	// Function to get the client IP address
	function get_client_ip() {
	    $ipaddress = '';
	    if ($_SERVER['HTTP_CLIENT_IP'])
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_X_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if($_SERVER['HTTP_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if($_SERVER['REMOTE_ADDR'])
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
	
	//variables
	$ip = get_client_ip();
	$dbh = mysql_connect ("50.87.144.29", "swafford_jeremy", "cloud999") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("swafford_recap");
	$con = mysqli_connect("50.87.144.29", "swafford_jeremy", "cloud999", "swafford_recap");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	date_default_timezone_set ("America/New_York");
	$now = date("F j, Y @ g:i a");
	$date = $_POST["Year"] . "-" . $_POST["Month"] . "-" . $_POST["Day"];
	$day = strftime("%A",strtotime($date));
	
	//collect the data from the form
	$summary = $_POST['summary'];
	$startOdo = $_POST['startodo'];
	$endOdo = $_POST['endodo'];
	$email = $_POST['email'];
	$eList = new employeeList();
	//supervisor info
	$eList->name[0] = $_POST['name'];
	$empName[0] = $_POST['name'];
	$eList->hours[0] = $_POST['hours'];
	$empHours[0] = $_POST['hours'];
	$eList->job[0] = $_POST['job'];
	$empJob[0] = $_POST['job'];
	$supMultHour = array();
	$supMultJob = array();
	$multHours = isset($_POST['multhours']);
	For ($i=1; $i<=10; $i++){
		$supMultHour[$i] = $_POST['hoursm' . $i];
		$supMultJob[$i] = $_POST['jobm' . $i];
	}
	
	//employee info
	For ($i=1; $i<=$E_COUNT; $i++){
		$eList->name[$i] = $_POST['employee' . $i];
		$eList->hours[$i] = $_POST['hours' . $i];
		$eList->job[$i] = $_POST['job' . $i];
		$empName[$i] = $_POST['employee' . $i];
		$empHours[$i] = $_POST['hours' . $i];
		$empJob[$i] = $_POST['job' . $i];
	}
	
	//expenses
	$exp = new expense();
	For ($i=1; $i<=$E_COUNT; $i++){
		$exp->name[$i] = $_POST['expense' . $i];
		$exp->cost[$i] = $_POST['cost' . $i];
		$expName[$i] = $_POST['expense' . $i];
		$expCost[$i] = $_POST['cost' . $i];
	}
	
	
	//calculate total expenses
	For ($i=1; $i <= 10; $i++){
		$totalExpense += $exp->cost[$i];
	}
	
	
	//remove duplicates here
	
	
	//compose message
	$message .= $eList->name[0] . ': ' . $eList->hours[0] . ' #' . $eList->job[0] .'<Br>';
	$totalHours = $eList->hours[0];
	//supervisor portion
	For ($i=1; $i <= 10; $i++){
		If ($supMultHour[$i] > 0){
			$message .= "--->" . $supMultHour[$i] . ' #' . $supMultJob[$i] .'<Br>';
			$totalHours += $supMultHour[$i];
		}
	}
	//print each employee that was entered in the form
	//hours
	For ($i=1; $i<=$E_COUNT; $i++){
		If ($eList->name[$i] != "" && $eList->name[$i] != "---Select Employee---"){
			$message .= $eList->name[$i] . ': ' . $eList->hours[$i] . ' #' . $eList->job[$i] .'<Br>';
			
			$totalHours += $eList->hours[$i];
		}
	}
	
	//--##Hours database##--
	For ($i=0; $i<=$E_COUNT; $i++){
		If ($eList->name[$i] != "" && $eList->name[$i] != "---Select Employee---"){
			$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours) VALUES ('$now', '$date', '$empName[$i]', '$empJob[$i]', '$empHours[$i]')";
			mysql_query($esql, $dbh);
		}
	}
	for ($i=1; $i<=$E_COUNT; $i++){
		If ($expCost[$i] > 0){
			$exsql = "INSERT INTO Expenses (Submitted, Date, Name, Job, Expense, Cost) VALUES ('$now', '$date', '$empName[0]', '$empJob[0]', '$expName[$i]', '$expCost[$i]')";
			mysql_query($exsql, $dbh);
		}
	}
	$message = $message . "<b>Total Hours: " . $totalHours . "</b><hr>";
	
	//put expenses in $message
	For ($i=1; $i<=10; $i++){
		If ($exp->cost[$i] > 0){
			$message = $message . $exp->name[$i] . ' $' . $exp->cost[$i] .'<Br>';
			$totalCost += $exp->cost[$i];
		}
	}
	If ($totalCost > 0){
		$message = $message . "<b>Total expenses: $" . $totalCost . "</b><hr>";
	}
	
	//add odometer to $message
	If ($startOdo != "" && $endOdo != ""){
		$diff = $endOdo - $startOdo;
		$message = $message . "Odometer: " . $startOdo . " - " . $endOdo . "<br><b>Total Mileage: " . $diff .'</b><hr>';
	}
	
	//replace line breaks with <BR>
	$summary = str_replace("'", "&#39", $summary);
	$summary = str_replace('"', "&#34", $summary);
	$summary = str_replace("\n", "<BR>", $summary);
	
	//print the page
	Echo "<html><head></head><body><h1>Thank you for turning in your recap for today.</h1>
	<h2>Recap Receipt for " . $day . " " . $_POST['Month'] . "-" . $_POST['Day'] . "-" . $_POST['Year'] . "</h2>
	 <h4>A copy of your recap is below, and has been emailed to " . $email . " as a receipt. <br>If there are errors in your recap, please reply to the email sent to you with the corrections.</h4>";
	Echo $message;
	echo $summary;
	echo "</body></html>";
	
	$message = $summary . "<hr>" . $message;
	
	//DATABASE
	
	$sql = "INSERT INTO Data (Name, Submitted, Email, Summary, Date, IP, Hours, Expenses, Mileage) VALUES ('$empName[0]', '$now', '$email', '$message', '$date', '$ip', '$totalHours', '$totalCost', '$diff')";
	mysql_query($sql, $dbh);
	if (!$dbh){
		Die("could not connect: " . mysql_error());
	}
	
	// add hours into database
	$theJob = $_POST['job'];
	for($i=0; $i<=10; $i++){
		if($empHours[$i] != ""){
			if($eList->job[$i] == $theJob){
				$jobHours += $eList->hours[$i];
			}
		}
	}
	$message = "<h1>Recap Receipt for " . $day . " " . $_POST['Month'] . "-" . $_POST['Day'] . "-" . $_POST['Year'] . "</h1>" . $message;
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= "From: $empName[0]" . "\r\n" . "Reply-To: recap@tsidisaster.com" . "\r\n" . "Bcc: jeremy@tsidisaster.com";
	$message = wordwrap($message, 70, "\r\n");
	mail($email, "Recap Receipt", $message, $headers);
?>