<? include("database.php");
	
	?>
<html>
	<head>
		<title>Equipment Inspection Checklist</title>
		
		<script type="text/javascript">

			function checkChecks(){
				
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
			
			function showHide(sender) {
				if(document.getElementById(sender.name + "poor").checked || document.getElementById(sender.name + "help").checked) {
					//document.getElementById(div_id).style.display = 'block';
					document.getElementById(sender.name + "CommentR").style.display = "block";
					document.getElementById(sender.name + "Comment").required = true;
				}
				else {
					//document.getElementById(div_id).style.display='none';
					document.getElementById(sender.name + "CommentR").style.display = "none";
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
			
			/*function validate(){
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
					if (i<5){
						if(document.getElementById("hoursm" + i).value > 24){
							message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
							document.getElementById("hoursm" + i).focus();	
							success = 0;
						}
					}
				}
				
				for (i=1; i<=FIELD_COUNT; i++) {
					if(document.getElementById("employee" + i).value != "---Select Employee---" && document.getElementById("hours" + i).value == ""){
						message = "Please enter the number of hours for " + document.getElementById("employee" + i).value + " in the space next to his name.";
						document.getElementById("hours" + i).focus();
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
				    return str.replace(/\w\Sg, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
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
				//}
			}*/
			
			
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
		
		<form action="equipmentinspection.php" name="inspectionForm" method="post" enctype="multipart/form-data">
		
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
			<option value='2021'>2021</option>
			<option value='2022'>2022</option>
			<option value='2023'>2023</option>
			<option value='2024'>2024</option>
			<option value='2025'>2025</option>
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
			
			<select onchange="insertEmail(this.value)" id="nameDrop" name="nameDrop" style="display: inline">
				<option>---Received Equipment From---</option>
				<option>Rental Company</option>
				<?php
					$tmp = mysqli_query($con,"SELECT Name FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "<option value ='" . $row['Name'] . "'>" . $row['Name'];
					}
				?>
				<option>N/A</option>
			</select><br />
			
			<input type="number" pattern="[0-9]*" name="eqNum" id="eqNum" placeholder="Equipment #" size="10px" onchange="selectEQ(this)">
			
			<select name="equipment" id="equipment">
				<option>---Select Equipment---</option>
				<option value="100">Menzi Muck #100</option>
				<option value="101">Takehuchi mini excavator #101</option>
				<option value="102">TEST</option>
			</select><br />
			
			<input type="number" name="milesHours" id="milesHours" placeholder="miles/hours"><br>
			
			
			<table border>
				<th>Item</th><th>Description</th><th>Good</th><th>Fair</th><th>Poor</th><th>Needs Immediate Repair</th><th>N/A</th><th>Comments</th>
				
				<?
					$outside = array("Lights",
						"Steps/Hand Rails",
						"Tires/Tracks",
						"Exhaust",
						"Fenders",
						"Bucket",
						"Cutting Edge/Teeth",
						"Lifting Mechanism",
						"Hoses",
						"Fittings Greased",
						"Hitch/Coupler",
						"Wipers");
					
					$engine = array("Battery Cable",
						"Fan Belt",
						"Hoses",
						"Air Filter",
						"Guards");
					
					$inside_cab = array("Brakes",
						"Backup Alarm",
						"Fire Extinguisher",
						"Gauges",
						"Horn",
						"Hydraulic Controls",
						"Glass",
						"Mirror",
						"Roll Over Protection",
						"Seat Belt",
						"Steering");
						
					$fluids = array("Visible Leaks",
						"Oil Level/Pressure",
						"Coolant Level",
						"Hydraulic Oil Level",
						"Transmission Fluid Level",
						"Fuel Level");
					
					$items = array("Steering" => "Over 3&#34; free play, check tie rod ends",
						"Clutch" => "Proper adjustement 3/4&#34; free travel",
						"Brakes" => "foot and hand. Must hold firm",
						"Gauges" => "All gauges must be working",
						"Horn" => "In working order",
						"Backup Alarm" => "In working order",
						"Mirrors" => "In working order",
						"Wipers" => "In working order",
						"Seat Belts" => "Satisfactory",
						"Fire extinguisher" => "Present/Charged/Inspected",
						"Cooling" => "Check radiator and hoses",
						"Engine" => "Check for knocks and leaks",
						"Belts" => "Check for wear/cracks",
						"Oil" => "Full and clean",
						"Hydraulic" => "Check lines/pump for wear/cracks/leaks",
						"Electrical" => "Generator and starter working",
						"Battery" => "Check for corrosion on terminals",
						"Transmission" => "Check for leaks",
						"Differential" => "Check for leaks",
						"Springs/shocks" => "Check hangers",
						"Frame" => "Check for cracks and bent",
						"Lubrication" => "Check for dry fittings",
						"Tires, wheels, lug bolts" => "Depth of tread and cuts",
						"Lights" => "Must be working",
						"Glass" => "Report all cracks/shattered",
						"Body" => "Report dents",
						"Exhaust" => "Check for leaks",
						"Fuel" => "Check for leaks",
						"Lights" => " ",
						"Steps/Hand Rails" => " ",
						"Tires/Tracks" => " ",
						"Exhaust" => " ",
						"Fenders" => " ",
						"Bucket" => " ",
						"Cutting Edge/Teeth" => " ",
						"Lifting Mechanism" => " ",
						"Hoses" => " ",
						"Fittings Greased" => " ",
						"Hitch/Coupler" => " ",
						"Wipers" => " ",
						"Battery Cable" => " ",
						"Fan Belt" => " ",
						"Hoses" => " ",
						"Air Filter" => " ",
						"Guards" => " ",
						"Brakes" => " ",
						"Backup Alarm" => " ",
						"Fire Extinguisher" => " ",
						"Gauges" => " ",
						"Horn" => " ",
						"Hydraulic Controls" => " ",
						"Glass" => " ",
						"Mirror" => " ",
						"Roll Over Protection" => " ",
						"Seat Belt" => " ",
						"Steering" => " ",
						"Visible Leaks" => " ",
						"Oil Level/Pressure" => " ",
						"Coolant Level" => " ",
						"Hydraulic Oil Level" => " ",
						"Transmission Fluid Level" => " ",
						"Fuel Level" => " "
						);
					
					
					foreach($items as $key => $value){
						echo "<tr><td>$key</td><td>$value</td>\n";
						echo "<td align='center'><input type='radio' name='$key' id='".$key."good'  value='good' onchange='showHide(this)'></td>\n";
						echo "<td align='center'><input type='radio' name='$key' id='".$key."fair'  value='fair' onchange='showHide(this)'></td>\n";
						echo "<td align='center'><input type='radio' name='$key' id='".$key."poor'  value='poor' onchange='showHide(this)'></td>\n";
						echo "<td align='center'><input type='radio' name='$key' id='".$key."help'  value='help' onchange='showHide(this)'></td>\n";
						echo "<td align='center'><input type='radio' name='$key' id='".$key."na'    value='na'   onchange='showHide(this)'></td>\n";
						echo "<td align='center'><input type='text' name='".$key."Comment' id='".$key."Comment' placeholder='Comments'></td>\n";
						echo "<td class='commentRequired' id='".$key."CommentR'>Comment Required</td></tr>\n\n";
						$count++;
					}
					
					/*
					echo "<tr><td colspan='8'>Outside</td></tr>";
					for($i=0; $i<sizeof($outside); $i++){
						echo "<tr><td>$outside[$i]</td></tr>";
					}
					
					echo "<tr><td colspan='8'>Engine</td></tr>";
					for($i=0; $i<sizeof($engine); $i++){
						echo "<tr><td>$engine[$i]</td></tr>";
					}
					
					echo "<tr><td colspan='8'>Inside Cab</td></tr>";
					for($i=0; $i<sizeof($inside_cab); $i++){
						echo "<tr><td>$inside_cab[$i]</td></tr>";
					}
					
					echo "<tr><td colspan='8'>Fluids</td></tr>";
					for($i=0; $i<sizeof($fluids); $i++){
						echo "<tr><td>$fluids[$i]</td></tr>";
					}
					*/ 
				?>
			
			</table>
			
			<input type="submit">
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