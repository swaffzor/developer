<?php
	session_start();

	require_once("database.php");
	include_once("functions.php");
	include_once("globals.php");
	require_once("classes.php");
	
	// if($_SESSION['LoggedIn'] != 1){
	// 	echo '<meta http-equiv="refresh" content="0;login.php?sender='.$URL.'">';
	// 	exit();
	// }
	// else 
	if($_SESSION['LoggedIn'] == 1){
		//initialize the object with the user's data from the employees table
		$tsiemp = new TSIemployee();
		$tsiemp->SetEmployeeData($_SESSION['User'], $con);
	}
	
	date_default_timezone_set ("America/New_York");
	
	$now = date("Y-m-d g:i:s a");
	
	if (strpos($_SERVER['REQUEST_URI'], "past.php") !== false){
		$date = $_POST['Year'] ."-".$_POST['Month'] ."-".$_POST['Day'];
		$yesterday = 0;
	}
	else{
		//get last recap date from the database using cookie data
		$lastRecapDateRows = mysqli_query($con, "SELECT Date FROM Data WHERE Name = '".$_COOKIE['name']."' ORDER BY Date DESC LIMIT 1");
		while($row = mysqli_fetch_array($lastRecapDateRows)) {
			$lastRecapDate = $row['Date'];
			//echo "Last Recap Submitted: $lastRecapDate <BR>";
		}
		
		//show the previous date if after midnight but before 10am
		$to_time = strtotime(date("Y-m-d"));
		$from_time = strtotime(date("Y-m-d G:i"));
		
		//day:86400, hour:3600, minute:60
		if($_POST['not_yesterday_flag'] == 1){
			$date = date("Y-m-d");
			$yesterday = 0;			
		}
		else{
			if(round(abs($to_time - $from_time) / 3600, 2) < $SHOW_YESTERDAY_THRESHOLD){
				//yesterday's date
				$date = date("Y-m-d", strtotime("-1 days", $to_time));
				$yesterday = 1;
			}
			else{
				$date = date("Y-m-d");
				$yesterday = 0;			
			}
		}
		
	}
	
	//load job numbers
	$tempjob = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");	
	while($row = mysqli_fetch_array($tempjob)){
		$jobs[$row['Number']] = $row['Name'];	//dump the results into a job array
	}
	
	/*if (isset($_POST['summary'])){
		include_once("recap.php");
	}*/
	
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
				
	$employees = mysqli_query($con,"SELECT * FROM employees WHERE Status = 'Active' AND Company = 'TSI' OR id = '1' ORDER BY Name");
	$empEntered = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$date'");
	while($row = mysqli_fetch_array($employees)) {
		$empNames[] = $row['Name'];
	}
	while($row = mysqli_fetch_array($empEntered)) {
		$empNamesEntered[] = $row['Name'];
	}
	
	$fresh = true;
	for ($i=0; $i<count($empNames); $i++){
		for ($j=0; $j<count($empNamesEntered); $j++){
			if($empNames[$i] == $empNamesEntered[$j]){
				$fresh = false;
			}
		}
		if($fresh){
			$disabled[$i] = "";
		}
		else{
			$disabled[$i] = "disabled";
		}
		$fresh = true;
		$emps[] = $empNames[$i];
	}
	unset($empNames);
	unset($empNamesEntered);
	$employees = mysqli_query($con,"SELECT * FROM employees WHERE Status = 'Active' AND Company != 'TSI' ORDER BY Name");
	$empEntered = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$date'");
	while($row = mysqli_fetch_array($employees)) {
		$empNames[] = $row['Name'];
	}
	while($row = mysqli_fetch_array($empEntered)) {
		$empNamesEntered[] = $row['Name'];
	}
	
	$fresh = true;
	for ($i=0; $i<count($empNames); $i++){
		for ($j=0; $j<count($empNamesEntered); $j++){
			if($empNames[$i] == $empNamesEntered[$j]){
				$fresh = false;
			}
		}
		if($fresh){
			$subDisabled[$i] = "";
		}
		else{
			$subDisabled[$i] = "disabled";
		}
		$fresh = true;
		$subEmps[] = $empNames[$i];
	}
		
	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Recap</title>
		
		
		<link rel="apple-touch-icon-precomposed" href="http://tsidisaster.net/recap-touch-icon-114.png">
		<link rel="icon" type="image/png" href="http://tsidisaster.net/favicon.ico">
		<script type="text/javascript">

			var FIELD_COUNT = 30;
			var clicks = 0;
			var YESTERDAY = <? echo $yesterday; ?>;
			
			function start(){
				putToDay();
				makeSameJob();
				checkChecks();
			}
			
			function checkChecks(){
				//alert("checking checks");
				showHide('expenses', 'box');
				showHide('cHours', 'cHoursb');
				showHide('cHours2', 'cHoursb2');
				showHide('odo', 'odoc');
				showHide('moreHours', 'multhours');
			}
			
			function insertEmail(senderName){
				var names = [
					<?
					$count=0;
					$tmp = mysqli_query($con,"SELECT * FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "'".$row['Name'] . "',";
						$count++;
					}
					?>
				''];
				var emails = [
					<?
					$tmp = mysqli_query($con,"SELECT * FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "'".$row['email'] . "',";
					}
					?>
				''];
				var COUNT = <? echo $count; ?>;
				
				for(i=0; i<COUNT; i++){
					if (document.getElementById("nameDrop").value == names[i]){
						document.getElementById("email").value = emails[i];
					}
				}
			}
			
			function showHide(div_id, sender) {
				if(document.getElementById(sender).checked) {
					//document.getElementById(div_id).style.display = 'block';
					document.getElementById(div_id).style.position = 'relative';
					document.getElementById(div_id).style.left = '0px';
				}
				else {
					//document.getElementById(div_id).style.display='none';
					document.getElementById(div_id).style.position='absolute;';
					document.getElementById(div_id).style.left = '-9999px';
				}
			}
			
			function putToDay(){
				//alert("putting today");
				var toDay = new Date();
				if(YESTERDAY){
					toDay.setDate(toDay.getDate()-1);
				}
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				
				
				
				if(mm < 10){
					document.getElementById("Month").value = "0" + mm;
				}
				else{
					document.getElementById("Month").value = mm;
				}
				
				if(dd < 10){
					document.getElementById("Day").value = "0" + dd;
				}
				else{
					document.getElementById("Day").value = dd;
				}
				
				document.getElementById("Year").value = yyyy;
				
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				
				dateCheck();
				//*/
			}
			
			function dateCheck(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				
				//disable future dates
				//Day
				var op = document.getElementById("Day").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > dd && dmm == mm) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
				//Month
				var op = document.getElementById("Month").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > mm && dyyyy == yyyy) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
				//Year
				var op = document.getElementById("Year").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > yyyy) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
			}
			
			function validate(){
				//alert("validating");
				var success = 1;	//true/false replacement for return false
				var message;		//message to show in alert when validation fails
				clicks++;
				//document.getElementById("upload").disabled = true;	//disable button to reduce duplicates
			
				if(document.getElementById("hours").value == ""){
					message = "Fill out your hours";
					document.getElementById("hours").focus();	
					success = 0;
				}
				else if(document.getElementById("hours").value > 24 || document.getElementById("hours").value < 0){
					message = "Invalid time entered.";
					document.getElementById("hours").focus();	
					success = 0;
				}
				
				else if(document.getElementById("job").value == 0){
					message = "Fill out your job";
					document.getElementById("job").focus();				
					success = 0;
				}
				else if(document.getElementById("summary").value == ""){
					message = "Fill out the summary";
					document.getElementById("summary").focus();	
					success = 0;
				}
				for (i=1; i<=FIELD_COUNT; i++) {
					if(document.getElementById("hours" + i).value > 24 || document.getElementById("hours" + i).value < 0){
						message = "Invalid time entered.";
						document.getElementById("hours" + i).focus();	
						success = 0;
					}
					if(document.getElementById("sub" + i).value > 24 || document.getElementById("sub" + i).value < 0){
						message = "Invalid time entered.";
						document.getElementById("sub" + i).focus();	
						success = 0;
					}
					if (i< <? echo $SUP_MULT_HOUR_COUNT; ?>){
						if(document.getElementById("hoursm" + i).value > 24 || document.getElementById("hoursm" + i).value < 0){
							message = "Invalid time entered.";
							document.getElementById("hoursm" + i).focus();	
							success = 0;
						}
					}
				}
				
				for (i=1; i<=FIELD_COUNT; i++) {
					//check for empty hours for employees
					if(document.getElementById("employee" + i).value != "---Select Employee---" && document.getElementById("hours" + i).value == ""){
						message = "Please enter the number of hours for " + document.getElementById("employee" + i).value + " in the space next to their name.";
						document.getElementById("hours" + i).focus();
						success = 0;
					}
					//check for empty hours for subs
					if(document.getElementById("semployee" + i).value != "---Select Employee---" && document.getElementById("sub" + i).value == ""){
						message = "Please enter the number of hours for " + document.getElementById("semployee" + i).value + " in the space next to his name.";
						document.getElementById("sub" + i).focus();
						success = 0;
					}
					//check for empty job numbers for employees
					if(document.getElementById("employee" + i).value != "---Select Employee---" && document.getElementById("job" + i).value == ""){
						message = "Please enter the job number for " + document.getElementById("employee" + i).value;
						document.getElementById("job" + i).focus();
						success = 0;
					}
					//check for empty job numbers for subs
					if(document.getElementById("semployee" + i).value != "---Select Employee---" && document.getElementById("sjob" + i).value == ""){
						message = "Please enter the job number for " + document.getElementById("semployee" + i).value;
						document.getElementById("sjob" + i).focus();
						success = 0;
					}
				}
				
				
				if (document.getElementById("startodo").value != "" && document.getElementById("endodo").value == ""){
					message = "Fill out the ending odometer";
					document.getElementById("endodo").focus();	
					success = 0;
				}
				else if (document.getElementById("endodo").value != "" && document.getElementById("startodo").value == ""){
					message = "Fill out the starting odometer";	
					document.getElementById("startodo").focus();
					success = 0;
				}
				if (document.getElementById("endodo").value == document.getElementById("startodo").value && document.getElementById("startodo").value != 0 && document.getElementById("endodo").value != 0){
					message = "Your starting and ending odometer are the same.";
					document.getElementById("startodo").focus();
					success = 0;
				}
				
				//todo: is this working?
				for(i=1;i<11;i++){
					if(document.getElementById("expense"+i).value != ""){
						if(document.getElementById("cost"+i).value == ""){
							message = "Please fill out the expense cost";
							document.getElementById("cost"+i).focus();
							success = 0;
						}						
					}
					if(document.getElementById("cost"+i).value != ""){
						//alert("inside 1st if for cost != ''");
						if(document.getElementById("expense"+i).value == ""){
							//alert("inside 2nd if for expense =''");							
							message = "Please fill out the expense name";
							document.getElementById("expense"+i).focus();
							success = 0;
						}
					}
				}
				
				//if validation fails, show the message, return false and enable the button for retry
				if(success == 0){
					alert(message);
					document.getElementById("upload").disabled = false;
					document.getElementById("theButton").disabled = false;
					return false;
				}
				else{
					//submit the form
					document.getElementById("email").disabled = false;
					document.forms["recapForm"].submit();
				}
				
				//document.getElementById("upload").disabled = false;
				//document.getElementById("theButton").disabled = true;
				//document.getElementById("theButton").style.position='absolute;';	//hide on success to reduce duplicates
				//document.getElementById("theButton").style.left = '-9999px';
							
			}
			
			function makeSameJob(){
				//alert("making same job");
				//if(document.getElementById("sameJob").checked){
					/*
						CAUTION: NEED TO ADD ANY CHANGES TO START AS WELL
					*/
					var theJob = document.getElementById("job").value;
					for(i=1; i<31; i++){
						document.getElementById("job" + i).value = theJob;
					}
					for(i=1; i<31; i++){
						document.getElementById("sjob" + i).value = theJob;
					}
					for(i=1; i<11; i++){
						document.getElementById("ejob" + i).value = theJob;
					}
					if(theJob == 192){
						document.getElementById("thedata").checked = true;
						showHide("kensingtonData", "thedata");
					}
					else{
						document.getElementById("thedata").checked = false;
						showHide("kensingtonData", "thedata");
					}
					if(theJob == 195 || theJob == 227){
						document.getElementById("thedata").checked = true;
						showHide("WetlandData", "thedata");
					}
					else{
						document.getElementById("thedata").checked = false;
						showHide("WetlandData", "thedata");
					}
					
					if(theJob == 200){
						document.getElementById("unit0").style.position = 'relative';
						document.getElementById("unit0").style.left = '0px';
					}
					else{
						document.getElementById("unit0").style.position = 'absolute';
						document.getElementById("unit0").style.left = '-9999px';
					}
					
				//}
			}
			
			function ShowBox(sender){
				var index;
				index = sender.id.substr(sender.id.length-1, 1);
				if(sender.value != " "){
					document.getElementById("summary" + index).style.display = 'block';
					document.getElementById("summary" + index).placeholder = "Job " + sender.value + " summary";
					document.getElementById("summary").placeholder = "Job " + document.getElementById("job").value + " summary here";
				}
				else{
					document.getElementById("summary" + index).style.display = 'none'; 
				}
			}
			
			function showStyle(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				var elems = document.getElementsByTagName('input');
				var len = elems.length;
				var elemst = document.getElementsByTagName('textarea');
				var lent = elems.length;
				var elemss = document.getElementsByTagName('select');
				var lens = elems.length;
				
				
				if(dd != ddd || mm != dmm || yyyy != dyyyy){
					var currentPage = "<? echo $_SERVER['REQUEST_URI']; ?>";
					//document.getElementById("messageDiv").style.display = 'block';
					document.getElementById("messageDiv").style.position = 'relative';
					document.getElementById("messageDiv").style.left = '0px';
					//disable form, show buttons
					document.recapForm.action = "past.php";
					document.getElementById("dateHere").innerHTML = dmm+"-"+ddd+"-"+dyyyy;
					
					//removed the disabling because it does not post disabled fields
					/*for (var i = 0; i < len; i++) {
					    elems[i].disabled = true;
					}
					for (var i = 0; i < lent; i++) {
					    elemst[i].disabled = true;
					}*/
				}
				else{
					//document.getElementById("messageDiv").style.display='none';
					document.getElementById("messageDiv").style.position='absolute;';
					document.getElementById("messageDiv").style.left = '-9999px';
					/*for (var i = 0; i < len; i++) {
					    elems[i].disabled = false;
					}
					for (var i = 0; i < lent; i++) {
					    elemst[i].disabled = false;
					}*/
					if (YESTERDAY){
						document.recapForm.action = "index.php";
						document.getElementById("message").innerHTML ="change to today?";
						document.getElementById("dateHere").innerHTML = dmm+"-"+ddd+"-"+dyyyy;
						document.getElementById("messageDiv").style.position = 'relative';
						document.getElementById("messageDiv").style.left = '0px';
						document.getElementById("date_text").innerHTML ="Enter a recap ";
						document.getElementById("not_yesterday_flag").value = 1;
					}
					else{
						document.getElementById("not_yesterday_flag").value = 0;
					}
				}
			}
			
			function showUnitID(sender){
				var num = sender.id.slice(-1);
				if(sender.value == 200){
					if(num == 'b'){
						document.getElementById("unit0").style.position = 'relative';
						document.getElementById("unit0").style.left = '0px';
					}
					else{
						document.getElementById("unit" + num).style.position = 'relative';
						document.getElementById("unit" + num).style.left = '0px';
					}
				}
				else{
					if(num == 'b'){
						document.getElementById("unit0").style.position = 'absolute';
						document.getElementById("unit0").style.left = '-9999px';
					}
					else{
						document.getElementById("unit" + num).style.position = 'absolute';
						document.getElementById("unit" + num).style.left = '-9999px';
					}
				}
			}
			
		</script>		
		<link rel="stylesheet" href="mystyle.css">
	</head>
	<body onload="start();">
		<? 
		include_once("nav2.php"); 
		include_once 'weekview.php';
