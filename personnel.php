<?		
	
	include("nav.php");
	session_start();
	/*if($_SESSION['LoggedIn'] != 1){
		echo '<meta http-equiv="refresh" content="0;login.php?sender=index.php">';
		exit();
	}*/
	
	$pword = "beachboys";
	$expire = time() + (60*60*24*90); // 3 months
	
	if($_POST['remember'] == "on" && $_POST['pword'] == $pword){
		setcookie("rememberme", $pword, $expire);
	}
		//setcookie("rememberme", "false");
	/*
	echo "<pre>";
	print_r($_POST);
	print_r($_COOKIE);
	echo "</pre>";
	*/
	
	$page_to_display = "
		<form name='test' action='personnel.php' method='post' >
			<input type='password' name='pword'>Password
			<br><input type='checkbox' id='remember' name='remember'><label for='remember'>Remember me for 3 months</label><br>
	";
			
		foreach($_POST as $key => $value){
			if($key != "submit" && $key != "pword"){
				$page_to_display.= "<input type='hidden' name='$key' value='$value'>";
			}
		}
		
		$page_to_display.= "<input type='submit'>
			</form><br>
			If you do not know the password, contact Marc Junker
		";
	
	if($_COOKIE['rememberme'] != $pword){	
		if($_POST['pword'] == ""){
			echo $page_to_display;
			exit();
		}
		elseif($_POST['pword'] == $pword){
			//success
		}
		else{
			echo "<i>Incorrect Password</i>";
			echo $page_to_display;
			exit();
		}
	}
?>


