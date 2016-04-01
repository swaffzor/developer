<html>
	<?php
		
		date_default_timezone_set ("America/New_York");
		include("database.php");
		include("functions.php");
		include("nav.php");
		
		//! Backend
			if(isset($_POST['firstname'])){
				/*
					echo "<pre>";
					print_r($_POST);
					echo "</pre>";
				*/
				
				$illegals = array("'",'"',"\n");
				$replacements = array("&#39", "&#34", "<br>");
				
				foreach($_POST as $key=>$value){
					$_POST[$key] = str_replace($illegals, $replacements, $value);
				}
				
				/**
				* Employee
				*/
				class TSIemployee {
				
					public $name;
					public $status;
					public $company;
					public $recap;
					public $email;
					public $exempt;
					public $daysMissing;
					public $reportsTo;
					public $firstName;
					public $sqlId;
				
				}
				
				$newb = new TSIemployee();
				
				$newb->name = ucfirst(trim($_POST['firstname'])) . " " . ucfirst(trim($_POST['lastname']));
				$newb->firstName = ucfirst(trim($_POST['firstname']));
				$newb->status = ucfirst(trim($_POST['status']));
				if(strtoupper(trim($_POST['company'])) == "TSI"){
					$newb->company = "TSI";
				}
				else{
					$newb->company = $_POST['company'];
				}
				foreach($_POST as $key => $value){
					if($key == "exempt"){
						if($value == "on"){
							$newb->exempt = "exempt";
						}
						else{
							$newb->exempt = "";
						}
					}
					if($key == "recap"){
						if($value == "on"){
							$newb->recap = "yes";
						}
						else{
							$newb->recap = "";
						}
					}
				}
				$newb->email = trim($_POST['email']);
				$newb->daysMissing = trim($_POST['daysmissing']);
				$newb->reportsTo = trim($_POST['reportingto']);
				$newb->sqlId = $_POST['id'];
				
				
				echo "<pre>";
				print_r($newb);
				echo "</pre>";
				
				
				if($_POST['action'] == "add"){
					$sql = "INSERT INTO employees (
						Name, 
						Status, 
						Company, 
						recap,
						email,
						exempt,
						daysMissing,
						ReportingTo,
						Firstname
					)
					VALUES(
						'".$newb->name."', 
						'".$newb->status."', 
						'".$newb->company."', 
						'".$newb->recap."', 
						'".$newb->email."',
						'".$newb->exempt."',
						'".$newb->daysMissing."',
						'".$newb->reportsTo."',
						'".$newb->firstName."')";
				}
				else if($_POST['action'] == "edit"){
					$sql = "UPDATE employees SET 
						Name = '".$newb->name."',
						Status = '".$newb->status."', 
						Company = '".$newb->company."',
						recap = '".$newb->recap."',
						email = '".$newb->email."',
						exempt = '".$newb->exempt."',
						daysMissing = '".$newb->daysMissing."',
						ReportingTo = '".$newb->reportsTo."',
						Firstname = '".$newb->firstName."'
						WHERE id='".$newb->sqlId."'";
				}
				if(mysqli_query($con, $sql)){
					echo "<h2>Entered into database successfully</h2>";
				}
				else{
					echo "<h2>There was a problem entering into database</h2>";
				}
			}
		
		
		$eCount = 0;
		$database_results = mysqli_query($con,"SELECT * FROM employees GROUP BY Name");
		while($row = mysqli_fetch_array($database_results)) {
			$name[] = $row['Name'];
			$status[] = $row['Status'];
			$recap[] = $row['recap'];
			$email[] = $row['email'];
			$firstName[] = $row['Firstname'];
			$company[] = $row['Company'];
			$exempt[] = $row['exempt'];
			$daysMissing[] = $row['daysMissing'];
			$reportingTo[] = $row['ReportingTo'];
			$id[] = $row['id'];
			$eCount++;
		}
		
		//echo "<pre>";
		//print_r($name);
		//echo "</pre>";
		
		
	?>
			
		
	<head>
		<style>
			table{
				table-layout: fixed;
			}
			td { 
				width: 33%; 
			}
			th { 
				width: 33%; 
			}
		</style>
		
		<script type='text/javascript'>
			
			var eName = [<?
				for($i=0; $i<sizeof($name); $i++){
					echo "'$name[$i]',";
				}
			?>];
			var eStatus = [<?
				for($i=0; $i<sizeof($status); $i++){
					echo "'$status[$i]',";
				}
			?>];
			var eRecap = [<?
				for($i=0; $i<sizeof($recap); $i++){
					echo "'$recap[$i]',";
				}
			?>];
			var eEmail = [<?
				for($i=0; $i<sizeof($email); $i++){
					echo "'$email[$i]',";
				}
			?>];
			var eFirstname = [<?
				for($i=0; $i<sizeof($firstName); $i++){
					echo "'$firstName[$i]',";
				}
			?>];
			var eCompany = [<?
				for($i=0; $i<sizeof($company); $i++){
					echo "'$company[$i]',";
				}
			?>];
			var eExempt = [<?
				for($i=0; $i<sizeof($exempt); $i++){
					echo "'$exempt[$i]',";
				}
			?>];
			var eDaysmissing = [<?
				for($i=0; $i<sizeof($daysMissing); $i++){
					echo "'$daysMissing[$i]',";
				}
			?>];
			var eReportingto = [<?
				for($i=0; $i<sizeof($reportingTo); $i++){
					echo "'$reportingTo[$i]',";
				}
			?>];
			var eID = [<?
				for($i=0; $i<sizeof($id); $i++){
					echo "'$id[$i]',";
				}
			?>];
			
			function Populate(id){
				var lastname = eName[id];
				lastname = lastname.split(" ");
				
				document.getElementById("edit").checked = true;
				document.getElementById("button").value = "Edit Employee";
				document.getElementById("lastname").value = lastname[1];
				document.getElementById("email").value = eEmail[id];
				document.getElementById("status").value = eStatus[id];
				document.getElementById("company").value = eCompany[id];
				document.getElementById("daysmissing").value = eDaysmissing[id];
				document.getElementById("reportingto").value = eReportingto[id];
				document.getElementById("id").value = eID[id];
				
				if(eFirstname[id] != ""){
					document.getElementById("firstname").value = eFirstname[id];
				}
				else{
					document.getElementById("firstname").value = lastname[0];
				}
				
				var temp = eExempt[id].toUpperCase();
				if(temp == "EXEMPT"){
					document.getElementById("exempt").checked = true;
				}
				else{
					document.getElementById("exempt").checked = false;					
				}
				temp = eRecap[id].toUpperCase();
				if(temp == "YES"){
					document.getElementById("recap").checked = true;
				}
				else{
					document.getElementById("recap").checked = false;					
				}
			}
						function ChangeAction(sender){
				if(sender.value == "add"){
					document.getElementById("employee_form").reset();
					document.getElementById(sender.id).checked = true;
					document.getElementById("nameDrop").value = "";
					document.getElementById("firstname").focus();
					document.getElementById("button").value = "Add Employee";
				}
				else{
					document.getElementById("button").value = "Edit Employee";
				}
			}
			
			function Explain(sender){
				var explanation;
				if(sender.name == "status"){
					explanation = "<ul><li>Must be 'Active' to be in the drop down.</li></ul>";
				}
				else if(sender.name == "reportingto"){
					explanation = "<ul><li>Put email address of who you want a copy of the recap receipt to go to.</li><li>Leave blank to send to nobody.</li></ul>";
				}
				else if(sender.name == "company"){
					explanation = "<ul><li>Enter TSI if they are a regular TSI employee.</li><li>Enter name of company if a sub or other.</li></ul>";
				}
				else if(sender.name == "daysmissing"){
					explanation = "<ul><li>This is the number of consecutive days that they have not had hours reported for</li>";
					explanation = explanation + "<li>After 31, they are considered not to be working and the status is set to 'Expired'</li>";
					explanation = explanation + "<li>Leave blank for a new employee</li></ul>";
				}
				else if(sender.name == "exempt"){
					explanation = "<ul><li>Check this box if the employee is salaried.</li></ul>";
				}
				else if(sender.name == "recap"){
					explanation = "<ul><li>Check this box if this employee is supposed to enter a daily recap.</li>";
					explanation = explanation + "<li>If this is checked, then they will get a reminder email and their name will be in the supervisor drop-down on the recap page.</li>";
					explanation = explanation + "<li>If this is checked, then an email address for the employee must be entered</li></ul>";
				}
				else{
					explanation = "";
				}
				
				document.getElementById("explain").innerHTML = explanation;
			}
			
			function validate(){
				var validationPassed = true;
				var errorMessage;
				var theID;
				
				document.getElementById("button").disabled = true;
				
				if(document.getElementById("firstname").value == ""){
					validationPassed = false;
					errorMessage = "Please enter the first name."
					theID = "firstname";
				}
				if(document.getElementById("lastname").value == ""){
					validationPassed = false;
					errorMessage = "Please enter the last name."
					theID = "lastname";
				}
				if(document.getElementById("status").value == ""){
					validationPassed = false;
					errorMessage = "Please enter the status."
					theID = "status";
				}
				if(document.getElementById("company").value == ""){
					validationPassed = false;
					errorMessage = "Please enter the company."
					theID = "company";
				}
				if(document.getElementById("recap").checked == true){
					if(document.getElementById("email").value == ""){
						validationPassed = false;
						errorMessage = "Please enter the email address."
						theID = "email";
					}
				}
				
				if(validationPassed == false){
					document.getElementById(theID).focus();
					alert(errorMessage);
					document.getElementById("button").disabled = false;
					return false;
				}
				else{
					document.getElementById("button").disabled = false;
					document.getElementById("id").disabled = false;
					document.forms["employee_form"].submit();
				}
				
				
			}
			
		</script>
	</head>
	<body>
		<?
			echo "<select id='nameDrop' onchange=Populate(this.value)>";
			for($i=0;$i<$eCount;$i++){
				echo "<option value='".$i."'>" . $name[$i] . "</option>";
			}
			echo "</select>";	
		?>
		
		<form name="employee_form" id="employee_form" action="employees.php" method="post">
		<input type="text" name="id" id="id" disabled="true" size="5"><br>
		<input type="radio" name="action" id="add" value="add" onchange="ChangeAction(this)" checked="true"><label for="add"> Add employee</label><br>
		<input type="radio" name="action" id="edit" value="edit" onchange="ChangeAction(this)"><label for="edit">Edit employee</label><BR>
		<table>
			<th>First Name</th><th>Last Name</th><th>Status</th>
		<tr><td><input type="text" name="firstname" id="firstname" onfocus="Explain(this)"></td>
		<td><input type="text" name="lastname" id="lastname" onfocus="Explain(this)"></td>
		<td><input type="text" name="status" id="status" onfocus="Explain(this)"></td></tr></table>
		<table>
		<th>Company</th><th>Exempt</th><th>Days missing</th>
		<tr><td><input type="text" name="company" id="company" onfocus="Explain(this)"></td>
		<td align="center"><input type="checkbox" name="exempt" id="exempt" onclick="Explain(this)"></td>
		<td><input type="text" name="daysmissing" id="daysmissing" onfocus="Explain(this)"></td></tr></table>
		<table>
		<th>Require Recap</th><th>email</th><th>Send Recap copy to</th>
		<tr><td align="center"><input type="checkbox" name="recap" id="recap" onclick="Explain(this)"></td>
		<td><input type="text" name="email" id="email" onfocus="Explain(this)"></td>
		<td><input type="text" name="reportingto" id="reportingto" onfocus="Explain(this)"></td></tr></table>
		
		<p id="explain"></p><br>
		
		<p id="description"></p><br>
		
		<input type="button" id="button" name="button" onclick="validate()" value="Add Employee"></button>
		
		</form>
		
		
		
	</body>
</html>