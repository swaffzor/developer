<?php
	require_once("database.php");
	include_once("functions.php");
	date_default_timezone_set ("America/New_York");
	$date = date("Y-m-d");
	
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

			function checkChecks(){
				showHide('expenses', 'box');
				showHide('cHours', 'cHoursb');
				showHide('cHours2', 'cHoursb2');
				showHide('odo', 'odoc');
				showHide('expenses2', 'box2');
				showHide('moreHours', 'multhours');
				
				//make same job as cookie's job
				makeSameJob();
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
				var toDay = new Date();
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
						message = "Please enter the number of hours for " + document.getElementById("employee" + i).value + " in the space next to his name.";
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
				//if(document.getElementById("sameJob").checked){
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
						showHide("data", "thedata")
					}
					else{
						document.getElementById("thedata").checked = false;
						showHide("data", "thedata")
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
					//document.getElementById("messageDiv").style.display = 'block';
					document.getElementById(div_id).style.position = 'relative';
					document.getElementById("messageDiv").style.left = '0px';
					//disable form, show buttons
					document.recapForm.action = "past.php";
					document.getElementById("dateHere").innerHTML = dmm+"-"+ddd+"-"+dyyyy;
					
					for (var i = 0; i < len; i++) {
					    elems[i].disabled = true;
					}
					for (var i = 0; i < lent; i++) {
					    elemst[i].disabled = true;
					}
				}
				else{
					//document.getElementById("messageDiv").style.display='none';
					document.getElementById(div_id).style.position='absolute;';
					document.getElementById("messageDiv").style.left = '-9999px';
					for (var i = 0; i < len; i++) {
					    elems[i].disabled = false;
					}
					for (var i = 0; i < lent; i++) {
					    elemst[i].disabled = false;
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
	<body onload="checkChecks(); putToDay();">
		<? include("nav2.php"); 		?>
		<form action="recap.php" name="recapForm" method="post" enctype="multipart/form-data">
	<table cellspacing="15px"><tr><td valign="top" width="500px">
		<table>
			<th>Month</th><th>Day</th><th>Year</th><th></th>
			<tr><td><select name='Month' id="Month" onchange="dateCheck(); showStyle()" selected="<?php echo $_POST['Month']?>">
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
		
			<td><select name='Day' id="Day" onchange="dateCheck(); showStyle()" selected="<?php echo $_POST['Day']?>">
				<option value='01' id="1">01</option>
				<option value='02' id="2">02</option>
				<option value='03' id="3">03</option>
				<option value='04' id="4">04</option>
				<option value='05' id="5">05</option>
				<option value='06' id="6">06</option>
				<option value='07' id="7">07</option>
				<option value='08' id="8">08</option>
				<option value='09' id="9">09</option>
				<option value='10' id="10">10</option>
				<option value='11' id="11">11</option>
				<option value='12' id="12">12</option>
				<option value='13' id="13">13</option>
				<option value='14' id="14">14</option>
				<option value='15' id="15">15</option>
				<option value='16' id="16">16</option>
				<option value='17' id="17">17</option>
				<option value='18' id="18">18</option>
				<option value='19' id="19">19</option>
				<option value='20' id="20">20</option>
				<option value='21' id="21">21</option>
				<option value='22' id="22">22</option>
				<option value='23' id="23">23</option>
				<option value='24' id="24">24</option>
				<option value='25' id="25">25</option>
				<option value='26' id="26">26</option>
				<option value='27' id="27">27</option>
				<option value='28' id="28">28</option>
				<option value='29' id="29">29</option>
				<option value='30' id="30">30</option>
				<option value='31' id="31">31</option>
			</select>
			</td>
			
			<td colspan="1"><select name='Year' id="Year" onchange="dateCheck(); showStyle()" selected="<?php echo $_POST['Year']?>">
				<option value='2014'>2014</option>
				<option value='2015'>2015</option>
				<option value='2016'>2016</option>
				<option value='2017'>2017</option>
				<option value='2018'>2018</option>
				<option value='2019'>2019</option>
				<option value='2020'>2020</option>
			</select></td>
			<tr>
				<td colspan="3" align="center">
					<div id="messageDiv" class="hide"><p id="message" style="color: red; font-size: 20;">Not Today's Date</p>
					Are you sure you want to enter a past recap for <br><b><span style="color: red; font-size: 20;"><span id="dateHere" name="dateHere"></span></span></b><br>
					<button>Yes</button>&nbsp&nbsp&nbsp&nbsp&nbsp<button type="button" onclick="putToDay(), showStyle()">No</button>
					</div>
				</td>
		</tr>
		</table>
		
			<?//!name
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
			<input type="email" name="email" id="email" placeholder="email" disabled="true" required value="<?php echo $_COOKIE['email'] ?>">
			
			<input type="checkbox" id="noList" name="noList" onchange="showHideName()">Name Not Listed<br />

			 <input placeholder="Hours" name="hours" id="hours" type="number" step="any" required value="<?php echo $_POST['hours'] ?>"/>

			<select name="job" id="job" onchange="makeSameJob()">
				<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs WHERE Status = 1 ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_COOKIE['job'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
			</select> 
			Multiple jobs<input type="checkbox" <?php if(isset($_POST['multhours']) == 1){echo "checked";} ?> value="value1" name ="multhours" id="multhours" onclick="showHide('moreHours', 'multhours')"><br />
			
			<!MORE HOURS>
			<div id="moreHours" class="hide">
				<input placeholder="Hours" name="hoursm1" id="hoursm1" type="number" step="any"  value="<?php echo $_POST['hoursm1']?>"/>
				<select name="jobm1">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['jobm1'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br>
				<input placeholder="Hours" name="hoursm2" id="hoursm2" type="number" step="any"/ value="<?php echo $_POST['hoursm2']?>">
				<select name="jobm2">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['jobm2'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br>
				<input placeholder="Hours" name="hoursm3" id="hoursm3" type="number" step="any"  value="<?php echo $_POST['hoursm3'] ?>"/>
				<select name="jobm3">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['jobm3'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br>
				<input placeholder="Hours" name="hoursm4" id="hoursm4" type="number" step="any"  value="<?php echo $_POST['hoursm4'] ?>"/>
				<select name="jobm4">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['jobm4'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br>
			</div>
			
			<!CREW HOURS>
			<input type="checkbox" name="cHoursb" <?php if(isset($_POST['cHoursb']) == 1){echo "checked";} ?> id="cHoursb" onclick="showHide('cHours', 'cHoursb')">Crew Hours
			<div id="cHours" class="hide">
				<!--input type="checkbox" name="sameJob" id="sameJob" onclick="makeSameJob()">Same Job<br-->
				
				<select name="employee1" id="employee1" value="<?php echo $_POST['employee1'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>
				</select>
				
				<input placeholder="Employee 1 hours" type="number" step="any" name="hours1" id="hours1" value="<?php echo $_POST['hours1'] ?>">
				<select name="job1" id="job1">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job1'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee2" id="employee2" value="<?php echo $_POST['employee2'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>
				</select>
				<input placeholder="Employee 2 hours" type="number" step="any" name="hours2" id="hours2"  value="<?php echo $_POST['hours2'] ?>">
				<select name="job2" id="job2">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job2'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee3" id="employee3" value="<?php echo $_POST['employee3'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>
				</select>
				<input placeholder="Employee 3 hours" type="number" step="any" name="hours3" id="hours3" value="<?php echo $_POST['hours3'] ?>">
				<select name="job3" id="job3">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job3'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee4" id="employee4"  value="<?php echo $_POST['employee4'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 4 hours" type="number" step="any" name="hours4" id="hours4" value="<?php echo $_POST['hours4'] ?>">
				<select name="job4" id="job4">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job4'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee5" id="employee5" value="<?php echo $_POST['employee5'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 5 hours" type="number" step="any" name="hours5" id="hours5"  value="<?php echo $_POST['hours5'] ?>">
				<select name="job5" id="job5">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job5'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee6" id="employee6" value="<?php echo $_POST['employee6'] ?>"><<?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 6 hours" type="number" step="any" name="hours6" id="hours6" value="<?php echo $_POST['hours6'] ?>">
				<select name="job6" id="job6">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job6'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee7" id="employee7"  value="<?php echo $_POST['employee7'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 7 hours" type="number" step="any" name="hours7" id="hours7" value="<?php echo $_POST['hours7'] ?>">
				<select name="job7" id="job7">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job7'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee8" id="employee8" value="<?php echo $_POST['employee8'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 8 hours" type="number" step="any" name="hours8" id="hours8" value="<?php echo $_POST['hours8'] ?>">
				<select name="job8" id="job8">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job8'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee9" id="employee9" value="<?php echo $_POST['employee9'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 9 hours" type="number" step="any" name="hours9" id="hours9" value="<?php echo $_POST['hours9'] ?>">
				<select name="job9" id="job9">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job9'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="employee10" id="employee10" value="<?php echo $_POST['employee10'] ?>"><?php
					for ($i=0; $i<count($emps); $i++){
						echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Employee 10 hours" type="number" step="any" name="hours10" id="hours10" value="<?php echo $_POST['hours10'] ?>">
				<select name="job10" id="job10">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job10'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
					<input type="checkbox" name="cHoursb2" <?php if(isset($_POST['cHoursb2']) == 1){echo "checked";} ?> id="cHoursb2" onclick="showHide('cHours2', 'cHoursb2')">More Crew Hours
					<div id="cHours2" class="hide">
						<select name="employee11" id="employee11" value="<?php echo $_POST['employee11'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						
						<input placeholder="Employee 11 hours" type="number" step="any" name="hours11" id="hours11" value="<?php echo $_POST['hours11'] ?>">
						<select name="job11" id="job11">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job11'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee12" id="employee12" value="<?php echo $_POST['employee12'] ?>"><<?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 12 hours" type="number" step="any" name="hours12" id="hours12" value="<?php echo $_POST['hours12'] ?>">
						<select name="job12" id="job12">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job12'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee13" id="employee13" value="<?php echo $_POST['employee13'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 13 hours" type="number" step="any" name="hours13" id="hours13" value="<?php echo $_POST['hours13'] ?>">
						<select name="job13" id="job13">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job13'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee14" id="employee14" value="<?php echo $_POST['employee14'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 14 hours" type="number" step="any" name="hours14" id="hours14" value="<?php echo $_POST['hours14'] ?>">
						<select name="job14" id="job14">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job14'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee15" id="employee15" value="<?php echo $_POST['employee15'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 15 hours" type="number" step="any" name="hours15" id="hours15" value="<?php echo $_POST['hours15'] ?>">
						<select name="job15" id="job15">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job15'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee16" id="employee16" value="<?php echo $_POST['employee16'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 16 hours" type="number" step="any" name="hours16" id="hours16" value="<?php echo $_POST['hours16'] ?>">
						<select name="job16" id="job16">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job16'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee17" id="employee17" value="<?php echo $_POST['employee17'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 17 hours" type="number" step="any" name="hours17" id="hours17" value="<?php echo $_POST['hours17'] ?>">
						<select name="job17" id="job17">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job17'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee18" id="employee18" value="<?php echo $_POST['employee18'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 18 hours" type="number" step="any" name="hours18" id="hours18" value="<?php echo $_POST['hours18'] ?>">
						<select name="job18" id="job18">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job18'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee19" id="employee19" value="<?php echo $_POST['employee19'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 19 hours" type="number" step="any" name="hours19" id="hours19" value="<?php echo $_POST['hours19'] ?>">
						<select name="job19" id="job19">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job19'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="employee20" id="employee20" value="<?php echo $_POST['employee20'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 20 hours" type="number" step="any" name="hours20" id="hours20" value="<?php echo $_POST['hours20'] ?>">
						<select name="job20" id="job20">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job20'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<!--------------------------------another 10 employees---------------------------->
						<select name="employee21" id="employee21" value="<?php echo $_POST['employee21'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 21 hours" type="number" step="any" name="hours21" id="hours21" value="<?php echo $_POST['hours20'] ?>">
						<select name="job21" id="job21">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee22" id="employee22" value="<?php echo $_POST['employee22'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 22 hours" type="number" step="any" name="hours22" id="hours22" value="<?php echo $_POST['hours20'] ?>">
						<select name="job22" id="job22">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee23" id="employee23" value="<?php echo $_POST['employee21'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 23 hours" type="number" step="any" name="hours23" id="hours23" value="<?php echo $_POST['hours20'] ?>">
						<select name="job23" id="job23">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee24" id="employee24" value="<?php echo $_POST['employee24'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 24 hours" type="number" step="any" name="hours24" id="hours24" value="<?php echo $_POST['hours20'] ?>">
						<select name="job24" id="job24">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee25" id="employee25" value="<?php echo $_POST['employee25'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 25 hours" type="number" step="any" name="hours25" id="hours25" value="<?php echo $_POST['hours20'] ?>">
						<select name="job25" id="job25">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee26" id="employee26" value="<?php echo $_POST['employee26'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 26 hours" type="number" step="any" name="hours26" id="hours26" value="<?php echo $_POST['hours20'] ?>">
						<select name="job26" id="job26">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee27" id="employee27" value="<?php echo $_POST['employee27'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 27 hours" type="number" step="any" name="hours27" id="hours27" value="<?php echo $_POST['hours20'] ?>">
						<select name="job27" id="job27">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee28" id="employee28" value="<?php echo $_POST['employee28'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 28 hours" type="number" step="any" name="hours28" id="hours28" value="<?php echo $_POST['hours20'] ?>">
						<select name="job28" id="job28">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee29" id="employee29" value="<?php echo $_POST['employee29'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 29 hours" type="number" step="any" name="hours29" id="hours29" value="<?php echo $_POST['hours20'] ?>">
						<select name="job29" id="job29">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="employee30" id="employee30" value="<?php echo $_POST['employee30'] ?>"><?php
							for ($i=0; $i<count($emps); $i++){
								echo "<option value='" . $emps[$i] . "' ".$disabled[$i].">" . $emps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Employee 30 hours" type="number" step="any" name="hours30" id="hours30" value="<?php echo $_POST['hours20'] ?>">
						<select name="job30" id="job30">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
					</div>
			</div><br />
			
			<input type="checkbox" name="ssubb" <?php if(isset($_POST['ssubb']) == 1){echo "checked";} ?> id="ssubb" onclick="showHide('ssub', 'ssubb')">Sub Hours
			<div id="ssub" class="hide">
				<!--input type="checkbox" name="sameJob" id="sameJob" onclick="makeSameJob()">Same Job<br-->
				
				<select name="semployee1" id="semployee1" value="<?php echo $_POST['employee1'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>
				</select>
				
				<input placeholder="Sub 1 hours" type="number" step="any" name="sub1" id="sub1" value="<?php echo $_POST['sub1'] ?>">
				<select name="sjob1" id="sjob1">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job1'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee2" id="semployee2" value="<?php echo $_POST['employee2'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>
				</select>
				<input placeholder="Sub 2 hours" type="number" step="any" name="sub2" id="sub2"  value="<?php echo $_POST['sub2'] ?>">
				<select name="sjob2" id="sjob2">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job2'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee3" id="semployee3" value="<?php echo $_POST['employee3'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>
				</select>
				<input placeholder="Sub 3 hours" type="number" step="any" name="sub3" id="sub3" value="<?php echo $_POST['sub3'] ?>">
				<select name="sjob3" id="sjob3">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job3'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee4" id="semployee4"  value="<?php echo $_POST['employee4'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 4 hours" type="number" step="any" name="sub4" id="sub4" value="<?php echo $_POST['sub4'] ?>">
				<select name="sjob4" id="sjob4">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job4'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee5" id="semployee5" value="<?php echo $_POST['employee5'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 5 hours" type="number" step="any" name="sub5" id="sub5"  value="<?php echo $_POST['sub5'] ?>">
				<select name="sjob5" id="sjob5">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job5'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee6" id="semployee6" value="<?php echo $_POST['employee6'] ?>"><<?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 6 hours" type="number" step="any" name="sub6" id="sub6" value="<?php echo $_POST['sub6'] ?>">
				<select name="sjob6" id="sjob6">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job6'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee7" id="semployee7"  value="<?php echo $_POST['employee7'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 7 hours" type="number" step="any" name="sub7" id="sub7" value="<?php echo $_POST['sub7'] ?>">
				<select name="sjob7" id="sjob7">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job7'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee8" id="semployee8" value="<?php echo $_POST['employee8'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 8 hours" type="number" step="any" name="sub8" id="sub8" value="<?php echo $_POST['sub8'] ?>">
				<select name="sjob8" id="sjob8">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job8'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee9" id="semployee9" value="<?php echo $_POST['employee9'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 9 hours" type="number" step="any" name="sub9" id="sub9" value="<?php echo $_POST['sub9'] ?>">
				<select name="sjob9" id="sjob9">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job9'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
				<select name="semployee10" id="semployee10" value="<?php echo $_POST['employee10'] ?>"><?php
					for ($i=0; $i<count($subEmps); $i++){
						echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
					}?>				</select>
				<input placeholder="Sub 10 hours" type="number" step="any" name="sub10" id="sub10" value="<?php echo $_POST['sub10'] ?>">
				<select name="sjob10" id="sjob10">
					<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						if($_POST['job10'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
					}?>
				</select><br />
					<input type="checkbox" name="ssubb2" <?php if(isset($_POST['csubb2']) == 1){echo "checked";} ?> id="ssubb2" onclick="showHide('ssub2', 'ssubb2')">More Sub Hours
					<div id="ssub2" class="hide">
						<select name="semployee11" id="semployee11" value="<?php echo $_POST['employee11'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						
						<input placeholder="Sub 11 hours" type="number" step="any" name="sub11" id="sub11" value="<?php echo $_POST['sub11'] ?>">
						<select name="sjob11" id="sjob11">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job11'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee12" id="semployee12" value="<?php echo $_POST['employee12'] ?>"><<?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 12 hours" type="number" step="any" name="sub12" id="sub12" value="<?php echo $_POST['sub12'] ?>">
						<select name="sjob12" id="sjob12">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job12'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee13" id="semployee13" value="<?php echo $_POST['employee13'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 13 hours" type="number" step="any" name="sub13" id="sub13" value="<?php echo $_POST['sub13'] ?>">
						<select name="sjob13" id="sjob13">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job13'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee14" id="semployee14" value="<?php echo $_POST['employee14'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 14 hours" type="number" step="any" name="sub14" id="sub14" value="<?php echo $_POST['sub14'] ?>">
						<select name="sjob14" id="sjob14">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job14'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee15" id="semployee15" value="<?php echo $_POST['employee15'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 15 hours" type="number" step="any" name="sub15" id="sub15" value="<?php echo $_POST['sub15'] ?>">
						<select name="sjob15" id="sjob15">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job15'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee16" id="semployee16" value="<?php echo $_POST['employee16'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 16 hours" type="number" step="any" name="sub16" id="sub16" value="<?php echo $_POST['sub16'] ?>">
						<select name="sjob16" id="sjob16">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job16'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee17" id="semployee17" value="<?php echo $_POST['employee17'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 17 hours" type="number" step="any" name="sub17" id="sub17" value="<?php echo $_POST['sub17'] ?>">
						<select name="sjob17" id="sjob17">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job17'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee18" id="semployee18" value="<?php echo $_POST['employee18'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 18 hours" type="number" step="any" name="sub18" id="sub18" value="<?php echo $_POST['sub18'] ?>">
						<select name="sjob18" id="sjob18">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job18'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee19" id="semployee19" value="<?php echo $_POST['employee19'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 19 hours" type="number" step="any" name="sub19" id="sub19" value="<?php echo $_POST['sub19'] ?>">
						<select name="sjob19" id="sjob19">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job19'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<select name="semployee20" id="semployee20" value="<?php echo $_POST['employee20'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 20 hours" type="number" step="any" name="sub20" id="sub20" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob20" id="sjob20">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
						if($_POST['job20'] == $row['Number']){
							echo "<option selected value='". $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						else{
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
							}?>
						</select><br />
						<!--------------------------------another 10 employees---------------------------->
						<select name="semployee21" id="semployee21" value="<?php echo $_POST['employee21'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 21 hours" type="number" step="any" name="sub21" id="sub21" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob21" id="sjob21">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee22" id="semployee22" value="<?php echo $_POST['employee22'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 22 hours" type="number" step="any" name="sub22" id="sub22" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob22" id="sjob22">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee23" id="semployee23" value="<?php echo $_POST['employee21'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 23 hours" type="number" step="any" name="sub23" id="sub23" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob23" id="sjob23">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee24" id="semployee24" value="<?php echo $_POST['employee24'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 24 hours" type="number" step="any" name="sub24" id="sub24" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob24" id="sjob24">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee25" id="semployee25" value="<?php echo $_POST['employee25'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 25 hours" type="number" step="any" name="sub25" id="sub25" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob25" id="sjob25">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee26" id="semployee26" value="<?php echo $_POST['employee26'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 26 hours" type="number" step="any" name="sub26" id="sub26" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob26" id="sjob26">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee27" id="semployee27" value="<?php echo $_POST['employee27'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 27 hours" type="number" step="any" name="sub27" id="sub27" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob27" id="sjob27">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee28" id="semployee28" value="<?php echo $_POST['employee28'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 28 hours" type="number" step="any" name="sub28" id="sub28" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob28" id="sjob28">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee29" id="semployee29" value="<?php echo $_POST['employee29'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 29 hours" type="number" step="any" name="sub29" id="sub29" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob29" id="sjob29">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
						<select name="semployee30" id="semployee30" value="<?php echo $_POST['employee30'] ?>"><?php
							for ($i=0; $i<count($subEmps); $i++){
								echo "<option value='" . $subEmps[$i] . "' ".$subDisabled[$i].">" . $subEmps[$i] . "</option>";
							}?>						</select>
						<input placeholder="Sub 30 hours" type="number" step="any" name="sub30" id="sub30" value="<?php echo $_POST['sub20'] ?>">
						<select name="sjob30" id="sjob30">
							<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
						</select><br />
					</div>
			</div><br />
			
			<input type="checkbox" name="odoc" <?php if(isset($_POST['odoc']) == 1){echo "checked";} ?> id="odoc" onclick="showHide('odo', 'odoc')">Odometer
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
				<input type="text" name="vid" id="vid" placeholder="Company Vehicle Id">
				<input placeholder="Starting odometer" type="number" step="any" name="startodo" id="startodo" value="<?php echo $_POST['startodo'] ?>">
				<input placeholder="Ending odometer" type="number" step="any" name="endodo" id="endodo" value="<?php echo $_POST['endodo'] ?>"><br />
			</div><br />
			
			<input type="checkbox" id="box" name="box" <?php if(isset($_POST['box']) == 1){echo "checked";} ?> onclick="showHide('expenses', 'box')">Expenses
			<div id='expenses' class="hide">
				<input type="text" name="expense1" placeholder="Expense 1" value="<?php echo $_POST['expense1'] ?>">
				<input placeholder="Cost" type="number" step="any" name="cost1" value="<?php echo $_POST['cost1'] ?>">
				<select name="ejob1" id="ejob1">
				<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
					}?>
				</select><br />
				<input type="text" name="expense2" placeholder="Expense 2" value="<?php echo $_POST['expense2'] ?>">
				<input placeholder="Cost" type="number" step="any" name="cost2" value="<?php echo $_POST['cost2'] ?>">
				<select name="ejob2" id="ejob2">
				<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
					}?>
				</select><br />
				<input type="text" name="expense3" placeholder="Expense 3" value="<?php echo $_POST['expense3'] ?>">
				<input placeholder="Cost" type="number" step="any" name="cost3" value="<?php echo $_POST['cost3'] ?>">
				<select name="ejob3" id="ejob3">
				<?php
					$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
					while($row = mysqli_fetch_array($job)) {
						echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
					}?>
				</select><br />
				<input type="checkbox" id="box2" name="box2" <?php if(isset($_POST['box2']) == 1){echo "checked";} ?> onclick="showHide('expenses2', 'box2')">More expenses
				<div id="expenses2" class="hide">
					<input type="text" name="expense4" placeholder="Expense 4" value="<?php echo $_POST['expense4'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost4" value="<?php echo $_POST['cost4'] ?>">
					<select name="ejob4" id="ejob4">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
					<input type="text" name="expense5" placeholder="Expense 5" value="<?php echo $_POST['expense5'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost5" value="<?php echo $_POST['cost5'] ?>">
					<select name="ejob5" id="ejob5">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
					<input type="text" name="expense6" placeholder="Expense 6" value="<?php echo $_POST['expense6'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost6" value="<?php echo $_POST['cost6'] ?>">
					<select name="ejob6" id="ejob6">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
					<input type="text" name="expense7" placeholder="Expense 7" value="<?php echo $_POST['expense7'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost7" value="<?php echo $_POST['cost7'] ?>">
					<select name="ejob7" id="ejob7">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
					<input type="text" name="expense8" placeholder="Expense 8" value="<?php echo $_POST['expense8'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost8" value="<?php echo $_POST['cost8'] ?>">
					<select name="ejob8" id="ejob8">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
					<input type="text" name="expense9" placeholder="Expense 9" value="<?php echo $_POST['expense9'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost9" value="<?php echo $_POST['cost9'] ?>">
					<select name="ejob9" id="ejob9">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
					<input type="text" name="expense10" placeholder="Expense 10" value="<?php echo $_POST['expense10'] ?>">
					<input placeholder="Cost" type="number" step="any" name="cost10" value="<?php echo $_POST['cost10'] ?>">
					<select name="ejob10" id="ejob10">
					<?php
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}?>
					</select><br />
				</div>
			</div><br />
			
			<textarea name="summary" id="summary" rows="10" cols="50" required placeholder="Today's work summary and progress"><?php echo $_POST['summary'] ?></textarea><br />
			<textarea name="planning" id="planning" cols="50" placeholder="Next day planning"></textarea><br>
			<textarea name="problems" id="problems" cols="50" placeholder="List any problems, delays, reasons for downtime, or change orders"></textarea><br>
			<textarea name="discipline" id="discipline" cols="50" placeholder="List any disciplinary actions including name and offense"></textarea><br>
			<textarea name="recognition" id="recognition" cols="50" placeholder="List (if any) employees that have demonstrated exceptional work or employees that deserve recognition"></textarea>
			<!input type="file" name="userfile" id="file"> <br />

			<div id="theButton" style="position: relative; left: 0px;">
			<input type="button" id="upload" onclick="validate()" value="Submit" style="color: #f61c1c;">
			</div>
			
			
		
		<? //include("recap.php"); ?>
		</td>
		<td valign="top">
			<input type="checkbox" name="thedata" id="thedata" class="hide">
			<div id="data" name="data" class="hide">
				<h2 style="color:red;">Kensington Data Required</h2>
				Lake:<select name="kensingtonLake" id="kensingtonLake">
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
				<p>Please input exact quantities for work performed today.</p>
				<input type="number" name="fabric" id="fabric" placeholder="Filter fabric placed">Linear feet<br>
				<input type="number" name="geowebPlaced" id="geowebPlaced" placeholder="Geoweb placed">Linear feet<br>
				<input type="number" name="fillPlaced" id="fillPlaced" placeholder="Fill dirt placed">Tons<br>
				<input type="number" name="grading" id="grading" placeholder="Graded slope">Linear feet<br>
				<input type="number" name="tieins" id="tieins" placeholder="Number of tie-ins">Each<br>
				<input type="number" name="rockPlaced" id="rockPlaced" placeholder="Rock placed">Linear feet<br>
				<input type="number" name="topsoilPlaced" id="topsoilPlaced" placeholder="Topsoil placed">Linear feet<br>
				<input type="number" name="sodPlaced" id="sodPlaced" placeholder="Sod placed">Square feet<br>
				<input type="number" name="fillDelivered" id="fillDelivered" placeholder="Screenings">Tons<br>
				<input type="number" name="rockDelivered" id="rockDelivered" placeholder="Rock delivered">Tons<br>
				<input type="number" name="topsoilDelivered" id="topsoilDelivered" placeholder="Topsoil delivered">Cubic Yards<br>
				
				<input type="hidden" name="updateSwitch" id="updateSwitch" value="<? 
					if (isset($_POST['summary'])){
						echo "1";
					}
					else{
						echo "0";
					}
				?>">
				</form>
			</div>
			<? include_once("news.html"); ?>
		</td>
		<td>
			<!insert exception request here>	
		</td>
		</tr>
		</table>
	</body>
</html>