<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>

	<head>
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;}
		</style>
		<title>Personnel Report</title>
		<?php
			require_once 'database.php';
			
		?>
		
		<script type="text/javascript">
			function putToday(){
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();
				var ddd = document.getElementById("day").value;
				var dmm = document.getElementById("month").value;
				var dyyyy = document.getElementById("year").value;
				
				if(mm < 10){
					document.getElementById("month").value = "0" + mm;
					document.getElementById("month2").value = "0" + mm;
				}
				else{
					document.getElementById("month").value = mm;
					document.getElementById("month2").value = mm;
				}
				
				if(dd < 10){
					document.getElementById("day").value = "0" + dd;
					document.getElementById("day2").value = "0" + dd;
				}
				else{
					document.getElementById("day").value = dd;
					document.getElementById("day2").value = dd;
				}
				
				
				document.getElementById("year").value = yyyy;
				document.getElementById("year2").value = yyyy;
			}
			
			function enableDate(){
				if (document.getElementById("dateCheckBox").checked){
					document.getElementById("month").disabled=false;
					document.getElementById("day").disabled=false;
					document.getElementById("year").disabled=false;
				}
				else{
					document.getElementById("month").disabled=true;
					document.getElementById("day").disabled=true;
					document.getElementById("year").disabled=true;
					putToday();
				}
			}
			
			function test(){
				alert("hi");
			}
			
			function destination(sender){
				document.dateForm.action = sender;
			}
			
			function payRange(prange){
			//Possible better solution: encode value in selectoin as "yyyy-mm-dd-yyyy2-mm2-dd2" and have date[3] through date[5] be the second date. Use php to calculate the math and enter the value before page load.
				var date = prange.split("-");	//split date yyyy-mm-dd into an array
				document.getElementById("year2").value = date[0];
				document.getElementById("month2").value = date[1];
				document.getElementById("day2").value = date[2];
				
				mm = date[1];
				dd = date[2];
				
				if(date[2] > 13){
					dd = (date[2] - 13);
				}
				else{
					dd = dd - 13;
					if(mm == 1 || mm == 2 || mm == 4 || mm == 6 || mm == 8 || mm == 9 || mm == 11){
						dd = dd + 31;
					}
					else if(mm == 3){
						dd = dd + 28;
					}
					else{
						dd = dd + 30;
					}
					if(mm == "01"){
						mm = "12";
					}
					else if(mm < 11){
						mm = "0"+ (mm - 1);
					}
					else{
						mm = mm - 1;
					}
				}
					//alert(mm); //error where the month is not showing up 
				if (dd < 10){
					dd = "0" + dd;
				}
				if(mm < 10){
					//mm = "0" + mm;
				}
				document.getElementById("day").value = dd;
				document.getElementById("month").value = mm;
			}
		</script>
		
	</head>
	<body onload="putToday()">
	<h1>Personnel Report</h1>
	<p>Select the criteria that you want to view</p>
	<table border>
	<form action='personnel_report.php' method='post' name='dateForm'>
	
	<th></th><th>Month</th><th>Day</th><th>Year</th>
	<td rowspan="3" align="center">Auto Range Selector<br>
	<select id="paydate" name="paydate" onchange="payRange(this.value)">
		<option value="NA">---Pay Period Ending---</option>
		<option value="2014-08-02">02 August 2014</option>
		<option value="2014-08-16">16 August 2014</option>
		<option value="2014-08-30">30 August 2014</option>
		<option value="2014-09-13">13 September 2014</option>
		<option value="2014-09-27">27 September 2014</option>
		<option value="2014-10-11">11 October 2014</option>
		<option value="2014-10-25">25 October 2014</option>
		<option value="2014-11-08">08 November 2014</option>
		<option value="2014-11-22">22 November 2014</option>
		<option value="2014-12-06">06 December 2014</option>
		<option value="2014-12-20">20 December 2014</option>
		<option value="2015-01-03">03 January 2015</option>
	</select>
	</td>
	<tr>
		<td align="center">From</td>
			<div id="date"><td align="center"><select name='Month' id="month" >
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
		
			<td align="center"><select name='Day' id="day" >
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
				<option value='13'>13</option>
				<option value='14'>14</option>
				<option value='15'>15</option>
				<option value='16'>16</option>
				<option value='17'>17</option>
				<option value='18'>18</option>
				<option value='19'>19</option>
				<option value='20'>20</option>
				<option value='21'>21</option>
				<option value='22'>22</option>
				<option value='23'>23</option>
				<option value='24'>24</option>
				<option value='25'>25</option>
				<option value='26'>26</option>
				<option value='27'>27</option>
				<option value='28'>28</option>
				<option value='29'>29</option>
				<option value='30'>30</option>
				<option value='31'>31</option>
			</select>
			</td>
			
			<td align="center"><select name='Year' id="year" >
				<option value='2013'>2013</option>
				<option value='2014'>2014</option>
				<option value='2015'>2015</option>
				<option value='2016'>2016</option>
				<option value='2017'>2017</option>
				<option value='2018'>2018</option>
				<option value='2019'>2019</option>
				<option value='2020'>2020</option>
			</select></td></div>
			
	<tr>
		<td align="center">To</td>
			<div id="date2"><td align="center"><select name='Month2' id="month2" >
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
		
			<td align="center"><select name='Day2' id="day2" >
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
				<option value='13'>13</option>
				<option value='14'>14</option>
				<option value='15'>15</option>
				<option value='16'>16</option>
				<option value='17'>17</option>
				<option value='18'>18</option>
				<option value='19'>19</option>
				<option value='20'>20</option>
				<option value='21'>21</option>
				<option value='22'>22</option>
				<option value='23'>23</option>
				<option value='24'>24</option>
				<option value='25'>25</option>
				<option value='26'>26</option>
				<option value='27'>27</option>
				<option value='28'>28</option>
				<option value='29'>29</option>
				<option value='30'>30</option>
				<option value='31'>31</option>
			</select>
			</td>
			
			<td align="center"><select name='Year2' id="year2" >
				<option value='2013'>2013</option>
				<option value='2014'>2014</option>
				<option value='2015'>2015</option>
				<option value='2016'>2016</option>
				<option value='2017'>2017</option>
				<option value='2018'>2018</option>
				<option value='2019'>2019</option>
				<option value='2020'>2020</option>
			</select></td></div>
			<tr>
				<td align="center">Employee</td>
				<td align="center" colspan="3">
					<select name="employee">
						<option class="emp" value="" id="all">All Employees</option>
						<?php
						$employees = mysqli_query($con,"SELECT * FROM employees ORDER BY Name");
						while($row = mysqli_fetch_array($employees)) {
						  echo "<option value='" . $row['Name'] . "'>" . $row['Name'] . "</option>";
						}?>
					</select>
				</td>
			</tr>
			<tr>
				
				<td align="center">
					Job
				</td>
				<td align="center" colspan="3" align="center">
					<select name="job" id="job">
						<option value="0" id="all">All Jobs</option>
						<?php
							$job = mysqli_query($con,"SELECT * FROM Jobs");
							while($row = mysqli_fetch_array($job)) {
							  echo "<option value='" . $row['Number'] . "'>" . $row['Number'] . " " . $row['Name'] . "</option>";
							}?>
					</select>
				</td>
			</tr>
			
			
				
	<tr>
	<td colspan="2">
		<input type="radio" id="default" name="choice" checked="checked" onclick="destination('personnel_report.php')"><label for="default">Report</label><br>
		<input type="radio" id="payroll" name="choice" onclick="destination('payroll_report.php')"><label for="payroll">Payroll Report</label><br>
		<input type="radio" id="missing" name="choice" onclick="destination('missing.php')"><label for="missing">Missing Person Report</label><br>
		<input type="radio" id="dups" name="choice" onclick="destination('duplicates.php')"><label for="dups">Duplicates<span style="color:red;">BETA</span></label><br>
		<input type="radio" id="tsBox" name="choice" onclick="destination('timesheet.php')"><label for="tsBox">Timesheet View <span style="color:red;">BETA</span></label><br>
		<input type="radio" id="sum" name="choice" onclick="destination('summary.php')"><label for="sum">Summaries <span style="color:red;">BETA</span></label><br>
		<input type="radio" id="exception" name="choice" onclick="destination('exception.php')"><label for="exception">Exception <span style="color:red;">BETA</span></label><br>
	</td>
	<td align="center" colspan="2"><input type='submit' name='submit'></td></tr>
</form>

</table>

</body>
</html>