<?php
	require_once("database.php");
	include_once("functions.php");
	include_once("globals.php");
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
				showHide('expenses2', 'box2');
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
			
			function showHideName(){
				if (document.getElementById("noList").checked){
					document.getElementById("nameDrop").style.display = 'none';
					document.getElementById("name").style.display = 'inline';
					document.getElementById("email").disabled = false;
				}
				else{
					document.getElementById("nameDrop").style.display = 'inline';
					document.getElementById("name").style.display = 'none';	
					document.getElementById("email").disabled = true;				
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
			
				if(document.getElementById("name").value == "" && document.getElementById("noList").checked){
					message = "Fill out your name";
					success = 0;
				}
				else if(document.getElementById("noList").checked == false && document.getElementById("nameDrop").value == "---Select Name---"){
					message = "Select your name";
					document.getElementById("nameDrop").focus();
					success = 0;
				}
				else if(document.getElementById("email").value == ""){
					message = "Fill out your email";
					document.getElementById("email").focus();
					success = 0;
				}
				else if(document.getElementById("hours").value == ""){
					message = "Fill out your hours";
					document.getElementById("hours").focus();	
					success = 0;
				}
				else if(document.getElementById("hours").value > 24){
					message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
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
					if(document.getElementById("hours" + i).value > 24){
						message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
						document.getElementById("hours" + i).focus();	
						success = 0;
					}
					if(document.getElementById("sub" + i).value > 24){
						message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
						document.getElementById("sub" + i).focus();	
						success = 0;
					}
					if (i<5){
						if(document.getElementById("hoursm" + i).value > 24){
							message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
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
					message = "What spacecraft did you drive today? Your starting and end odometer are the same.";
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
				
				//check for full name
				var spaceCount = 0;
				var x = document.getElementById("name").value;
				var boolSwitch = true;
				for(i = 0; i < x.length; i++){
					if(x[i] == ' '){
						boolSwitch = false;
						spaceCount++;
					}
				}
				
				
				
				if(boolSwitch == true && document.getElementById("noList").checked){
					message = "Please enter full name";
					document.getElementById("name").focus();
					success = 0;
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
			//Capitalize 1st letter in name
			function nameFix(){
				var eName = document.getElementById("name").value;
				document.getElementById("name").value = toTitleCase(eName);
				function toTitleCase(str){
				    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
				}
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
					if(theJob == 195){
						document.getElementById("thedata").checked = true;
						showHide("scarboroughData", "thedata");
					}
					else{
						document.getElementById("thedata").checked = false;
						showHide("scarboroughData", "thedata");
					}
					
				//}
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
			
		</script>		
		<style>
			body{
				font-family: sans-serif;
			}
			.hide{
				position: absolute;
				left: -9999px;
			}
		</style>
	</head>
	<body onload="start();">
		
		
		<? include_once("nav2.php"); 		?>
	<table cellspacing="15px"><tr><td valign="top">
		<form action="recap.php" name="recapForm" method="post" enctype="multipart/form-data">
		
		
		<?
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
					<th>Month</th><th>Day</th><th>Year</th><th></th>
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
			<select onchange="insertEmail(this.value)" id="nameDrop" name="nameDrop" style="display: inline">
				<option>---Select Name---</option>
				<?php
					$tmp = mysqli_query($con,"SELECT Name FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "<option value ='" . $row['Name']."'";
						
						if($row["Name"] == $_COOKIE["name"]){
							echo " selected";
						}
						
						echo ">" . $row['Name']."</option>";
					}
				?>
			</select>
			<input placeholder="Name" name="name" id="name" type="text" onchange="nameFix()" required value="<?php echo $_POST['name'] ?>" style="display: none"/>
			

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
			
			
			</select> 
			
			<hr>
			
			
			
			
			<!CREW HOURS>
			
			<div id="cHours">
				<? //! Crew Hours
					for ($i=1; $i<11; $i++){
						echo "<select name='employee".$i."' id='employee".$i."' value='".$_POST['employee'.$i.'']."'>";
							for ($j=0; $j<count($emps); $j++){
								if($_POST['employee'.$i.''] == $emps[$j]){
									echo "<option selected value='" . $emps[$j] . "' ".$disabled[$j].">" . $emps[$j] . "</option>";
								}
								else{
									echo "<option value='" . $emps[$j] . "' ".$disabled[$j].">" . $emps[$j] . "</option>";
								}
							}
						echo "</select>";
		
						echo "<select name='job".$i."' id='job".$i."'>";
		
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								if($_POST['job'.$i.''] == $row['Number']){
									echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
								}
								else{
									echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
								}
							}
						echo "</select>";
						echo "<input placeholder='Employee ".$i." hours' type='number' step='any' name='hours".$i."' id='hours".$i."' value='".$_POST['hours'.$i.'']."'><br>";
					}
				?>
			
				<input type="checkbox" name="cHoursb2" <?php if(isset($_POST['cHoursb2']) == 1){echo "checked";} ?> id="cHoursb2" onclick="showHide('cHours2', 'cHoursb2')">More Crew Hours
					<div id="cHours2" class="hide">
						<? //!Extra Employees
							for ($i=11; $i<31; $i++){
								echo "<select name='employee".$i."' id='employee".$i."' value='".$_POST['employee'.$i.'']."'>";
									for ($j=0; $j<count($emps); $j++){
										if($_POST['employee'.$i.''] == $emps[$j]){
											echo "<option selected value='" . $emps[$j] . "' ".$disabled[$j].">" . $emps[$j] . "</option>";
										}
										else{
											echo "<option value='" . $emps[$j] . "' ".$disabled[$j].">" . $emps[$j] . "</option>";
										}
									}
								echo "</select>";
				
								echo "<input placeholder='Employee ".$i." hours' type='number' step='any' name='hours".$i."' id='hours".$i."' value='".$_POST['hours'.$i.'']."'>";
								echo "<select name='job".$i."' id='job".$i."'>";
				
									$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
									while($row = mysqli_fetch_array($job)) {
										if($_POST['job'.$i.''] == $row['Number']){
											echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
										}
										else{
											echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
										}
									}
								echo "</select><br />";
							}
							
						?>
					</div>
			</div><br />
			
			<input type="checkbox" name="ssubb" <?php if(isset($_POST['ssubb']) == 1){echo "checked";} ?> id="ssubb" onclick="showHide('ssub', 'ssubb')">Sub Hours
			<div id="ssub" class="hide">
				<? //!Subs
					
					for ($i=1; $i<11; $i++){
						echo "<select name='semployee".$i."' id='semployee".$i."' value='".$_POST['semployee'.$i.'']."'>";
							for ($j=0; $j<count($subEmps); $j++){
								if($_POST['employee'.$i.''] == $subEmps[$j]){
									echo "<option selected value='" . $subEmps[$j] . "' ".$subDisabled[$j].">" . $subEmps[$j] . "</option>";
								}
								else{
									echo "<option value='" . $subEmps[$j] . "' ".$subDisabled[$j].">" . $subEmps[$j] . "</option>";
								}
							}
						echo "</select>";
		
						echo "<input placeholder='Sub ".$i." hours' type='number' step='any' name='sub".$i."' id='sub".$i."' value='".$_POST['sub'.$i.'']."'>";
						echo "<select name='sjob".$i."' id='sjob".$i."'>";
		
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								if($_POST['job'.$i.''] == $row['Number']){
									echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
								}
								else{
									echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
								}
							}
						echo "</select><br />";
					}
					
				?>
					
					<input type="checkbox" name="ssubb2" <?php if(isset($_POST['csubb2']) == 1){echo "checked";} ?> id="ssubb2" onclick="showHide('ssub2', 'ssubb2')">More Sub Hours
					<div id="ssub2" class="hide">
						<? //!More Subs
					
							for ($i=11; $i<31; $i++){
								echo "<select name='semployee".$i."' id='semployee".$i."' value='".$_POST['semployee'.$i.'']."'>";
									for ($j=0; $j<count($subEmps); $j++){
										if($_POST['employee'.$i.''] == $subEmps[$j]){
											echo "<option selected value='" . $subEmps[$j] . "' ".$subDisabled[$j].">" . $subEmps[$j] . "</option>";
										}
										else{
											echo "<option value='" . $subEmps[$j] . "' ".$subDisabled[$j].">" . $subEmps[$j] . "</option>";
										}
									}
								echo "</select>";
				
								echo "<input placeholder='Sub ".$i." hours' type='number' step='any' name='sub".$i."' id='sub".$i."' value='".$_POST['sub'.$i.'']."'>";
								echo "<select name='sjob".$i."' id='sjob".$i."'>";
				
									$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
									while($row = mysqli_fetch_array($job)) {
										if($_POST['job'.$i.''] == $row['Number']){
											echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
										}
										else{
											echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
										}
									}
								echo "</select><br />";
							}
							
						?>
					</div>
			</div><br />
			
			

			<div id="theButton" style="position: relative; left: 0px;">
			<input type="button" id="upload" onclick="validate()" value="Submit" style="color: #f61c1c;">
			</div>
			 
		<? //include("recap.php"); ?>
		</td>
		<td valign="top">
				
			<input type="checkbox" name="thedata" id="thedata" class="hide">
			
			
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
			<!insert exception request here>	
		</td>
		</tr>
		</table>
	</body>
</html>