/*
		echo "<pre>POST ";
		print_r($_POST);
		echo "<br>SESSION ";
		print_r($_SESSION);
		echo "</pre>";
*/
		?>
		
		
	<form action="recap.php" name="recapForm" method="post" enctype="multipart/form-data">
		<table cellspacing="15px"><tr><td valign="top">
		
		
		<? //!Date
			//if page contains past.php then display other date code
			if (strpos($_SERVER['REQUEST_URI'], "past.php") !== false){
				echo "<table><th>Month</th><th>Day</th><th>Year</th>
					<tr><td><select name='Month' id='Month' onchange='showStyle()' selected='". $_POST['Month']."'>
						<option value='". $_POST['Month']."'>". $_POST['Month']."</option>
					</select></td>
				
					<td><select name='Day' id='day' onchange='showStyle()' selected = '". $_POST['Day']."'>
						<option value='". $_POST['Day']."'>". $_POST['Day']."</option>
					</select>
					</td>
					
					<td><select name='Year' id='Year' onchange='showStyle()' selected='". $_POST['Year']."'>
						<option value='". $_POST['Year']."'>". $_POST['Year']."</option>
					</select></td></tr>
					<tr>
						<td colspan='3' align='center'>To change the date, press the back button</td>
					</tr>
					<tr>
						<td colspan='3' align='center'>
							<div id='messageDiv'><p id='message' style='color: red; font-size: 20;'>Not Today's Date</p></div>
						</td>
					</tr>
					</table>";
			}
			else{
				echo "<table>
					<th>Month</th><th>Day</th><th>Year</th>
					<tr><td><select name='Month' id='Month' onchange='dateCheck(); showStyle()' selected='". $_POST['Month']."'>
						<option value='01'>01</option>
						<option value='02'>02</option>
						<option value='03'>03</option>
						<option value='04'>04</option>
						<option value='05'>05</option>
						<option value='06'>06</option>
						<option value='07'>07</option>
						<option value='08'>08</option>
						<option value='09'>09</option>
						<option value='10'>10</option>
						<option value='11'>11</option>
						<option value='12'>12</option>
					</select></td>
				
					<td><select name='Day' id='Day' onchange='dateCheck(); showStyle()' selected='". $_POST['Day']."'>
						<option value='01' id='1'>01</option>
						<option value='02' id='2'>02</option>
						<option value='03' id='3'>03</option>
						<option value='04' id='4'>04</option>
						<option value='05' id='5'>05</option>
						<option value='06' id='6'>06</option>
						<option value='07' id='7'>07</option>
						<option value='08' id='8'>08</option>
						<option value='09' id='9'>09</option>
						<option value='10' id='10'>10</option>
						<option value='11' id='11'>11</option>
						<option value='12' id='12'>12</option>
						<option value='13' id='13'>13</option>
						<option value='14' id='14'>14</option>
						<option value='15' id='15'>15</option>
						<option value='16' id='16'>16</option>
						<option value='17' id='17'>17</option>
						<option value='18' id='18'>18</option>
						<option value='19' id='19'>19</option>
						<option value='20' id='20'>20</option>
						<option value='21' id='21'>21</option>
						<option value='22' id='22'>22</option>
						<option value='23' id='23'>23</option>
						<option value='24' id='24'>24</option>
						<option value='25' id='25'>25</option>
						<option value='26' id='26'>26</option>
						<option value='27' id='27'>27</option>
						<option value='28' id='28'>28</option>
						<option value='29' id='29'>29</option>
						<option value='30' id='30'>30</option>
						<option value='31' id='31'>31</option>
					</select>
					</td>
					
					<td colspan='1'><select name='Year' id='Year' onchange='dateCheck(); showStyle()' selected='". $_POST['Year']."'>
						<option value='2014'>2014</option>
						<option value='2015'>2015</option>
						<option value='2016'>2016</option>
						<option value='2017'>2017</option>
						<option value='2018'>2018</option>
						<option value='2019'>2019</option>
						<option value='2020'>2020</option>
						<option value='2021'>2021</option>
						<option value='2022'>2022</option>
						<option value='2023'>2023</option>
						<option value='2024'>2024</option>
						<option value='2025'>2025</option>
					</select></td>
					<tr>
						<td colspan='3' align='center'>
							<div id='messageDiv' class='hide'><p id='message' style='color: red; font-size: 20;'>Not Today's Date</p>
							<span id='date_text'>Are you sure you want to enter a past recap</span> for <br><b><span style='color: red; font-size: 20;'><span id='dateHere' name='dateHere'></span></span></b><br>
							<button>Yes</button>&nbsp&nbsp&nbsp&nbsp&nbsp<button type='button' onclick='putToDay(), showStyle()'>No</button>
							</div>
						</td>
				</tr>
				</table>";
			} 
		?>
		
			<?//!name
				if($yesterday){
					echo "<p id='message_yesterday' style='color: red; font-size: 20;'>Yesterday's Date</p>";
				}
			?>
			<select id="nameDrop" name="nameDrop" style="display: inline" onchange="insertEmail(this)">
				<?php
					if ($tsiemp->idNumber > 0) {
						echo '<option value="'.$tsiemp->idNumber.'">'.$tsiemp->name.'</option>';
					} else {
						foreach ($emps as &$employee) {
							echo '<option value="'.$employee.'">'.$employee.'</option>';
						}
					}
				?>
			</select>
			<input type="email" name="email" id="email" placeholder="email" disabled="true" required value="<?php echo $tsiemp->email; ?>"><br>

			<input placeholder="Hours" name="hours" id="hours" type="number" step="any" style="width: 50px" required value="<?php echo $_POST['hours']; ?>"/>

			<select name="job" id="job" onchange="makeSameJob()">
				<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs WHERE Status = 1 ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						
						if(isset($_POST['summary'])){
							$temp = $_POST['job'];
						}
						else{
							$temp = $_COOKIE['job']; 
						}
						if($temp == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
			</select> 
			
			<!equipment unit id>
			<input type="number" name="unit0" id="unit0" placeholder='unit #' style="width: 50px" class="hide" value="<? echo $_POST['unit0']; ?>">
			
			<br>
			<textarea name="summary" id="summary" rows="15" cols="50" required placeholder="Today's work summary and progress"><?php echo $_POST['summary'] ?></textarea><br />
		</td>
		<td>
			<? //!extra data for wetland and lake bank restoration  ?>
			<input type="checkbox" name="thedata" id="thedata" class="hide">
			
			<div id="WetlandData" name="WetlandData" class="hide">
				<h2 style="color:red;">Additional Data Required</h2>
				<input type="number" name="dike" id="dike" placeholder="Dike" value="<? echo $_POST['dike']; ?>">Truckloads<br>
				<input type="number" name="landSmoothing" id="landSmoothing" placeholder="Land Smoothing" value="<? echo $_POST['landSmoothing']; ?>">1/4 mile intervals<br>
				<input type="number" name="siltFencePlaced" id="siltFencePlaced" placeholder="Silt Fence Placed" value="<? echo $_POST['siltFencePlaced']; ?>">Truckloads<br>
				<input type="number" name="structures" id="structures" placeholder="Structures" value="<? echo $_POST['structures']; ?>">Structures<br>
				<input type="number" name="berms" id="berms" placeholder="Berms" value="<? echo $_POST['berms']; ?>">Loads hauled<br>
			</div>
			
			<div id="kensingtonData" name="kensingtonData" class="hide">
				<h2 style="color:red;">Kensington Data Required</h2>
				<?
					for ($i=0; $i<2; $i++){
						echo "Lake:<select name='kensingtonLake$i' id='kensingtonLake$i'>
							<option value='0'>---Select Lake---</option>
							<option value='1'>1</option>
							<option value='2'>2</option>
							<option value='5'>5</option>
							<option value='8'>8</option>
							<option value='9G'>9G</option>
							<option value='9T'>9T</option>
							<option value='10'>10</option>
							<option value='11'>11</option>
							<option value='12'>12</option>
							<option value='16'>16</option>
							<option value='17'>17</option>
						</select>
						<p>Please input exact quantities for work performed today.</p>";
						echo "<input type='number' name='fabric".$i."' id='fabric".$i."' placeholder='Filter fabric placed' value='". $_POST['fabric'.$i] ."'>Linear feet<br>";
						echo "<input type='number' name='geowebPlaced".$i."' id='geowebPlaced".$i."' placeholder='Geoweb placed' value='".$_POST['geowebPlaced'.$i]."'>Linear feet<br>";
						echo "<input type='number' name='fillPlaced".$i."' id='fillPlaced".$i."' placeholder='Fill dirt placed' value='".$_POST['fillPlaced'.$i]."'>Tons<br>";
						echo "<input type='number' name='grading".$i."' id='grading".$i."' placeholder='Graded slope' value='".$_POST['grading'.$i]."'>Linear feet<br>";
						echo "<input type='number' name='tieins".$i."' id='tieins".$i."' placeholder='Number of tie-ins' value='".$_POST['tieins'.$i]."'>Each<br>";
						echo "<input type='number' name='rockPlaced".$i."' id='rockPlaced".$i."' placeholder='Rock placed' value='".$_POST['rockPlaced'.$i]."'>Linear feet<br>";
						echo "<input type='number' name='topsoilPlaced".$i."' id='topsoilPlaced".$i."' placeholder='Topsoil placed' value='".$_POST['topsoilPlaced'.$i]."'>Linear feet<br>";
						echo "<input type='number' name='sodPlaced".$i."' id='sodPlaced".$i."' placeholder='Sod placed' value='".$_POST['sodPlaced'.$i]."'>Square feet<br>";
						echo "<input type='number' name='fillDelivered".$i."' id='fillDelivered".$i."' placeholder='Screenings' value='".$_POST['fillDelivered'.$i]."'>Tons<br>";
						echo "<input type='number' name='rockDelivered".$i."' id='rockDelivered".$i."' placeholder='Rock delivered' value='".$_POST['rockDelivered'.$i]."'>Tons<br>";
						echo "<input type='number' name='topsoilDelivered".$i."' id='topsoilDelivered".$i."' placeholder='Topsoil delivered' value='".$_POST['topsoilDelivered'.$i]."'>Cubic Yards<br><br><hr>";
					}
				?>
				<input type="hidden" name="updateSwitch" id="updateSwitch" value="<? 
					if (isset($_POST['summary'])){
						echo "1" ;
					}
					else{
						echo "0";
					}
				?>">
				<input type="hidden" name="not_yesterday_flag" id="not_yesterday_flag">
				<input type="hidden" name="load_time" id="load_time" value="<? echo $now; ?>">
				</form>
			</div>
		</td>
		<td>			
			<? //! project leadership chart ?>
			<!-- a href="http://tsidisaster.net/images/Project_Leadership_Chart.jpg" target="_blank"><img src="http://tsidisaster.net/images/Project_Leadership_Chart.jpg" width="500px" ></a><br-->
		</td>-
		</tr>
		<tr><td colspan="3">
			
			
			<input type="checkbox" <?php if(isset($_POST['multhours']) == 1){echo "checked";} ?> value="value1" name ="multhours" id="multhours" onclick="showHide('moreHours', 'multhours')"><label for="multhours">Multiple jobs</label><br />
			
			
			<div id="moreHours" class="hide">
				<? //!Multiple Hours
					for ($i=1; $i<$SUP_MULT_HOUR_COUNT; $i++){
						//multiple hours
						echo "<input placeholder='Hours' name='hoursm".$i."' id='hoursm".$i."' type='number' step='any' style='width: 50px' value='".$_POST['hoursm'.$i.'']."'>";
						//multiple job select
						echo "<select name='jobm".$i."' id='jobm".$i."' onchange='ShowBox(this); showUnitID(this);'>";
							foreach($jobs as $number=> $name){
								echo "<option value='" . $number . "'>" . $number . " " . $name . "</option>";
							}
						echo "</select>
						
						<input type='number' name='unit".$i."' id='unit".$i."' placeholder='unit #' style='width: 50px' class='hide' value='".$_POST['unit'.$i]."'>
						
						<br>";
						echo "<textarea name='summary".$i."' id='summary".$i."' style='display:none' rows='10' cols='50' placeholder=''></textarea><br />";
					}
				?>
			</div>
			
			<!CREW HOURS>
				 <input type="checkbox" name="cHoursb" <?php if(isset($_POST['cHoursb']) == 1){echo "checked";} ?> id="cHoursb" onclick="showHide('cHours', 'cHoursb')"><label for="cHoursb">Crew Hours</label>
			<div id="cHours" class="hide">
				<? //! Crew Hours
					for ($i=1; $i<11; $i++){
						echo "<select name='employee".$i."' id='employee".$i."' value='".$_POST['employee'.$i.'']."'>"; //!todo: js to show hours
							for ($j=0; $j<count($emps); $j++){
								if($_POST['employee'.$i.''] == $emps[$j]){
									echo "<option selected value='" . $emps[$j] . "'>" . $emps[$j] . "</option>";
								}
								else{
									echo "<option value='" . $emps[$j] . "'>" . $emps[$j] . "</option>";
								}
							}
						echo "</select>";
		
						echo "<input placeholder='Hours' type='number' step='any' name='hours".$i."' id='hours".$i."' style='width: 50px' value='".$_POST['hours'.$i.'']."'>";
						echo "<select name='job".$i."' id='job".$i."'>";
						foreach($jobs as $number=> $name){
								echo "<option value='" . $number . "'>" . $number . " " . $name . "</option>";
							}
						echo "</select><br />";
					}
				?>
			
			<input type="checkbox" name="cHoursb2" <?php if(isset($_POST['cHoursb2']) == 1){echo "checked";} ?> id="cHoursb2" onclick="showHide('cHours2', 'cHoursb2')"><label for="cHoursb2">More Crew Hours</label>
					<div id="cHours2" class="hide">
						<? //!Extra Employees
							for ($i=11; $i<31; $i++){
								echo "<select name='employee".$i."' id='employee".$i."' value='".$_POST['employee'.$i.'']."'>";
									for ($j=0; $j<count($emps); $j++){
										if($_POST['employee'.$i.''] == $emps[$j]){
											echo "<option selected value='" . $emps[$j] . "'>" . $emps[$j] . "</option>";
										}
										else{
											echo "<option value='" . $emps[$j] . "'>" . $emps[$j] . "</option>";
										}
									}
								echo "</select>";
				
								echo "<input placeholder='Hours' type='number' step='any' name='hours".$i."' id='hours".$i."' style='width: 50px' value='".$_POST['hours'.$i.'']."'>";
								echo "<select name='job".$i."' id='job".$i."'>";
								foreach($jobs as $number=> $name){
									echo "<option value='" . $number . "'>" . $number . " " . $name . "</option>";
								}
								echo "</select><br />";
							}
							
						?>
					</div>
			</div><br />

			<input type="checkbox" name="ssubb" <?php if(isset($_POST['ssubb']) == 1){echo "checked";} ?> id="ssubb" onclick="showHide('ssub', 'ssubb')"><label for="ssubb">Sub Hours</label>
			<div id="ssub" class="hide">
				<? //!Subs
					
					for ($i=1; $i<11; $i++){
						echo "<select name='semployee".$i."' id='semployee".$i."' value='".$_POST['semployee'.$i.'']."'>";
							for ($j=0; $j<count($subEmps); $j++){
								if($_POST['employee'.$i.''] == $subEmps[$j]){
									echo "<option selected value='" . $subEmps[$j] . "'>" . $subEmps[$j] . "</option>";
								}
								else{
									echo "<option value='" . $subEmps[$j] . "'>" . $subEmps[$j] . "</option>";
								}
							}
						echo "</select>";
		
						echo "<input placeholder='Hours' type='number' step='any' name='sub".$i."' id='sub".$i."' style='width: 50px' value='".$_POST['sub'.$i.'']."'>";
						echo "<select name='sjob".$i."' id='sjob".$i."'>";
							foreach($jobs as $number=> $name){
								echo "<option value='" . $number . "'>" . $number . " " . $name . "</option>";
							}
						echo "</select><br />";
					}
					
				?>
					
					<input type="checkbox" name="ssubb2" <?php if(isset($_POST['csubb2']) == 1){echo "checked";} ?> id="ssubb2" onclick="showHide('ssub2', 'ssubb2')"><label for="ssubb2">More Sub Hours</label>
					<div id="ssub2" class="hide">
						<? //!More Subs
					
							for ($i=11; $i<31; $i++){
								echo "<select name='semployee".$i."' id='semployee".$i."' value='".$_POST['semployee'.$i.'']."'>";
									for ($j=0; $j<count($subEmps); $j++){
										if($_POST['employee'.$i.''] == $subEmps[$j]){
											echo "<option selected value='" . $subEmps[$j] . "'>" . $subEmps[$j] . "</option>";
										}
										else{
											echo "<option value='" . $subEmps[$j] . "'>" . $subEmps[$j] . "</option>";
										}
									}
								echo "</select>";
				
								echo "<input placeholder='Hours' type='number' step='any' name='sub".$i."' id='sub".$i."' style='width: 50px' value='".$_POST['sub'.$i.'']."'>";
								echo "<select name='sjob".$i."' id='sjob".$i."'>";
								foreach($jobs as $number=> $name){
									echo "<option value='" . $number . "'>" . $number . " " . $name . "</option>";
								}
								echo "</select><br />";
							}
							
						?>
					</div>
			</div><br />
			
			<input type="checkbox" name="odoc" <?php if(isset($_POST['odoc']) == 1){echo "checked";} ?> id="odoc" onclick="showHide('odo', 'odoc')"><label for="odoc">Odometer</label>
			<div id="odo" class="hide">
				<table cellspacing="10">
					<th>ID</th><th>Year</th><th align="left">Make/Model</th><th>Tag Number</th>
					<tr><td>101</td><td>1997</td><td>Dodge Ram 3500</td><td>M074AX</td></tr>
					<tr><td>102</td><td>2008</td><td>Ford F550 SRW</td><td>521WCN</td></tr>
					<tr><td>103</td><td>2000</td><td>Ford F350 Super Duty</td><td>N846CC</td></tr>
					<tr><td>107</td><td>2001</td><td>Ford Van</td><td>907QAN</td></tr>
					<tr><td>108</td><td>2005</td><td>Saturn Vue</td><td>092PVI</td></tr>
					<tr><td>109</td><td>2013</td><td>Ford F150</td><td>BRDK01</td></tr>
					<tr><td>110</td><td>2013</td><td>Ford F150</td><td>BRDK02</td></tr>
					<tr><td>111</td><td>2014</td><td>Ford F250 SD</td><td>CFNX20</td></tr>
					<tr><td>112</td><td>2014</td><td>Ford F250 SD</td><td></td></tr>
					<tr><td>113</td><td>2004</td><td>Chevrolet C1500 TA</td><td>838WTT</td></tr>
					<tr><td>114</td><td>2008</td><td>Ford Expedition</td><td>BZTT68</td></tr>
					<tr><td>115</td><td>1998</td><td>Chevy Truck</td><td>CPTF62</td></tr>
					<tr><td>117</td><td>1994</td><td>GMC Van</td><td>907QAN</td></tr>
					<tr><td>119</td><td>1997</td><td>Ford F350</td><td>203MCK</td></tr>
					<tr><td>120</td><td>1988</td><td>Chevrolet Pickup</td><td>N104TB</td></tr>
					<tr><td>121</td><td>2014</td><td>Ford F550</td><td>CXGS63</td></tr>
				</table>
				Please input the ID number of your company vehicle <br>
				<input type="text" name="vid" id="vid" placeholder="Company Vehicle Id" value="<?php echo $_POST['vid'] ?>">
				<input placeholder="Starting odometer" type="number" step="any" name="startodo" id="startodo" value="<?php echo $_POST['startodo'] ?>">
				<input placeholder="Ending odometer" type="number" step="any" name="endodo" id="endodo" value="<?php echo $_POST['endodo'] ?>"><br />
			</div><br />
			
			<input type="checkbox" id="box" name="box" <?php if(isset($_POST['box']) == 1){echo "checked";} ?> onclick="showHide('expenses', 'box')"><label for="box">Expenses</label>
			<div id='expenses' class="hide">
				<? //! Expenses
					for ($i=1; $i<11; $i++){
						echo "<input type='text' name='expense".$i."' id='expense".$i."' placeholder='Expense ".$i."' value='".$_POST['expense'.$i.'']."'>";
						echo "<input placeholder='Cost' type='number' step='any' name='cost".$i."' id='cost".$i."' value='".$_POST['cost'.$i.'']."'>";
						echo "<select name='ejob".$i."' id='ejob".$i."'>";
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						echo "</select><br />";
					}
				?>
			</div><br />

			<textarea name="planning" id="planning" cols="50" placeholder="Next day planning"><?php echo $_POST['planning'] ?></textarea><br>
			<textarea name="problems" id="problems" cols="50" placeholder="List any problems, delays, reasons for downtime, or change orders"><?php echo $_POST['problems'] ?></textarea><br>
			<textarea name="discipline" id="discipline" cols="50" placeholder="List any disciplinary actions including name and offense"><?php echo $_POST['discipline'] ?></textarea><br>
			<textarea name="recognition" id="recognition" cols="50" placeholder="List (if any) employees that have demonstrated exceptional work or employees that deserve recognition"><?php echo $_POST['recognition'] ?></textarea>
			
			<p>Technical Difficulties</p>
			<textarea name="technicalDifficulties" id="technicalDifficulties" cols="50" rows="4" placeholder="If you are having technical difficulties with this page or anything with the recap system (like needing to add an employee not listed in a drop down,) list those here to create a ticket. NOTE: This area will not be seen by the Managing Members."><?php echo $_POST['technicalDifficulties'] ?></textarea>
			<!input type="file" name="userfile" id="file"> <br />

			<div id="theButton" style="position: relative; left: 0px;">
			<input type="button" id="upload" onclick="validate()" value="Submit" style="color: #f61c1c;">
			</div>
			 
		<? //include("recap.php"); ?>
		</td>
		</tr>
		</table>
	</body>
</html>