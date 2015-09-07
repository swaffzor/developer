<html>
	<head>
		<style>
			td { 
				width: 16.5%; 
			}
			
			.tahead{
				background-color: #77c5fc;
			}
			
			.different{
				background-color: #ecff61;
			}
			.tableOdd{
				table-layout: fixed; 
				background-color: #73c4ff;
			}
			.tableEven{
				table-layout: fixed; 
				background-color: #8fff73;
			}
		</style>
	</head>
	<body>

<?php
	
	require_once 'database.php';
	
	Class equipmentList{
		Public $eqID = array();
		Public $makemodel = array();
		Public $description = array();
		public $year = array();
		public $vin = array();
		public $ID = array();
	}
	
	$newList = new equipmentList();
	$oldList = new equipmentList();
	
	$equp = mysqli_query($eqcon,"SELECT * FROM EquipmentUpdate ORDER BY EquipmentID");
	while($row = mysqli_fetch_array($equp)) {
		$newList->eqID[] = $row['EquipmentID'];
		$newList->makemodel[] = $row['MakeModel'];
		$newList->description[] = $row['Description'];
		$newList->year[] = $row['Year'];
		$newList->vin[] = $row['VIN'];
		$newList->ID[] = $row['ID'];
	}
	
	$temp = mysqli_query($eqcon,"SELECT * FROM Equipment ORDER BY EquipmentID");
	while($row = mysqli_fetch_array($temp)) {
		$oldList->eqID[] = $row['EquipmentID'];
		$oldList->makemodel[] = $row['MakeModel'];
		$oldList->description[] = $row['Description'];
		$oldList->year[] = $row['Year'];
		$oldList->vin[] = $row['VIN'];
		$oldList->ID[] = $row['ID'];
	}
	
	echo "<form action='update_equipment_list.php' name='changeForm' method='post' enctype='multipart/form-data'><table>";
	
	$colorCounter = 0;
	for($a=0; $a<sizeof($newList->makemodel); $a++){
		for($b=0; $b<sizeof($oldList->makemodel); $b++){
			//TSI ID is the same, but differing info
			if($newList->eqID[$a] != 0){	
				if($newList->eqID[$a] == $oldList->eqID[$b]){
					if($newList->makemodel[$a] != $oldList->makemodel[$b] || $newList->description[$a] != $oldList->description[$b] || $newList->year[$a] != $oldList->year[$b] || $newList->vin[$a] != $oldList->vin[$b]){
						
						//table header
						echo "<table border class='";
						if($colorCounter%2 == 0){
							echo "tableEven";
						}
						else{
							echo "tableOdd";
						}
						$colorCounter++;
						echo "'><thead><tr><td colspan='3' align='center'>".$oldList->eqID[$b];
						
						//change checkbox
						echo "</td><td ><input type='checkbox' id='cb".$oldList->ID[$b]."' name='cb".$oldList->ID[$b]."'";
						$theID = "cb".$oldList->ID[$b];
						if(isset($_POST[$theID])){
							echo " checked='".$_POST[$theID]."'";
						}
						echo "><label for='cb".$oldList->ID[$b];
						echo "'>change</label></td></tr></thead>";
						
						//table body
						echo "<tbody><tr><th>Type</th><th>Current</th><th>New</th></tr></tbody>";
						
						//table foot (actual data in this case)
						if($newList->makemodel[$a] != $oldList->makemodel[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tfoot><tr $different><td>make/model</td><td>".$oldList->makemodel[$b]."</td><td>".$newList->makemodel[$a]."</td></tr>";
						
						if($newList->description[$a] != $oldList->description[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tr $different><td>description</td><td>".$oldList->description[$b]."</td><td>".$newList->description[$a]."</td></tr>";
						
						if($newList->year[$a] != $oldList->year[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tr $different><td>year</td><td>".$oldList->year[$b]."</td><td>".$newList->year[$a]."</td></tr>";
						
						if($newList->vin[$a] != $oldList->vin[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tr $different><td>VIN</td><td>".$oldList->vin[$b]."</td><td>".$newList->vin[$a]."</td></tr></tfoot></table>";
						
						
						//! backend
						if(isset($_POST[$theID])){
							if($newList->makemodel[$a] != $oldList->makemodel[$b]){
								$sqlMakemodel = " MakeModel='".$newList->makemodel[$a]."'";
								$p1 = true;
							}
							else{
								$sqlMakemodel = "";
								$p1 = false;
							}
							if($newList->description[$a] != $oldList->description[$b]){
								if($p1){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlDescription = "$comma Description='".$newList->description[$a]."'";
								$p2 = true;
							}
							else{
								$sqlDescription = "";
								$p2 = false;
							}
							if($newList->year[$a] != $oldList->year[$b]){
								if($p1 == true || $p2 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlYear = "$comma Year='".$newList->year[$a]."'";
								$p3 = true;
							}
							else{
								$sqlYear = "";
								$p3 = false;
							}
							if($newList->vin[$a] != $oldList->vin[$b]){
								if($p1 == true || $p2 == true || $p3 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlVin = "$comma VIN='".$newList->vin[$a]."'";
							}
							else{
								$sqlVin = "";
							}
							
							$sql = "UPDATE Equipment SET $sqlMakemodel $sqlDescription $sqlVin $sqlYear WHERE ID= '".$oldList->ID[$b]."'";
							if(mysqli_query($eqcon, $sql)){
								echo "<h2>Updated info</h2>";
							}
							else{
								echo "<h1>Update failure</h1>";
							}
						}
					}
				}
				
				//new TSI ID, same VIN
				else if($newList->vin[$a] == $oldList->vin[$b] && $newList->vin[$a] != "" && $oldList->vin[$b] != ""){
					//look up TSI ID to make sure number is not already taken
					$numberInUse = false;
					$test = mysqli_query($eqcon,"SELECT * FROM Equipment WHERE EquipmentID = '".$newList->eqID[$a]."' LIMIT 1");
					while($row = mysqli_fetch_array($test)) {
						echo "TSI ID: " . $newList->eqID[$a]." IS IN USE!<br>";
						$numberInUse = true;
					}
					if($numberInUse == false){
						//table header
						echo "<table border class='";
						if($colorCounter%2 == 0){
							echo "tableEven";
						}
						else{
							echo "tableOdd";
						}
						$colorCounter++;
						echo "'><thead><tr><td colspan='3' align='center'>".$newList->eqID[$a];
						
						//change checkbox
						echo "</td><td ><input type='checkbox' id='cb".$oldList->ID[$b]."' name='cb".$oldList->ID[$b]."'";
						$theID = "cb".$oldList->ID[$b];
						if(isset($_POST[$theID])){
							echo " checked='".$_POST[$theID]."'";
						}
						echo "><label for='cb".$oldList->ID[$b];
						echo "'>change</label></td></tr></thead>";
						
						//table body
						echo "<tbody><tr><th>Type</th><th>Current</th><th>New</th></tr></tbody>";
						
						//table foot (actual data in this case)
						if($newList->eqID[$a] != $oldList->eqID[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tfoot><tr $different><td>TSI ID</td><td>".$oldList->eqID[$b]."</td><td>".$newList->eqID[$a]."</td></tr>";
						
						if($newList->makemodel[$a] != $oldList->makemodel[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tfoot><tr $different><td>make/model</td><td>".$oldList->makemodel[$b]."</td><td>".$newList->makemodel[$a]."</td></tr>";
						
						if($newList->description[$a] != $oldList->description[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tr $different><td>description</td><td>".$oldList->description[$b]."</td><td>".$newList->description[$a]."</td></tr>";
						
						if($newList->year[$a] != $oldList->year[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tr $different><td>year</td><td>".$oldList->year[$b]."</td><td>".$newList->year[$a]."</td></tr>";
						
						if($newList->vin[$a] != $oldList->vin[$b]){
							$different = "class='different'";
						}
						else{
							$different = "";
						}
						echo "<tr $different><td>VIN</td><td>".$oldList->vin[$b]."</td><td>".$newList->vin[$a]."</td></tr></tfoot></table>";
						
						//! backend 2
						if(isset($_POST[$theID])){
							if($newList->makemodel[$a] != $oldList->makemodel[$b]){
								$sqlMakemodel = " MakeModel='".$newList->makemodel[$a]."'";
								$p1 = true;
							}
							else{
								$sqlMakemodel = "";
								$p1 = false;
							}
							if($newList->description[$a] != $oldList->description[$b]){
								if($p1){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlDescription = "$comma Description='".$newList->description[$a]."'";
								$p2 = true;
							}
							else{
								$sqlDescription = "";
								$p2 = false;
							}
							if($newList->year[$a] != $oldList->year[$b]){
								if($p1 == true || $p2 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlYear = "$comma Year='".$newList->year[$a]."'";
								$p3 = true;
							}
							else{
								$sqlYear = "";
								$p3 = false;
							}
							if($newList->vin[$a] != $oldList->vin[$b]){
								if($p1 == true || $p2 == true || $p3 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlVin = "$comma VIN='".$newList->vin[$a]."'";
								$p4 = true;
							}
							else{
								$sqlVin = "";
								$p4 = false;
							}
							if($newList->eqID[$a] != $oldList->eqID[$b]){
								if($p1 == true || $p2 == true || $p3 == true || $p4 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlEqid = "$comma EquipmentID='".$newList->eqID[$a]."'";
							}
							else{
								$sqlEqid = "";
							}
							
							$sql = "UPDATE Equipment SET $sqlMakemodel $sqlDescription $sqlVin $sqlYear $sqlEqid WHERE ID= '".$oldList->ID[$b]."'";
							if(mysqli_query($eqcon, $sql)){
								echo "<h2>Updated info</h2>";
							}
							else{
								echo "<h1>Update failure</h1>";
							}
						}
					}
				}
			}
		}
	}
	
	echo "<input type='submit'></form";
	
	
	
	
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	
	
	
?>

	</body>
</html>