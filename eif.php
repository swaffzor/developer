<? include("database.php");
	
	?>
	
<html>
	<head>
		<title>Equipment Inspection Checklist</title>
		
		<script type="text/javascript">

			function checkChecks(){
				
			}
			function submitForm(){
				
				
				
				document.getElementById("email").disabled = false;
				document.forms["inspectionForm"].submit();
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
					document.getElementById("name").style.position = 'relative';
					document.getElementById("email").disabled = false;
				}
				else{
					document.getElementById("nameDrop").style.display = 'inline';
					document.getElementById("name").style.display = 'none';	
					document.getElementById("email").disabled = true;				
				}
			}
			
			function showHide(sender) {
				theStatus = document.getElementById(sender.id).value;
				if(theStatus == "service due" || theStatus == "runs yet unreliable" || theStatus == "needs repair" || theStatus == "does not run" || theStatus == "poor" || theStatus == "deficient"){
					document.getElementById(sender.name + "_required").style.display = 'block';
					//document.getElementById("usability_required").className = "show";
					document.getElementById(sender.name + "_required").required = true;
				}
				else {
					document.getElementById(sender.name + "_required").style.display ='none';
					//document.getElementById("usability_required").className = "hide";
					document.getElementById(sender.name + "_required").required = false;
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
				else if(document.getElementById("fromName").value == "---Select Employee---"){
					message = "Select the person you are receiving/sending equipment from/to";
					document.getElementById("fromName").focus();	
					success = 0;
				}
				else if(document.getElementById("equipment").value == "---Select Equipment---"){
					message = "Select the equipment";
					document.getElementById("equipment").focus();	
					success = 0;	
				}
				else if(document.getElementById("milesHours").value == ""){
					message = "Please enter the hours or miles for this equipment";
					document.getElementById("milesHours").focus();	
					success = 0;
				}
				
				var radios = document.getElementsByName("usability");
			    var formValid = false;
			
			    var i = 0;
			    while (!formValid && i < radios.length) {
			        if (radios[i].checked) formValid = true;
			        i++;        
			    }
			
			    if (!formValid){
				    message = "Must check usability option";
				    success = 0;
			    }
				
				//usability comment
				
				//! YO THIS AINT WORKING RIGHT
				/*var theStatus = document.getElementByName("usability").value;
				if(theStatus == "service due" || theStatus == "runs yet unreliable" || theStatus == "needs repair" || theStatus == "does not run"){
					if(getElementById("usability_required").value == ""){
						message = "Please provide a comment on the usability of the equipment.";
						document.getElementById("usability_required").focus();
						success = 0;
					}
				}
				
				//condition comment
				theStatus = document.getElementsByName("condition").value;
				if(theStatus == "poor" || theStatus == "deficient"){
					if(getElementById("condition_required").value == ""){
						message = "Please provide a comment on the condition of the equipment.";
						document.getElementById("condition_required").focus();
						success = 0;
					}
				}
				*/
				radios = document.getElementsByName("condition");
			    formValid = false;
			
			    i = 0;
			    while (!formValid && i < radios.length) {
			        if (radios[i].checked) formValid = true;
			        i++;        
			    }
			
			    if (!formValid){
				    message = "Must check condition option";
				    success = 0;
			    }
				
				
				
								
				//if validation fails, show the message, return false and enable the button for retry
				if(success == 0){
					alert(message);
					//document.getElementById("upload").disabled = false;
					document.getElementById("theButton").disabled = false;
					return false;
				}
				else{
					//submit the form
					document.getElementById("email").disabled = false;
					document.forms["inspectionForm"].submit();
				}
				
				//document.getElementById("upload").disabled = false;
				//document.getElementById("theButton").disabled = true;
				//document.getElementById("theButton").style.position='absolute;';	//hide on success to reduce duplicates
				//document.getElementById("theButton").style.left = '-9999px';
							
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
			
			//when equipment number enetered is a value of equipment, auto-fill fields
			function selectEQ(sender){
				
				var eq = document.getElementById("equipment");
				for(i=0; i < eq.length; i++){
					if(eq.options[i].value == sender.value){
						eq.value = sender.value;
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
			
			.show{
				position: relative;
				left: 0px;
			}
			.commentRequired{
				color: red;
				display: none;
			}
		</style>
		
	</head>
	<body onload="checkChecks(); putToDay();">

		<? include("nav2.html"); 
			echo "<pre>";
			print_r($_COOKIE);
			
			$name = $_SESSION["first_name"];
			echo $name;
			
		?>
		</pre>
		
		<form action="eif.php" name="inspectionForm" method="post" enctype="multipart/form-data">
		
		<?//! Date Dropdown ?>
		<table>
			<th>Month</th><th>Day</th><th>Year</th><th></th>
		<tr><td><select name='Month' id="Month" onchange="dateCheck(); showStyle()">
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
	
		<td><select name='Day' id="Day" onchange="dateCheck(); showStyle()">
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
		
		<td colspan="1"><select name='Year' id="Year" onchange="dateCheck(); showStyle()">
			<option value='2014'>2014</option>
			<option value='2015'>2015</option>
			<option value='2016'>2016</option>
			<option value='2017'>2017</option>
			<option value='2018'>2018</option>
			<option value='2019'>2019</option>
			<option value='2020'>2020</option>
		</select></td></tr>
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
			
			<input type="checkbox" id="noList" name="noList" onchange="showHideName()"><label for="noList">Name Not Listed</label><br />
			
			
			<hr>
			Enter TSI Equipment ID or Select Equipment from list<br> 
			<input type="number" pattern="[0-9]*" name="eqNum" id="eqNum" placeholder="Equipment #" size="10px" onchange="selectEQ(this)">
			
			<select name="equipment" id="equipment">
				<option>---Select Equipment---</option>
				<?
					$tmp = mysqli_query($con,"SELECT * FROM Equipment ORDER BY MakeModel");
					while($row = mysqli_fetch_array($tmp)) {
						echo "<option value ='" . $row['MakeModel'] . "'>" . $row['MakeModel'] ." ". $row['Description'];
					}
				?>
			</select><br /><hr>
			
			I am 
			<select name="send/receive">
				<option value="sending">sending</option>
				<option value="receiving">receiving</option>
			</select>
				this equipment <span id="to/from">to/from</span><br>
				
			<select id="fromName" name="fromName" style="display: inline">
				<option>---Select Employee---</option>
				<option>Rental Company</option>
				<?php
					$tmp = mysqli_query($con,"SELECT Name FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "<option value ='" . $row['Name'] . "'>" . $row['Name'];
					}
				?>
				<option>N/A</option>
			</select><br />
			
			<input type="number" name="milesHours" id="milesHours" placeholder="miles/hours"><br>
			<hr>
			
			<table ><tr><td colspan="2" align="left">Usability:</td></tr>
			<tr><td><input type="radio" name="usability" id="use FO" value="fully operational" onchange='showHide(this)'>	<label for="use FO">Fully Operational</label><br>
			<input type="radio" name="usability" id="use RC" value="runs consistently" onchange='showHide(this)'>			<label for="use RC">Runs Consistently<Br>
			<input type="radio" name="usability" id="use SD" value="service due" onchange='showHide(this)'>				<label for="use SD">Service Due<br></label>
			<input type="radio" name="usability" id="use RYU" value="runs yet unreliable" onchange='showHide(this)'>		<label for="use RYU">Runs Yet Unreliable<br></label>
			<input type="radio" name="usability" id="use NR" value="needs repair" onchange='showHide(this)'>				<label for="use NR">Needs Repair<br></label>
			<input type="radio" name="usability" id="use DNR" value="does not run" onchange='showHide(this)'>				<label for="use DNR">DOES NOT RUN<br></label>
			</td>
			<td>
				<textarea name="usability_comments" rows="10" cols="50" placeholder="Use this box to disclose any functionality issues or immediate concerns regarding usability of equipment."></textarea>
				<span id="usability_required" style="color: red; display: none">Comment required</span>
			</td></tr><tr><td colspan="2"><hr></td></tr>
			<hr>
			
			<tr><td colspan="2" align="left">General Condition:</td></tr>
			<tr><td><input type="radio" name="condition" id="con_N" value="new/like new" onchange='showHide(this)'>	<label for="con_N">New/Like New</label><br>
			<input type="radio" name="condition" id="con_G" value="good" onchange='showHide(this)'>				<label for="con_G">Good</label><br>
			<input type="radio" name="condition" id="con_F" value="fair" onchange='showHide(this)'>				<label for="con_F">Fair</label><br>
			<input type="radio" name="condition" id="con_P" value="poor" onchange='showHide(this)'>				<label for="con_P">Poor</label><br>
			<input type="radio" name="condition" id="con_D" value="deficient" onchange='showHide(this)'>		<label for="con_D">Deficient</label><br>
			</td>
			<td>
				<textarea name="condition_comments" id="condition_comments" rows="10" cols="50" placeholder="Use this box to disclose any "></textarea>
			<span id="condition_required" style="color: red; display: none">Comment required</span>
			</td></tr>
			</table>
			<br><hr>
			
			Additional Comments:<Br>
			<textarea name="comments" rows="10" cols="50"></textarea>
			
			<br><hr>
			<input type="button" id="theButton" name="theButton" onclick="validate()" value="Submit">
		</form>
		
		<? 
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			
			$post_items = str_replace(" ", "_", $_POST);
			
			foreach($post_items as $key => $value){
				if(isset($_POST[$key])){
					echo "$key: ". $_POST[$key]."<BR>";
				}
			}
		?>
	</body>
</html>