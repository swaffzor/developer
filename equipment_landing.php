<?
	session_start();
	if($_SESSION['LoggedIn'] != 1){
		echo '<meta http-equiv="refresh" content="0;login.php?sender=index.php">';
		exit();
	}
	include_once 'nav.php';
	require_once 'database.php';
	
	date_default_timezone_set ("America/New_York");
	
	$tmp = mysqli_query($eqcon,"SELECT * FROM Equipment ORDER BY MakeModel");
		while($row = mysqli_fetch_array($tmp)) {
			$makeModel[] = $row['MakeModel'];
			$description[] = $row['Description'];
			$equipmentID[] = $row['EquipmentID'];
			$sqlID[] = $row['ID'];
			$personResponsible[] = $row['Responsible'];
			if($row['Year'] != 0){
				$year[] = $row['Year'];
			}
			else{
				$year[] = "";
			}
		}
		
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	
	echo "<table><tr><td>";
	
	$fromDate = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
	$toDate = $_POST['Year2'] . "-" . $_POST['Month2'] . "-" . $_POST['Day2'];
	$employee = $_POST['employee'];
	
	if($employee != ""){
		$empString = "Submitter = '" . $employee . "'";
	}
	else{
		$empString = "";
	}
	
	if($fromDate == "--"){
		$fromDate = $toDate;
	}
	
	if(isset($_POST['cbDate'])){
		$dateSQL = "Date BETWEEN '$fromDate' AND '$toDate'";
	}
	else{
		$dateSQL = "";
	}
	
	if($_POST['equipment'] != "---Select Equipment---"){
		$eqString = "equipment_id = '".$_POST['equipment']."'";
	}
	else{
		$eqString = "";
	}
	
	if(isset($_POST['employee']) || isset($_POST['equipment'])){
		$sql = "SELECT * FROM EquipmentInspections 
		WHERE  
		".$dateSQL;
		if($dateSQL != "" && $empString != ""){
			$sql.= " AND ";
		}
		$sql.= $empString;
		if(($empString != "" || $dateSQL != "") && $eqString != ""){
			$sql.= " AND ";
		}
		$sql.= $eqString."
		ORDER BY Date, Submitter";
	}
	else{
		$sql = "SELECT * FROM EquipmentInspections ORDER BY Date, Submitter";
	}
	$tmp = mysqli_query($eqcon, $sql);

	echo("<Br>");
	
	
	echo "</td><td>";
	
	//!Links
	while($row = mysqli_fetch_array($tmp)) {
		$links.= "<a href='equipment_report.php?ID=".$row['ID']."'>".$row['Submitted'] ." ". $row['Submitter'] . "</a><BR>";
	}
	
	$_SESSION['links'] = $links;
	
	?>
	<html>
	<head>
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;}
		</style>
		<title>Equipment Inpection Report</title>
		
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
			
			function putToday(){
				var postIsSet = <?
					if(isset($_POST['Month']) ||
					 isset($_POST['Month2']) ||
					 isset($_POST['Day']) ||
					 isset($_POST['Day2']) ||
					 isset($_POST['Year']) ||
					 isset($_POST['Year2'])){
						echo "1";
					}
					else{
						echo "0";
					}
					?>;
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();
				var ddd = document.getElementById("day").value;
				var dmm = document.getElementById("month").value;
				var dyyyy = document.getElementById("year").value;
				if(postIsSet == 0){
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
			}
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
				else{
					while(sender.value != sqlID[index] && index < 10000){
						index++;
					}
					document.getElementById("eqNum").value = tsiID[index];
				}
				
				/* the old way
				for(i=0; i < eq.length; i++){
					if(eq.options[i].value == eqNum.value){
						eq.value = eqNum.value;
					}
				}
				*/
			}
			
			function toggleDate(sender){
				document.getElementById("cbDate").checked = true;
			}
			
			function submitForm(){
				if(document.getElementById("cbDate").checked == false){
					document.getElementById("month").disabled = true;
					document.getElementById("day").disabled = true;
					document.getElementById("year").disabled = true;
					document.getElementById("month2").disabled = true;
					document.getElementById("day2").disabled = true;
					document.getElementById("year2").disabled = true;
				}
				
				document.forms["dateStuff"].submit();
			}
			
		</script>
		
	</head>
	<body onload="putToday()">
		<? //! Date 
		?>
		<?
			echo "Database Code: <span style='color:red;'>$sql</span><br><br>";
			$dateCode = '<form name="dateStuff" action="equipment_landing.php" method="post">
			<table>
			<th></th><th>Month</th><th>Day</th><th>Year</th>
			<tr>
				<td align="center">From</td>
					<div id="date"><td align="center"><select name="Month" id="month" onchange="toggleDate(this)">';
						
						for($i=1; $i<13; $i++){
							$dateCode.= "<option value='";
							if($i < 10){
								$dateCode.= "0";
							}
							$dateCode.= "$i'";
							if($i == $_POST['Month']){
								$dateCode.= " selected";
							}
							$dateCode.= ">";
							if($i < 10){
								$dateCode.= "0";
							}
							$dateCode.= "$i</option>";
						}
						
					$dateCode.='</select></td>
					
			
			
		
			<td align="center"><select name="Day" id="day" onchange="toggleDate(this)">';
				
				for($i=1; $i<32; $i++){
					$dateCode.= "<option value='";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i'";
					if($i == $_POST['Day']){
						$dateCode.= " selected";
					}
					$dateCode.= ">";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i</option>";
				}
				
			$dateCode.='</select>
			</td>
			
			<td align="center"><select name="Year" id="year" onchange="toggleDate(this)" >';
				
				for($i=2015; $i<2021; $i++){
					$dateCode.= "<option value='";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i'";
					if($i == $_POST['Year']){
						$dateCode.= " selected";
					}
					$dateCode.= ">";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i</option>";
				}
				
			$dateCode.='</select></td></div><td rowspan="2"><input type="checkbox" name="cbDate" id="cbDate" ';
				
				if(isset($_POST['cbDate'])){
					$dateCode.= "checked";
				}
				
				$dateCode.= '>Use dates</td>
			
	<tr>
		<td align="center">To</td>
			<div id="date2"><td align="center"><select name="Month2" id="month2" onchange="toggleDate(this)">';
				
				for($i=1; $i<13; $i++){
					$dateCode.= "<option value='";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i'";
					if($i == $_POST['Month2']){
						$dateCode.= " selected";
					}
					$dateCode.= ">";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i</option>";
				}
				
			$dateCode.='</select></td>
		
			<td align="center"><select name="Day2" id="day2" onchange="toggleDate(this)">';
				
				for($i=1; $i<32; $i++){
					$dateCode.= "<option value='";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i'";
					if($i == $_POST['Day2']){
						$dateCode.= " selected";
					}
					$dateCode.= ">";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i</option>";
				}
				
			$dateCode.='</select>
			</td>
			
			<td align="center"><select name="Year2" id="year2" onchange="toggleDate(this)">';
				
				for($i=2015; $i<2021; $i++){
					$dateCode.= "<option value='";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i'";
					if($i == $_POST['Year2']){
						$dateCode.= " selected";
					}
					$dateCode.= ">";
					if($i < 10){
						$dateCode.= "0";
					}
					$dateCode.= "$i</option>";
				}
				
			$dateCode.="</select></td></div>";
			
			echo $dateCode;
			?>
			
			<tr>
				<td align="center">Employee</td>
				<td align="center" colspan="3">
					<select name="employee">
						<option class="emp" value="" id="all">All Employees</option>
						<?php
						
						$employees = mysqli_query($con,"SELECT * FROM employees WHERE recap != '' ORDER BY Name");
						while($row = mysqli_fetch_array($employees)) {
							echo "<option value='" . $row['Name'] . "'";
							if($_POST['employee'] == $row['Name']){
								echo " selected";
							}
							echo ">" . $row['Name'] . "</option>";
						}?>
					</select>
				</td>
			</tr>
			</table>
				<table><tr>
				<td>Equipment</td>
				<td colspan="3">
					<input type="number" pattern="[0-9]*" name="eqNum" id="eqNum" placeholder="Equipment #" size="10px" onchange="selectEQ(this)" value="<?php echo $_POST['eqNum']; echo $_GET['eqNum']; ?>"></td><td>
			
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
				</td>
			</tr>
	</form>
		<tr><td align="center" colspan="2"><input type='button' id='button' name='button' onclick="submitForm()" value="Submit"></td></tr>
	</table>
	</td></tr></table>
	
	<?	
		echo "<a href='equipment_landing.php'><button>Reset Links</button></a><br>";
		echo $links;
	?>
	
</body>
</html>
