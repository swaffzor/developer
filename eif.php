<? 
	include("database.php");
	include_once("functions.php");
	
	date_default_timezone_set ("America/New_York");
	$now = date("F j, Y @ g:i a");
	session_start();
	if($_SESSION['LoggedIn'] != 1){
		echo '<meta http-equiv="refresh" content="0;login.php?sender='.$URL.'">';
		exit();
	}
	
	
	$tmp = mysqli_query($eqcon,"SELECT * FROM Equipment ORDER BY MakeModel");
		while($row = mysqli_fetch_array($tmp)) {
			$makeModel[] = $row['MakeModel'];
			$description[] = $row['Description'];
			$equipmentID[] = $row['EquipmentID'];
			$sqlID[] = $row['ID'];
			$personResponsible[] = $row['Responsible'];
			// don't want a bunch of 0's in the drop down
			if($row['Year'] != 0){
				$year[] = $row['Year'];
			}
			else{
				$year[] = "";
			}
			$vin[] = $row['VIN'];
			$mileshours[] = $row['MilesHours'];
		}
		
		include("nav2.php"); 
	?>
	
<html>
	<head>
		<title>Equipment Inspection Form</title>
		
		<script type="text/javascript">
			
			var make_model = [<?
				for($i=0; $i<sizeof($makeModel); $i++){
					echo "'$makeModel[$i]',";
				}
			?>];
			
			var sqlID = [<?
				for($i=0; $i<sizeof($sqlID); $i++){
					echo "'$sqlID[$i]',";
				}
			?>];
			
			var tsiID = [<?
				for($i=0; $i<sizeof($equipmentID); $i++){
					echo "'$equipmentID[$i]',";
				}
			?>];
			var description = [<?
				for($i=0; $i<sizeof($description); $i++){
					echo "'$description[$i]',";
				}
			?>];
			
			var year = [<?
				for($i=0; $i<sizeof($year); $i++){
					echo "'$year[$i]',";
				}
			?>];
			
			var VIN = [<?
				for($i=0; $i<sizeof($vin); $i++){
					echo "'$vin[$i]',";
				}
			?>];
			
			var mileshours = [<?
				for($i=0; $i<sizeof($mileshours); $i++){
					echo "'$mileshours[$i]',";
				}
			?>];

			
			
			function insertEmail(senderName){
				var names = [
					<?
					$count=0;
					$tmp = mysqli_query($con,"SELECT * FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "'".$row['Name'] . "',";
						$empNames[] = $row['Name'];
						$count++;
					}
					?>
				''];
				var emails = [
					<?
					$tmp = mysqli_query($con,"SELECT * FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "'".$row['email'] . "',";
						$empEmails[] = $row['email'];
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
				theStatus = document.getElementById(sender.id).value;
				if(theStatus == "service due" || theStatus == "runs yet unreliable" || theStatus == "needs repair" || theStatus == "does not run" || theStatus == "poor" || theStatus == "deficient" || theStatus == "not safe"){
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
				var cbNoTransfer = document.getElementById("noTransfer");				
				
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
				else if(!cbNoTransfer.checked && document.getElementById("tofromName").value == "---Select Employee---"){
					message = "Select the person you are receiving/sending equipment from/to";
					document.getElementById("tofromName").focus();	
					success = 0;
				}
				
				else if(document.getElementById("eqNum").value == "" || document.getElementById("eqNum".value == 0)){
					message = "Enter the equipment number";
					document.getElementById("eqNum").focus();	
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
				if (document.getElementById("use SD").checked == true || 
				document.getElementById("use RYU").checked == true || 
				document.getElementById("use NR").checked == true || 
				document.getElementById("use DNR").checked == true){
					if(document.getElementById("usability_comments").value == ""){
						message = "Please provide a comment on the usability of the equipment.";
						document.getElementById("usability_comments").focus();
						success = 0;
					}
				}
				
				//condition comment
				if (document.getElementById("con_P").checked == true || 
				document.getElementById("con_D").checked == true){
					if(document.getElementById("condition_comments").value == ""){
						message = "Please provide a comment on the condition of the equipment.";
						document.getElementById("condition_comments").focus();
						success = 0;
					}
				}
				
				//safety comment
				if (document.getElementById("safe_no").checked == true){
					if(document.getElementById("safety_comments").value == ""){
						message = "Please provide a comment on the safety of the equipment.";
						document.getElementById("safety_comments").focus();
						success = 0;
					}
				}
				
				radios = document.getElementsByName("condition");
			    formValid = false;
			
			    i = 0;
			    while (!formValid && i < radios.length) {
			        if (radios[i].checked) formValid = true;
			        i++;        
			    }
			
			    if (!formValid){
				    message = "Must check a condition option";
				    success = 0;
			    }
				
				radios = document.getElementsByName("safety");
			    formValid = false;
			
			    i = 0;
			    while (!formValid && i < radios.length) {
			        if (radios[i].checked) formValid = true;
			        i++;        
			    }
			
			    if (!formValid){
				    message = "Must check a safety option";
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
				var eqNum = document.getElementById("eqNum");
				var index = 0;
				
				//get the sqlID
				//loop to count which index it is
				//this index is the tsiID index
				//make the selection
				
				if (sender.type == "number"){
					//coming from the number text input, get the index 
					while(sender.value != tsiID[index] && index < 10000){
						index++;
					}
					document.getElementById("equipment").value = sqlID[index];
				}
				// sender is from the drop down box
				else{
					while(sender.value != sqlID[index] && index < 10000){
						index++;
					}
					//don't put in a 0 if there is a number to avoid overwriting actual number
					if(tsiID[index] != 0){
						document.getElementById("eqNum").value = tsiID[index];
					}
				}
				
				document.getElementById("info_makemodel").innerHTML = make_model[index];
				document.getElementById("info_description").innerHTML = description[index];
				document.getElementById("info_year").innerHTML = year[index];
				document.getElementById("info_vin").innerHTML = VIN[index];
				document.getElementById("info_mileshours").innerHTML = mileshours[index];
				document.getElementById("info_tsiid").innerHTML = tsiID[index];
				
				/* the old way
				for(i=0; i < eq.length; i++){
					if(eq.options[i].value == eqNum.value){
						eq.value = eqNum.value;
					}
				}
				*/
			}
			
			function disableTransfer(){
				var theState;
				
				if(document.getElementById("noTransfer").checked){
					theState = true;
					document.getElementById("tofromDiv").style.color = "gray";
				}
				else{
					theState = false;
					document.getElementById("tofromDiv").style.color = "black";
				}
				document.getElementById("send/receive").disabled = theState;
				document.getElementById("tofromName").disabled = theState;
			}
			
		</script>
				
		<link rel="stylesheet" href="mystyle.css">
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
			.disabled{
				color: gray;
			}
		</style>
		
	</head>
	<body onload="putToDay();">

		<? 
			
			//! BACK END
			
			/* if 'Name Not Listed' is checked then empName is what was manually entered */
			if(isset($_POST['noList']) == "on"){
				$name = $_POST['name'];
			}
			else{
				$name = $_POST['nameDrop'];
			}
			
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			*/
			if(isset($_POST['eqNum'])){
				$post_items = str_replace(" ", "_", $_POST);
				$illegals = array("'",'"',"\n");
				$replacements = array("&#39", "&#34", "<br>");
				
				$illegalsHTML = array("'",'"');
				$replacementsHTML = array("&#39", "&#34");
				
				$usability_comments = str_replace($illegals, $replacements, $_POST['usability_comments']);
				$condition_comments = str_replace($illegals, $replacements, $_POST['condition_comments']);
				$safety_comments = str_replace($illegals, $replacements, $_POST['safety_comments']);
				$additional_comments = str_replace($illegals, $replacements, $_POST['comments']);
				
				$dataTable.= "<h1>Confirmation of your equipment inspection form is shown below</h1>";
				
				$dataTable.= "<table>";
				$dataTable.= "<tr><td>Name:</td><td>$name</td></tr>";
				$dataTable.= "<tr><td>Submitted:</td><td>$now</td></tr>";
				foreach($post_items as $key => $value){
					if($key == "equipment"){
						$i=0;
						while($_POST['equipment'] != $sqlID[$i] && $i < 10000){
							$i++;
						}
						$eqIndex = $i;
						$dataTable.= "<tr><td>$key:</td><td>". $makeModel[$i] ." ". $description[$i] ."</td></tr>";
					}
					else if($key == "name"|| $key == "nameDrop"){
						//do nothing
					}
					else if(isset($_POST[$key])){
						$dataTable.= "<tr><td>$key:</td><td>". $_POST[$key]."</td></tr>";
					}
				}
				$dataTable.= "</table>";
				
				
					echo $dataTable;
				
				
				$message.= $dataTable;
				
				$rawHTML.= "<table>
				<th>Month</th><th>Day</th><th>Year</th><th></th>
				<tr><td><select>
					<option>".$_POST['Month']."</option>
				</select></td>
			
				<td><select>
					<option>".$_POST['Day']."</option>
				</select>
				</td>
				
				<td colspan='1'><select>
					<option>".$_POST['Year']."</option>
				</select></td></tr>
				</table>
			
			
				<select>
					<option>".$name."</option>
				</select>
				
				<input type='email' value='".$_POST['email']."' disabled='true'>
				<hr>
				Enter TSI Equipment ID or Select Equipment from list<br> 
				<input type='number' value='".$_POST['eqNum']."' disabled='true'>
				
				<select name='equipment' id='equipment' onchange='selectEQ(this)'>";
				$i=0;
				while($_POST['equipment'] != $sqlID[$i] && $i<10000){
					$i++;
				}
				
				$rawHTML.="<option>".$makeModel[$i] ." ". $description[$i]."</option>
				</select><br /><hr>
	
				I am 
				<select name='send/receive'>
					<option>".$_POST['send/receive']."</option>
				</select>
					this equipment <span id='to/from'>to/from</span><br>
					
				<select id='tofromName' name='tofromName' style='display: inline'>
					<option>".$_POST['tofromName']."</option>
				</select> with<br />
				
				<input type='number' value='".$_POST['milesHours']."' disabled='true'> miles/hours<br>
				<hr>
				
				<table ><tr><td colspan='2' align='left'>Usability:</td></tr>
				<tr><td>
					<input type='radio' name='usability' id='use FO' value='fully operational' disabled='true' ";
					if($_POST['usability'] == 'fully operational'){ 
						$rawHTML.= "checked";
					} 
					$rawHTML.= "><label for='use FO'>Fully Operational</label><br>
					<input type='radio' name='usability' id='use RC' value='runs consistently' disabled='true' ";
					if($_POST['usability'] == 'runs consistently'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='use RC'>Runs Consistently<br>
					<input type='radio' name='usability' id='use SD' value='service due' disabled='true' ";
					if($_POST['usability'] == 'service due'){
						$rawHTML.= "checked";
					}
					$rawHTML.= "><label for='use SD'>Service Due<br></label>
					<input type='radio' name='usability' id='use RYU' value='runs yet unreliable' disabled='true' ";
					if($_POST['usability'] == 'runs yet unreliable'){
						$rawHTML.= 'checked';
					}
					$rawHTML.="><label for='use RYU'>Runs Yet Unreliable<br></label>
					<input type='radio' name='usability' id='use NR' value='needs repair' disabled='true' ";
					if($_POST['usability'] == 'needs repair'){
						$rawHTML.= 'checked';
					}
					$rawHTML.="><label for='use NR'>Needs Repair<br></label>
					<input type='radio' name='usability' id='use DNR' value='does not run' disabled='true' ";
					if($_POST['usability'] == 'does not run'){
						$rawHTML.= 'checked';
					}
					$rawHTML.="><label for='use DNR'>DOES NOT RUN<br></label>
				</td>
				<td>
					<textarea name='usability_comments' id='usability_comments' rows='10' cols='50' disabled='true'>". $usability_comments."</textarea>
					<span id='usability_required' style='color: red; display: none'>Comment required</span>
				</td></tr>
			
				<tr><td colspan='2'><hr></td></tr>
				
				<tr><td colspan='2' align='left'>General Condition:</td></tr>
				<tr><td>
					<input type='radio' name='condition' id='con_N' value='new/like new' disabled='true' ";
					if($_POST['condition'] == 'new/like new'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='con_N'>New/Like New</label><br>
					<input type='radio' name='condition' id='con_G' value='good' disabled='true' ";
					if($_POST['condition'] == 'good'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='con_G'>Good</label><br>
					<input type='radio' name='condition' id='con_F' value='fair' disabled='true' ";
					if($_POST['condition'] == 'fair'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='con_F'>Fair</label><br>
					<input type='radio' name='condition' id='con_P' value='poor' disabled='true' ";
					if($_POST['condition'] == 'poor'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='con_P'>Poor</label><br>
					<input type='radio' name='condition' id='con_D' value='deficient' disabled='true' ";
					if($_POST['condition'] == 'deficient'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='con_D'>Deficient</label><br>
				</td>
				<td>
					<textarea name='condition_comments' id='condition_comments' rows='10' cols='50' disabled='true'>".$condition_comments."</textarea>
					<span id='condition_required' style='color: red; display: none'>Comment required</span>
				</td></tr>
				
				<tr><td colspan='2'><hr></td></tr>
				
				<tr><td colspan='2' align='left'>Safety:</td></tr>
				<tr><td>
					<input type='radio' name='safety' id='safe_yes' value='safe' disabled='true' ";
					if($_POST['safety'] == 'safe'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='safe_yes'>Equipment Is Safe</label><br>
					<input type='radio' name='safety' id='safe_no' value='not safe' disabled='true' ";
					if($_POST['safety'] == 'not safe'){
						$rawHTML.= 'checked';
					}
					$rawHTML.= "><label for='safe_no'>Equipment Is NOT Safe</label>
				</td>
				<td>
					<textarea name='safety_comments' id='safety_comments' rows='10' cols='50' disabled='true'>".$safety_comments."</textarea>
					<span id='safety_required' style='color: red; display: none'>Comment Required</span>
				</td></tr>
				</table>
				<br><hr>
				
				Additional Comments:<br>
				<textarea name='comments' rows='10' cols='50' disabled='true'>". $additional_comments."</textarea>
				
				<br><hr>";
				
				$message .= $rawHTML;
				
				
				if($_POST['tofromName'] != "Rental Company" && $_POST['tofromName'] != "N/A"){
					$i=0;
					while($_POST['tofromName'] != $empNames[$i] && $i<1000){
						$i++;
					}
					$emailOtherPerson = $empEmails[$i];
				}
				
				$emailTo = $_POST['email'];
				
				
				$rawHTML = str_replace($illegalsHTML, $replacementsHTML, $rawHTML);
			
				//!Database/email
				
				
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				$headers .= "From: robot@tsidisaster.net\r\n" . "Bcc: jeremy@tsidisaster.com, christina@tsidisaster.com, robert@tsidisaster.com";
				$message = wordwrap($message, 70, "\r\n");
				if(mail($emailTo, "Equipment Inspection Confirmation", $message, $headers)){
					echo "<h2>An email has been successfully sent</h2>";
				}
				else{
					echo "<h2>Something went wrong, please take a screenshot and send it in.</h2>";
				}
				
				$sql = "INSERT INTO EquipmentInspections (
					Submitter, 
					Submitted, 
					Date, 
					email,
					equipment_number,
					equipment_id,
					send_receive,
					tofrom_name,
					miles_hours,
					usability,
					usability_comment,
					condition_comment,
					safety,
					safety_comments,
					comments,
					ccondition,
					rawHTML
				) VALUES (
					'$name', 
					'$now', 
					'".$_POST['Year']."-".$_POST['Month']."-".$_POST['Day']."',
					'".$_POST['email']."',
					'".$_POST['eqNum']."',
					'".$_POST['equipment']."',
					'".$_POST['send/receive']."',
					'".$_POST['tofromName']."',
					'".$_POST['milesHours']."',
					'".$_POST['usability']."',
					'".$usability_comments."',
					'".$condition_comments."',
					'".$_POST['safety']."',
					'".$safety_comments."',
					'".$additional_comments."',
					'".$_POST['condition']."',
					'$rawHTML'
				)";
				
				if(mysqli_query($eqcon, $sql)){
					echo "<h2>Entered into database successfully</h2>";
				}
				else{
					echo "<h2>Something went wrong with the database</h2>";
					echo mysqli_error($eqcon) . "<br>" . mysqli_errno($eqcon);
					echo "<br>$sql<br>";
				}
				
				$sql = "UPDATE Equipment SET MilesHours= '".$_POST['milesHours']."' WHERE ID= '".$_POST['equipment']."'";
				
				if(!mysqli_query($eqcon, $sql)){
					echo "<h2>Could not update the miles/hours in the database, please take a screenshot and send to <a href='sms:1-407-463-8518'>Jeremy</a></h2>";
				}
				
				if($equipmentID[$eqIndex] == 0 && $_POST['eqNum'] != ""){
					mysqli_query($eqcon, "UPDATE Equipment SET EquipmentID= '".$_POST['eqNum']."' WHERE ID= '".$_POST['equipment']."'");
					echo "<h2>updated equipment number</h2>";
					mail("jeremy@tsidisaster.com", "Equipment Number Update", $name . " has updated sql ID of ".$_POST['equipment'] ." number to ".$_POST['eqNum']);
				}
				
				if(isset($_POST['noTransfer'])){
					mysqli_query($eqcon, "UPDATE Equipment SET Responsible= '".$name."' WHERE ID= '".$_POST['equipment']."'");
				}
				
				if($_POST['send/receive'] == "sending" && ($_POST['tofromName'] != "N/A" && $_POST['tofromName'] != "Rental Company" && $_POST['tofromName'] != "storage")){
					$tmp = mysqli_query($eqcon,"SELECT ID FROM EquipmentInspections ORDER BY ID DESC LIMIT 1 ");
						while($row = mysqli_fetch_array($tmp)) {
							$inspectionID = $row['ID'];
						}
						
					$sql = "INSERT INTO Reminders (
						Name,
						email,
						Submitted,
						equipmentID,
						inspectionID,
						complete
					) VALUES (
						'".$_POST['tofromName']."',
						'$emailOtherPerson',
						'$now',
						'".$sqlID[$eqIndex]."',
						'$inspectionID',
						'0'
					)";
					
					mysqli_query($eqcon, $sql);
					
					
					$message2 = "$name has reported that ".$makeModel[$eqIndex] ." ". $description[$eqIndex]." is being assigned to you.<br><br>Please visit <a href='http://tsidisaster.net/developer/eif.php?equipment=".$_POST['equipment']."&eqNum=".$_POST['eqNum']."&sendreceive=receiving&tofromName=".$name."'>this page</a> to fill out an equipment inspection form for it within 48 hours.";
					
					$headers2 = "MIME-Version: 1.0" . "\r\n";
					$headers2 .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
					$headers2 .= "From: robot@tsidisaster.net\r\n" . "Bcc: jeremy@tsidisaster.com";
					if(mail($emailOtherPerson, "Equipment Inspection Alert", $message2, $headers2)){
						echo "<h3>A Notifcation email has been successfully sent to ".$_POST['tofromName']."</h3>";
					}
					else{
						echo "<h3>Something went wrong, please take a screenshot and send it in.</h3>";
					}
					
					//make reponsible
					$sql = "UPDATE Equipment SET Responsible= '".$_POST['tofromName']."', MilesHours= '".$_POST['milesHours']."' WHERE ID= '".$_POST['equipment']."'";
					
					if(mysqli_query($eqcon, $sql)){
						echo "<h2>Responsibility for this equipment has been transfered to " . $_POST['tofromName'] . "</h2>";
					}
				}
				else if($_POST['send/receive'] == "sending" && $_POST['tofromName'] == "storage"){
					$sql = "UPDATE Equipment SET Responsible= '".$_POST['tofromName']."', MilesHours= '".$_POST['milesHours']."' WHERE ID= '".$_POST['equipment']."'";
					
					if(mysqli_query($eqcon, $sql)){
						echo "<h2>This equipment is being stored</h2>";
					}
				}
			}
		
			
		?>
		
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
			
			<input type="text" placeholder="Name" name="name" id="name" onchange="nameFix()" required value="<?php echo $_POST['name'] ?>" style="display: none;">
			<input type="email" name="email" id="email" placeholder="email" disabled="true" required value="<?php echo $_COOKIE['email'] ?>">
			
			<input type="checkbox" id="noList" name="noList" onchange="showHideName()" value="<?php echo $_POST['noList'] ?>"><label for="noList">Name Not Listed</label><br />
			
			
			<hr>
			Enter TSI Equipment ID or Select Equipment from list<br> 
			<input type="number" pattern="[0-9]*" name="eqNum" id="eqNum" placeholder="Equipment #" size="10px" onchange="selectEQ(this)" value="<?php echo $_POST['eqNum']; echo $_GET['eqNum']; ?>">
			
			<select name="equipment" id="equipment" onchange="selectEQ(this)">
				<option>---Select Equipment---</option>
				<? //! Equipment selection
					for($i=0;$i<sizeof($makeModel);$i++){
						echo "<option value ='" . $sqlID[$i] ."'";
						if($sqlID[$i] == $_POST['equipment'] || $sqlID[$i] == $_GET['equipment']){
							echo " selected";
						}
						echo ">" . $makeModel[$i] ." ". $description[$i]." ".$year[$i] ."\n";
					}
				?>
			</select>
			<br />
			
			<! equipment information>
			<table>
				<tr>
					<td>
						Make/Model:
					</td>
					<td id="info_makemodel">
						N/A
					</td>
				</tr>
				<tr>
					<td>
						Description:
					</td>
					<td id="info_description">
						N/A
					</td>
				</tr>
				<tr>
					<td>
						Year:
					</td>
					<td id="info_year">
						N/A
					</td>
				</tr>
				<tr>
					<td>
						VIN:
					</td>
					<td id="info_vin">
						N/A
					</td>
				</tr>
				<tr>
					<td>
						Miles/Hours:
					</td>
					<td id="info_mileshours">
						N/A
					</td>
				</tr>
				<tr>
					<td>
						TSI ID:
					</td>
					<td id="info_tsiid">
						N/A
					</td>
				</tr>
			</table>
			<hr>
			
			<table><tr><td>
				<div id="tofromDiv">
			I am 
			<select name="send/receive" id="send/receive">
				<option value="sending" <? if($_POST['send/receive'] == "sending" || $_GET['sendreceive'] == "sending"){echo "selected";} ?>>sending</option>
				<option value="receiving" <? if($_POST['send/receive'] == "receiving" || $_GET['sendreceive'] == "receiving"){echo "selected";} ?>>receiving</option>
			</select>
				this equipment <span id="to/from">to/from</span></div><br>
				
			<select id="tofromName" name="tofromName" style="display: inline">
				<option>---Select Employee---</option>
				<option value="storage">Store at Yard</option>
				<?php
					$tmp = mysqli_query($con,"SELECT Name FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "<option value ='" . $row['Name'] . "'";
						if($_POST['tofromName'] == $row['Name'] || $_GET['tofromName'] == $row['Name']){
							echo " selected";
						}
						echo ">" . $row['Name'];
					}
				?>
				<option>N/A</option>
			</select> <br />
			</td><td>
				<input type="checkbox" name="noTransfer" id="noTransfer" onchange="disableTransfer()"><label for="noTransfer">No transfer/Weekly Inspection</label>
			</td></tr></table>
			
			<input type="number" name="milesHours" id="milesHours" placeholder="miles/hours" value="<?php echo $_POST['milesHours'] ?>"> hours/miles<br>
			<hr>
			
			<table ><tr><td colspan="2" align="left">Usability:</td></tr>
			<tr><td>
				<input type="radio" name="usability" id="use FO" value="fully operational" onchange='showHide(this)' <? if($_POST['usability'] == "fully operational"){echo "checked";} ?>>	<label for="use FO">Fully Operational</label><br>
				<input type="radio" name="usability" id="use RC" value="runs consistently" onchange='showHide(this)' <? if($_POST['usability'] == "runs consistently"){echo "checked";} ?>>			<label for="use RC">Runs Consistently<br>
				<input type="radio" name="usability" id="use SD" value="service due" onchange='showHide(this)' <? if($_POST['usability'] == "service due"){echo "checked";} ?>>				<label for="use SD">Service Due<br></label>
				<input type="radio" name="usability" id="use RYU" value="runs yet unreliable" onchange='showHide(this)' <? if($_POST['usability'] == "runs yet unreliable"){echo "checked";} ?>>		<label for="use RYU">Runs Yet Unreliable<br></label>
				<input type="radio" name="usability" id="use NR" value="needs repair" onchange='showHide(this)' <? if($_POST['usability'] == "needs repair"){echo "checked";} ?>>				<label for="use NR">Needs Repair<br></label>
				<input type="radio" name="usability" id="use DNR" value="does not run" onchange='showHide(this)' <? if($_POST['usability'] == "does not run"){echo "checked";} ?>>				<label for="use DNR">DOES NOT RUN<br></label>
			</td>
			<td>
				<textarea name="usability_comments" id="usability_comments" rows="10" cols="50" placeholder="Use this box to disclose any functionality issues or immediate concerns regarding usability of equipment."><? echo $_POST['usability_comments']; ?></textarea>
				<span id="usability_required" style="color: red; display: none">Comment required</span>
			</td></tr>
			
			<tr><td colspan="2"><hr></td></tr>
			
			<tr><td colspan="2" align="left">General Condition:</td></tr>
			<tr><td>
				<input type="radio" name="condition" id="con_N" value="new/like new" onchange='showHide(this)' <? if($_POST['condition'] == "new/like new"){echo "checked";} ?>>		<label for="con_N">New/Like New</label><br>
				<input type="radio" name="condition" id="con_G" value="good" onchange='showHide(this)' <? if($_POST['condition'] == "good"){echo "checked";} ?>>				<label for="con_G">Good</label><br>
				<input type="radio" name="condition" id="con_F" value="fair" onchange='showHide(this)' <? if($_POST['condition'] == "fiar"){echo "checked";} ?>>				<label for="con_F">Fair</label><br>
				<input type="radio" name="condition" id="con_P" value="poor" onchange='showHide(this)' <? if($_POST['condition'] == "poor"){echo "checked";} ?>>				<label for="con_P">Poor</label><br>
				<input type="radio" name="condition" id="con_D" value="deficient" onchange='showHide(this)' <? if($_POST['condition'] == "deficient"){echo "checked";} ?>>		<label for="con_D">Deficient</label><br>
			</td>
			<td>
				<textarea name="condition_comments" id="condition_comments" rows="10" cols="50" placeholder="Use this box to disclose any condition issues"><? echo $_POST['condition_comments']; ?></textarea>
				<span id="condition_required" style="color: red; display: none">Comment required</span>
			</td></tr>
			
			<tr><td colspan="2"><hr></td></tr>
			
			<tr><td colspan="2" align="left">Safety:</td></tr>
			<tr><td>
				<input type="radio" name="safety" id="safe_yes" value="safe" onchange='showHide(this)' <? if($_POST['safety'] == "safe"){echo "checked";} ?>><label for="safe_yes">Equipment Is Safe</label><br>
				<input type="radio" name="safety" id="safe_no" value="not safe" onchange='showHide(this)'> <? if($_POST['safety'] == "not safe"){echo "checked";} ?><label for="safe_no">Equipment Is NOT Safe</label>
			</td>
			<td>
				<textarea name="safety_comments" id="safety_comments" rows="10" cols="50" placeholder="Please provide any details that are necessary about this equipment regarding safety."><? echo $_POST['safety_comments']; ?></textarea>
				<span id="safety_required" style="color: red; display: none">Comment Required</span>
			</td></tr>
			</table>
			<br><hr>
			
			Additional Comments:<br>
			<textarea name="comments" rows="10" cols="50"><? echo $_POST['comments']; ?></textarea>
			
			<br><hr>
			<input type="button" id="theButton" name="theButton" onclick="validate()" value="Submit">
		</form>
		
		
	</body>
</html>