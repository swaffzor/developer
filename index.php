<?php
	require_once("database.php");
	include_once("functions.php");
	include_once("globals.php");
	date_default_timezone_set ("America/New_York");
	$date = date("Y-m-d");
	
	
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
						showHide("kensingtonData", "thedata")
					}
					else{
						document.getElementById("thedata").checked = false;
						showHide("kensingtonData", "thedata")
					}
					if(theJob == 195){
						document.getElementById("thedata").checked = true;
						showHide("scarboroughData", "thedata")
					}
					else{
						document.getElementById("thedata").checked = false;
						showHide("scarboroughData", "thedata")
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
					document.getElementById("messageDiv").style.position = 'relative';
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
					document.getElementById("messageDiv").style.position='absolute;';
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
	<body onload="start();">
		<? include_once("nav2.html"); 		?>
	<table cellspacing="15px"><tr><td valign="top" width="500px">
		<form action="recap.php" name="recapForm" method="post" enctype="multipart/form-data">
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
				echo $_SERVER['REQUEST_URI'];
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
			
			Multiple jobs<input type="checkbox" <?php if(isset($_POST['multhours']) == 1){echo "checked";} ?> value="value1" name ="multhours" id="multhours" onclick="showHide('moreHours', 'multhours')"><br />
			
			
			<div id="moreHours" class="hide">
				<? //!Multiple Hours
					for ($i=1; $i<5; $i++){
						//multiple hours
						echo "<input placeholder='Hours' name='hoursm".$i."' id='hoursm".$i."' type='number' step='any'  value='".$_POST['hoursm'.$i.'']."'>";
						//multiple job select
						echo "<select name='jobm".$i."'>";
							$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						
							while($row = mysqli_fetch_array($job)) {
								echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}
						echo "</select><br>";
					}
				?>
			</div>
			
			<!CREW HOURS>
			<input type="checkbox" name="cHoursb" <?php if(isset($_POST['cHoursb']) == 1){echo "checked";} ?> id="cHoursb" onclick="showHide('cHours', 'cHoursb')">Crew Hours
			<div id="cHours" class="hide">
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
				<? //! Expenses
					for ($i=1; $i<11; $i++){
						echo "<input type='text' name='expense".$i."' placeholder='Expense ".$i."' value='".$_POST['expense'.$i.'']."'>";
						echo "<input placeholder='Cost' type='number' step='any' name='cost".$i."' value='".$_POST['cost'.$i.'']."'>";
						echo "<select name='ejob".$i."' id='ejob".$i."'>";
						$job = mysqli_query($con,"SELECT * FROM Jobs ORDER BY Number");
						while($row = mysqli_fetch_array($job)) {
							echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
						}
						echo "</select><br />";
					}
				?>
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
			
			<div id="scarboroughData" name="scarboroughData" class="hide">
				<h2 style="color:red;">Scarborough Data Required</h2>
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
				</form>
			</div>
			<?
				include_once("news.html");
			?>
		</td>
		<td>
			<!insert exception request here>	
		</td>
		</tr>
		</table>
	</body>
</html>